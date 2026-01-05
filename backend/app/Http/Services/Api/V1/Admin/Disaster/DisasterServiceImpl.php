<?php

namespace App\Http\Services\Api\V1\Admin\Disaster;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\DisasterReliefDetailsResource;
use App\Models\DisasterReliefDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisasterServiceImpl implements DisasterService
{
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->only([
                'request_id',
                'disaster_type',
                'disaster_date',
                'loss_type',
                'estimated_loss',
                'family_affected',
                'temporary_shelter_needed',
                'relief_items_needed',
            ]);

            // Damage photo file upload
            if ($request->hasFile('damage_photo')) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('damage_photo'),
                    'academic-certificate-image'
                );

                // DB columns
                $data['damage_photo'] = $imagePath;
                $data['damage_photo_url'] = asset('storage/' . $imagePath);
            }

            $disasterRelief = DisasterReliefDetail::create($data);

            DB::commit();

            return new DisasterReliefDetailsResource($disasterRelief);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            $disasterRelief = DisasterReliefDetail::findOrFail($id);

            $data = $request->only([
                'disaster_type',
                'disaster_date',
                'loss_type',
                'estimated_loss',
                'family_affected',
                'temporary_shelter_needed',
                'relief_items_needed',
            ]);

            // Damage photo file update
            if ($request->hasFile('damage_photo')) {

                // Delete old file if exists
                if (!empty($disasterRelief->damage_photo)) {
                    ImageUpload::deleteApplicationStorage($disasterRelief->damage_photo);
                }

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('damage_photo'),
                    'disaster-damage-photo'
                );

                $data['damage_photo'] = $imagePath;
                $data['damage_photo_url'] = asset('storage/' . $imagePath);
            }

            $disasterRelief->update($data);

            DB::commit();

            return new DisasterReliefDetailsResource($disasterRelief->fresh());

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            
            $disasterRelief = DisasterReliefDetail::findOrFail($id);

            $disasterRelief->delete();

            DB::commit();

            return true;

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}