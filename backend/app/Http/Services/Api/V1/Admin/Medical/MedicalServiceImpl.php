<?php

namespace App\Http\Services\Api\V1\Admin\Medical;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\MedicalAssistanceDetailResource;
use App\Models\MedicalAssistanceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicalServiceImpl implements MedicalService
{
    public function store(array $data, $applicantId)
    {
        DB::beginTransaction();

        try {

            // Add request_id to data
            $data['request_id'] = $applicantId;

            // Prescription file
            if (!empty($data['prescription_file']) && $data['prescription_file'] instanceof \Illuminate\Http\UploadedFile) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $data['prescription_file'],
                    'prescription-image'
                );

                $data['prescription_file'] = $imagePath;
                $data['prescription_file_url'] = asset('storage/' . $imagePath);
            }

            // Medical report file
            if (!empty($data['medical_report_file']) && $data['medical_report_file'] instanceof \Illuminate\Http\UploadedFile) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $data['medical_report_file'],
                    'medical-report-image'
                );

                $data['medical_report_file'] = $imagePath;
                $data['medical_report_file_url'] = asset('storage/' . $imagePath);
            }

            $medicalAssistance = MedicalAssistanceDetail::create($data);

            DB::commit();

            return new MedicalAssistanceDetailResource($medicalAssistance);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }


    public function update(array $data, $applicantId, int $id)
    {
        DB::beginTransaction();

        try {
            // Find existing record
            $medicalAssistance = MedicalAssistanceDetail::findOrFail($id);

            // Ensure request_id is correct
            $data['request_id'] = $applicantId;

            // Handle prescription file if exists
            if (!empty($data['prescription_file']) && $data['prescription_file'] instanceof \Illuminate\Http\UploadedFile) {

                // Delete old file if exists
                if ($medicalAssistance->prescription_file) {
                    ImageUpload::deleteApplicationStorage($medicalAssistance->prescription_file);
                }

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $data['prescription_file'],
                    'prescription-image'
                );

                $data['prescription_file'] = $imagePath;
                $data['prescription_file_url'] = asset('storage/' . $imagePath);
            }

            // Handle medical report file if exists
            if (!empty($data['medical_report_file']) && $data['medical_report_file'] instanceof \Illuminate\Http\UploadedFile) {

                // Delete old file if exists
                if ($medicalAssistance->medical_report_file) {
                    ImageUpload::deleteApplicationStorage($medicalAssistance->medical_report_file);
                }

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $data['medical_report_file'],
                    'medical-report-image'
                );

                $data['medical_report_file'] = $imagePath;
                $data['medical_report_file_url'] = asset('storage/' . $imagePath);
            }

            // Update all other fields
            $medicalAssistance->update($data);

            DB::commit();

            return new MedicalAssistanceDetailResource($medicalAssistance);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }


    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            $medicalAssistance = MedicalAssistanceDetail::findOrFail($id);

            $medicalAssistance->delete();

            DB::commit();

            return true;

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}