<?php

namespace App\Http\Services\Api\V1\Admin\Education;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\EducationSupportDetailsResource;
use App\Models\EducationSupportDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EducationServiceImpl implements EducationService
{
    public function store(array $data, $applicantId)
    {
        DB::beginTransaction();

        try {
            
            // Add request_id to data
            $data['request_id'] = $applicantId;

            // Academic certificate file upload
            if (!empty($data['academic_certificate_file']) && $data['academic_certificate_file'] instanceof \Illuminate\Http\UploadedFile) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $data['academic_certificate_file'],
                    'academic-certificate-image'
                );

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

    public function update(array $data, $applicantId, int $id)
    {
        DB::beginTransaction();

        try {
            $educationSupport = EducationSupportDetails::findOrFail($id);

            $data['request_id'] = $applicantId;

            // Academic certificate file update
            if (!empty($data['academic_certificate_file']) && $data['academic_certificate_file'] instanceof \Illuminate\Http\UploadedFile) {

                // Delete old file if exists
                if (!empty($educationSupport->academic_certificate_file)) {
                    ImageUpload::deleteApplicationStorage(
                        $educationSupport->academic_certificate_file
                    );
                }

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $data['academic_certificate_file'],
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