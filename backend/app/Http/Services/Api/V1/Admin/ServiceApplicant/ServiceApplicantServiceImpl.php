<?php

namespace App\Http\Services\Api\V1\Admin\ServiceApplicant;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\ServiceApplicantResource;
use App\Http\Services\Api\V1\Admin\Disaster\DisasterService;
use App\Http\Services\Api\V1\Admin\Education\EducationService;
use App\Http\Services\Api\V1\Admin\Financial\FinancialService;
use App\Http\Services\Api\V1\Admin\Job\JobService;
use App\Http\Services\Api\V1\Admin\LegalAid\LegalAidService;
use App\Http\Services\Api\V1\Admin\Medical\MedicalService;
use App\Models\DisasterReliefDetail;
use App\Models\EducationSupportDetails;
use App\Models\FinancialAidDetail;
use App\Models\JobSkillRegistration;
use App\Models\LegalAidDetails;
use App\Models\MedicalAssistanceDetail;
use App\Models\ServiceApplicant;
use App\Models\ServiceApplicantStatus;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ServiceApplicantServiceImpl implements ServiceApplicantService
{
    protected MedicalService $medicalService;
    protected FinancialService $financialService;
    protected EducationService $educationService;
    protected JobService $jobService;
    protected LegalAidService $legalAidService;
    protected DisasterService $disasterService;

    public function __construct(
        MedicalService $medicalService,
        FinancialService $financialService,
        EducationService $educationService,
        JobService $jobService,
        LegalAidService $legalAidService,
        DisasterService $disasterService
    )
    {
        $this->medicalService = $medicalService;
        $this->financialService = $financialService;
        $this->educationService = $educationService;
        $this->jobService = $jobService;
        $this->legalAidService = $legalAidService;
        $this->disasterService = $disasterService;
    }

    public function index(Request $request)
    {
        $service_application = ServiceApplicant::with(
                        'user', 
                        'serviceCategories', 
                        'ward',
                        'union',
                        'thana',
                        'district',
                        'division',
                        'attachments',
                        'statuses',
                        'medicalDetails',
                        'financialDetails',
                        'educationDetails',
                        'jobDetails',
                        'legalAidDetails',
                        'disasterDetails'
                    );

         // Sorting (secure)
        $sortableColumns = ['id', 'request_number', 'subject', 'status', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $service_application->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $service_application->where('request_number', 'like', "%{$search}%")
                ->orWhere('subject', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $service_applications = $service_application->paginate($itemsPerPage);

        return ServiceApplicantResource::collection($service_applications);
    }

    public function getAllServiceApplicants()
    {
         $service_application = ServiceApplicant::with(
                        'user', 
                        'serviceCategories', 
                        'ward',
                        'union',
                        'thana',
                        'district',
                        'division',
                        'attachments',
                        'statuses',
                        'medicalDetails',
                        'financialDetails',
                        'educationDetails',
                        'jobDetails',
                        'legalAidDetails',
                        'disasterDetails'
                    )
                    ->latest()
                    ->get();

        return ServiceApplicantResource::collection($service_application);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            // 1 Create Service Request
            $applicant = ServiceApplicant::create([
                'user_id'           => $request->user_id ?? Auth::id(),
                'service_category_id'=> $request->service_category_id,
                'ward_id'           => $request->ward_id,
                'union_id'          => $request->union_id,
                'thana_id'          => $request->thana_id,
                'district_id'       => $request->district_id,
                'division_id'       => $request->division_id,
                'request_number'    => $request->request_number,
                'priority'          => $request->priority ?? 'medium',
                'status'            => 'pending',
                'subject'           => $request->subject,
                'description'       => $request->description,
                'requested_amount'  => $request->requested_amount,
                'approved_amount'   => $request->approved_amount,
                'assigned_to'       => $request->assigned_to,
                'remarks'           => $request->remarks,
                'rejection_reason'  => $request->rejection_reason,
                'completion_notes'  => $request->completion_notes,
                'submitted_at'      => now(),
            ]);


            // 2 Get category name
            $category = ServiceCategory::find($request->service_category_id);

            if (!$category) {
                throw new \Exception("Service category not found.");
            }

            $categoryName = $category->name_en; // 'Medical Assistance', 'Education Assistance', etc.

            // 3 Name-based switch
            switch ($categoryName) {

                case 'Medical':
                    $medicalDetails = $request->medical_details ?? [];
                    $this->medicalService->store($medicalDetails, $applicant->id);
                    break;

                case 'Financial':
                    $financial = $request->financial_details ?? [];
                    $this->financialService->store($financial, $applicant->id);

                case 'Education':
                    $education = $request->education_details ?? [];
                    $this->educationService->store($education, $applicant->id);
                    break;

                case 'Job':
                    $job = $request->job_details ?? [];
                    $this->jobService->store($job, $applicant->id);
                    break;

                case 'Legal':
                    $legal = $request->legal_details ?? [];
                    $this->legalAidService->store($legal, $applicant->id);
                    break;

                case 'Disaster':
                    $disaster = $request->disaster_details ?? [];
                    $this->disasterService->store($disaster, $applicant->id);
                    break;

                default:
                    throw new \Exception("Service category not implemented.");
            }
            

            // 4 Attachments (optional, multiple)
           if ($request->hasFile('attachments')) {
                // Ensure attachments is array
                $files = $request->file('attachments');
                if (!is_array($files)) {
                    $files = [$files];
                }

                $uploadedPaths = ImageUpload::uploadMultipleImagesApplicationStorage(
                    $files,
                    'service_requests',
                    800,
                    600,
                    'jpg',
                    90
                );

                foreach ($uploadedPaths as $path) {
                    $applicant->attachments()->create([
                        'file_name'   => basename($path),
                        'file_path'   => $path,
                        'uploaded_by' => Auth::id(),
                        'uploaded_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return new ServiceApplicantResource($applicant);
            
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $service_application = ServiceApplicant::with(
                        'user', 
                        'serviceCategories', 
                        'ward',
                        'union',
                        'thana',
                        'district',
                        'division',
                        'attachments',
                        'statuses',
                        'medicalDetails',
                        'financialDetails',
                        'educationDetails',
                        'jobDetails',
                        'legalAidDetails',
                        'disasterDetails'
                    )
                    ->findOrFail($id);

        return new ServiceApplicantResource($service_application);
    }

    public function update(Request $request, int $id)
    {
         DB::beginTransaction();

        try {
            //1️Find existing ServiceApplicant
            $applicant = ServiceApplicant::findOrFail($id);

            // 2 Update main fields
            $applicant->update([
                'user_id'            => $request->user_id ?? $applicant->user_id,
                'service_category_id'=> $request->service_category_id ?? $applicant->service_category_id,
                'ward_id'            => $request->ward_id ?? $applicant->ward_id,
                'union_id'           => $request->union_id ?? $applicant->union_id,
                'thana_id'           => $request->thana_id ?? $applicant->thana_id,
                'district_id'        => $request->district_id ?? $applicant->district_id,
                'division_id'        => $request->division_id ?? $applicant->division_id,
                'request_number'     => $request->request_number ?? $applicant->request_number,
                'priority'           => $request->priority ?? $applicant->priority,
                'status'             => $request->status ?? $applicant->status,
                'subject'            => $request->subject ?? $applicant->subject,
                'description'        => $request->description ?? $applicant->description,
                'requested_amount'   => $request->requested_amount ?? $applicant->requested_amount,
                'approved_amount'    => $request->approved_amount ?? $applicant->approved_amount,
                'assigned_to'        => $request->assigned_to ?? $applicant->assigned_to,
                'remarks'            => $request->remarks ?? $applicant->remarks,
                'rejection_reason'   => $request->rejection_reason ?? $applicant->rejection_reason,
                'completion_notes'   => $request->completion_notes ?? $applicant->completion_notes,
            ]);

            // 3️Get category name
            $category = ServiceCategory::find($applicant->service_category_id);
            if (!$category) {
                throw new \Exception("Service category not found.");
            }
            $categoryName = $category->name_en;

            // 4 Update category-specific details
            switch ($categoryName) {
                case 'Medical':
                    $medicalDetails = $request->medical_details ?? [];
                    if ($applicant->medicalDetails) {
                        $this->medicalService->update($medicalDetails, $applicant->id, $applicant->medicalDetails->id);
                    } else {
                        $this->medicalService->store($medicalDetails, $applicant->id);
                    }
                    break;

                case 'Financial':
                    $financial = $request->financial_details ?? [];
                    if ($applicant->financialDetails) {
                        $this->financialService->update($financial, $applicant->id, $applicant->financialDetails->id);
                    } else {
                        $this->financialService->store($financial, $applicant->id);
                    }
                    break;

                case 'Education':
                    $education = $request->education_details ?? [];
                    if ($applicant->educationDetails) {
                        $this->educationService->update($education, $applicant->id, $applicant->educationDetails->id);
                    } else {
                        $this->educationService->store($education, $applicant->id);
                    }
                    break;

                case 'Job':
                    $job = $request->job_details ?? [];
                    if ($applicant->jobDetails) {
                        $this->jobService->update($job, $applicant->id, $applicant->jobDetails->id);
                    } else {
                        $this->jobService->store($job, $applicant->id);
                    }
                    break;

                case 'Legal':
                    $legal = $request->legal_details ?? [];
                    if ($applicant->legalAidDetails) {
                        $this->legalAidService->update($legal, $applicant->id, $applicant->legalAidDetails->id);
                    } else {
                        $this->legalAidService->store($legal, $applicant->id);
                    }
                    break;

                case 'Disaster':
                    $disaster = $request->disaster_details ?? [];
                    if ($applicant->disasterDetails) {
                        $this->disasterService->update($disaster, $applicant->id, $applicant->disasterDetails->id);
                    } else {
                        $this->disasterService->store($disaster, $applicant->id);
                    }
                    break;

                default:
                    throw new \Exception("Service category not implemented.");
            }

            // 5 Attachments (optional, multiple)
            if ($request->hasFile('attachments')) {
                $files = $request->file('attachments');
                if (!is_array($files)) {
                    $files = [$files];
                }

                $uploadedPaths = ImageUpload::uploadMultipleImagesApplicationStorage(
                    $files,
                    'service_requests',
                    800,
                    600,
                    'jpg',
                    90
                );

                foreach ($uploadedPaths as $path) {
                    $applicant->attachments()->create([
                        'file_name'   => basename($path),
                        'file_path'   => $path,
                        'uploaded_by' => Auth::id(),
                        'uploaded_at' => now(),
                    ]);
                }
            }

            DB::commit();

            // 6 Return updated resource
            return new ServiceApplicantResource($applicant);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            
            $applicant = ServiceApplicant::findOrFail($id);

            $category = ServiceCategory::find($applicant->service_category_id);

            if (!$category) {
                throw new \Exception("Service category not found.");
            }

            $categoryName = $category->name_en;

            switch ($categoryName) {

                case 'Medical':
                    $medical_id = MedicalAssistanceDetail::findOrfail($applicant->id);
                    $this->medicalService->destroy($medical_id);
                    break;

                case 'Financial':
                    $financial_id = FinancialAidDetail::findOrFail($applicant->id);
                    $this->financialService->destroy($financial_id);

                case 'Education':
                    $education_id = EducationSupportDetails::findOrFail($applicant->id);
                    $this->educationService->destroy($education_id);
                    break;

                case 'Job':
                    $job_id = JobSkillRegistration::findOrFail($applicant->id);
                    $this->jobService->destroy($job_id);
                    break;

                case 'Legal':
                    $legal_id = LegalAidDetails::findOrFail($applicant->id);
                    $this->legalAidService->destroy($legal_id);
                    break;

                case 'Disaster':
                    $disaster_id = DisasterReliefDetail::findOrFail($applicant->id);
                    $this->disasterService->destroy($disaster_id);
                    break;

                default:
                    throw new \Exception("Service category not implemented.");
            }

            $applicant->delete();

            DB::commit();

            return true;

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function changeStatus(Request $request, int $id)
    {
        DB::beginTransaction();

        try {

            $request->validate([
                'status'  => 'required|in:pending,in_review,approved,in_progress,completed,rejected,cancelled',
                'remarks' => 'nullable|string|max:1000',
            ]);

            // 1 Find applicant
            $applicant = ServiceApplicant::findOrFail($id);

            $oldStatus = $applicant->status;
            $newStatus = $request->status;

            // 2 Prevent same status update
            if ($oldStatus === $newStatus) {
                throw new HttpException(422, 'Status is already set to this value.');
            }

            // 3 Update main status
            $applicant->update([
                'status' => $newStatus,
            ]);

            // 4 Status-wise additional logic
            if ($newStatus === 'in_review') {

                $applicant->reviewed_at = now();

            } elseif ($newStatus === 'approved') {

                $applicant->approved_at = now();

            } elseif ($newStatus === 'completed') {

                $applicant->completed_at = now();

            } elseif ($newStatus === 'rejected') {

                if (!$request->remarks) {
                    throw new HttpException(422, 'Rejection reason is required.');
                }

                $applicant->rejection_reason = $request->rejection_reason;

            } elseif ($newStatus === 'cancelled') {

                if (!$request->remarks) {
                    throw new HttpException(422, 'Cancelled reason is required.');
                }

                $applicant->rejection_reason = $request->rejection_reason;
            }

            $applicant->save();

            // 5 Store status history
            ServiceApplicantStatus::create([
                'request_id' => $applicant->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'remarks'    => $request->remarks,
                'changed_by' => Auth::id() ? $request->changed_by : 1,
                'changed_at' => now(),
            ]);

            DB::commit();

            return new ServiceApplicantResource(
                $applicant->load('statuses')
            );

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

    }
}