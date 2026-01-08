<?php

namespace App\Http\Services\Api\V1\Admin\Disaster;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\DisasterReliefDetailsResource;
use App\Models\DisasterReliefDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisasterServiceImpl implements DisasterService
{
    public function store(array $data, $applicantId)
    {
        DB::beginTransaction();

        try {
             // Add request_id to data
            $data['request_id'] = $applicantId;

            // Damage photo file upload
            if (!empty($data['damage_photo']) && $data['damage_photo'] instanceof \Illuminate\Http\UploadedFile) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $data['damage_photo'],
                    'damage-image'
                );

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

    public function update(array $data, $applicantId, int $id)
    {
        DB::beginTransaction();

        try {
            $disasterRelief = DisasterReliefDetail::findOrFail($id);

            $data['request_id'] = $applicantId;

            // Damage photo file update
            if (!empty($data['damage_photo']) && $data['damage_photo'] instanceof \Illuminate\Http\UploadedFile) {

                // Delete old file if exists
                if (!empty($disasterRelief->damage_photo)) {
                    ImageUpload::deleteApplicationStorage($disasterRelief->damage_photo);
                }
                
                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $data['damage_photo'],
                    'damage-image'
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