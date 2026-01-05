<?php

namespace App\Http\Services\Api\V1\Admin\Education;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\EducationSupportDetailsResource;
use App\Models\EducationSupportDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EducationServiceImpl implements EducationService
{
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
             $data = $request->only([
                'request_id',
                'student_name',
                'student_age',
                'education_level',
                'institution_name',
                'class_year',
                'gpa_cgpa',
                'support_type',
            ]);

            // Academic certificate file upload
            if ($request->hasFile('academic_certificate_file')) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('academic_certificate_file'),
                    'academic-certificate-image'
                );

                // DB columns
                $data['academic_certificate_file'] = $imagePath;
                $data['academic_certificate_file_url'] = asset('storage/' . $imagePath);
            }

            $educationSupport = EducationSupportDetails::create($data);

            DB::commit();

            return new EducationSupportDetailsResource($educationSupport);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            $educationSupport = EducationSupportDetails::findOrFail($id);

            $data = $request->only([
                'student_name',
                'student_age',
                'education_level',
                'institution_name',
                'class_year',
                'gpa_cgpa',
                'support_type',
            ]);

            // Academic certificate file update
            if ($request->hasFile('academic_certificate_file')) {

                // Delete old file if exists
                if (!empty($educationSupport->academic_certificate_file)) {
                    ImageUpload::deleteApplicationStorage(
                        $educationSupport->academic_certificate_file
                    );
                }

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('academic_certificate_file'),
                    'academic-certificate-image'
                );

                $data['academic_certificate_file'] = $imagePath;
                $data['academic_certificate_file_url'] = asset('storage/' . $imagePath);
            }

            $educationSupport->update($data);

            DB::commit();

            return new EducationSupportDetailsResource($educationSupport->fresh());

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            
            $educationSupport = EducationSupportDetails::findOrFail($id);

            $educationSupport->delete();

            DB::commit();

            return true;

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}