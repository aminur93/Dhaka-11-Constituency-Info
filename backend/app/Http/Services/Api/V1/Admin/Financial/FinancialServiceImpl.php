<?php

namespace App\Http\Services\Api\V1\Admin\Financial;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\FinancialAidDetailsResource;
use App\Models\FinancialAidDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialServiceImpl implements FinancialService
{
    public function store(array $data, $applicantId)
    {
        DB::beginTransaction();

        try {

            // Add request_id to data
            $data['request_id'] = $applicantId;

            // Income proof file upload
            if (!empty($data['income_proof_file']) && $data['income_proof_file'] instanceof \Illuminate\Http\UploadedFile) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $data['income_proof_file'],
                    'income-proof-image'
                );

                $data['income_proof_file'] = $imagePath;
                $data['income_proof_file_url'] = asset('storage/' . $imagePath);
            }

            $financial = FinancialAidDetail::create($data);

            DB::commit();

            return new FinancialAidDetailsResource($financial);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function update(array $data, $applicantId, int $id)
    {
        DB::beginTransaction();

        try {

            $financial = FinancialAidDetail::findOrFail($id);

            $data['request_id'] = $applicantId;

            // Income proof file update
            if (!empty($data['income_proof_file']) && $data['income_proof_file'] instanceof \Illuminate\Http\UploadedFile) {

                // Delete old file if exists
                if (!empty($financial->income_proof_file)) {
                    ImageUpload::deleteApplicationStorage($financial->income_proof_file);
                }

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $data['income_proof_file'],
                    'income-proof-image'
                );

                $data['income_proof_file'] = $imagePath;
                $data['income_proof_file_url'] = asset('storage/' . $imagePath);
            }

            $financial->update($data);

            DB::commit();

            return new FinancialAidDetailsResource($financial->fresh());

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            
            $financial = FinancialAidDetail::findOrFail($id);
            $financial->delete();

            DB::commit();

            return true;

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}