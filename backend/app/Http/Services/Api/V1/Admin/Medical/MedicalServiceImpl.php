<?php

namespace App\Http\Services\Api\V1\Admin\Medical;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\MedicalAssistanceDetailResource;
use App\Models\MedicalAssistanceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicalServiceImpl implements MedicalService
{
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $data = $request->only([
                'request_id',
                'patient_name',
                'patient_age',
                'patient_gender',
                'relation_with_applicant',
                'disease_type',
                'hospital_name',
                'doctor_name',
                'estimated_cost',
                'treatment_duration',
                'is_emergency',
            ]);

            // Prescription file upload
            if ($request->hasFile('prescription_file')) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('prescription_file'),
                    'prescription-image'
                );

                // DB columns
                $data['prescription_file'] = $imagePath;
                $data['prescription_file_url'] = asset('storage/' . $imagePath);
            }

            // Medical report file upload
            if ($request->hasFile('medical_report_file')) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('medical_report_file'),
                    'medical-report-image'
                );

                // DB columns
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

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {

            $medicalAssistance = MedicalAssistanceDetail::findOrFail($id);

            $data = $request->only([
                'patient_name',
                'patient_age',
                'patient_gender',
                'relation_with_applicant',
                'disease_type',
                'hospital_name',
                'doctor_name',
                'estimated_cost',
                'treatment_duration',
                'is_emergency',
            ]);

            // Prescription file update
            if ($request->hasFile('prescription_file')) {

                // delete old file if exists
                if ($medicalAssistance->prescription_file) {
                    ImageUpload::deleteApplicationStorage($medicalAssistance->prescription_file);
                }

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('prescription_file'),
                    'prescription-image'
                );

                $data['prescription_file'] = $imagePath;
                $data['prescription_file_url'] = asset('storage/' . $imagePath);
            }

            // Medical report file update
            if ($request->hasFile('medical_report_file')) {

                // delete old file if exists
                if ($medicalAssistance->medical_report_file) {
                    ImageUpload::deleteApplicationStorage($medicalAssistance->medical_report_file);
                }

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('medical_report_file'),
                    'medical-report-image'
                );

                $data['medical_report_file'] = $imagePath;
                $data['medical_report_file_url'] = asset('storage/' . $imagePath);
            }

            $medicalAssistance->update($data);

            DB::commit();

            return new MedicalAssistanceDetailResource($medicalAssistance->fresh());

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