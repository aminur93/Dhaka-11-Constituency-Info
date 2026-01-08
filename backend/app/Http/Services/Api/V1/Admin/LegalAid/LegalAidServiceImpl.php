<?php

namespace App\Http\Services\Api\V1\Admin\LegalAid;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\LegalAidDetailsResource;
use App\Models\LegalAidDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LegalAidServiceImpl implements LegalAidService
{
    public function store(array $data, $applicantId)
    {
        DB::beginTransaction();

        try {
            
            // Add request_id to data
            $data['request_id'] = $applicantId;

            // Case documents file upload
            if (!empty($data['case_documents_file']) && $data['case_documents_file'] instanceof \Illuminate\Http\UploadedFile) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $data['case_documents_file'],
                    'case-documents-file'
                );

                $data['case_documents_file'] = $imagePath;
                $data['case_documents_file_url'] = asset('storage/' . $imagePath);
            }

            $legalAid = LegalAidDetails::create($data);

            DB::commit();

            return new LegalAidDetailsResource($legalAid);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function update(array $data, $applicantId, int $id)
    {
        DB::beginTransaction();

        try {
            $legalAid = LegalAidDetails::findOrFail($id);

            $data['request_id'] = $applicantId;

            // Case documents file update
            if (!empty($data['case_documents_file']) && $data['case_documents_file'] instanceof \Illuminate\Http\UploadedFile) {

                // Delete old file if exists
                if (!empty($legalAid->case_documents_file)) {
                    ImageUpload::deleteApplicationStorage($legalAid->case_documents_file);
                }

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $data['case_documents_file'],
                    'case-documents-file'
                );

                $data['case_documents_file'] = $imagePath;
                $data['case_documents_file_url'] = asset('storage/' . $imagePath);
            }

            $legalAid->update($data);

            DB::commit();

            return new LegalAidDetailsResource($legalAid->fresh());

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            
            $legalAid = LegalAidDetails::findOrFail($id);

            $legalAid->delete();

            DB::commit();

            return true;

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}