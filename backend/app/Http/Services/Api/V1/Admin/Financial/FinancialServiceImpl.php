<?php

namespace App\Http\Services\Api\V1\Admin\Financial;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\FinancialAidDetailsResource;
use App\Models\FinancialAidDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialServiceImpl implements FinancialService
{
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $data = $request->only([
                'request_id',
                'aid_purpose',
                'monthly_income',
                'family_members',
                'earning_members',
                'current_debt',
                'assets_description',
            ]);

            // Income proof file upload
            if ($request->hasFile('income_proof_file')) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('income_proof_file'),
                    'income-proof-image'
                );

                // DB columns
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

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {

            $financial = FinancialAidDetail::findOrFail($id);

            $data = $request->only([
                'aid_purpose',
                'monthly_income',
                'family_members',
                'earning_members',
                'current_debt',
                'assets_description',
            ]);

            // Income proof file update
            if ($request->hasFile('income_proof_file')) {

                // Delete old file if exists
                if (!empty($financial->income_proof_file)) {
                    ImageUpload::deleteApplicationStorage($financial->income_proof_file);
                }

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('income_proof_file'),
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