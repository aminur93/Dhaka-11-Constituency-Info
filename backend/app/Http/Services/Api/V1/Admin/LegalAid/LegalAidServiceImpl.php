<?php

namespace App\Http\Services\Api\V1\Admin\LegalAid;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\LegalAidDetailsResource;
use App\Models\LegalAidDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LegalAidServiceImpl implements LegalAidService
{
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $data = $request->only([
                'request_id',
                'case_type',
                'case_number',
                'court_name',
                'opponent_party',
                'case_description',
            ]);

            // Case documents file upload
            if ($request->hasFile('case_documents_file')) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('case_documents_file'),
                    'case-documents-file'
                );

                // DB columns
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

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            $legalAid = LegalAidDetails::findOrFail($id);

            $data = $request->only([
                'case_type',
                'case_number',
                'court_name',
                'opponent_party',
                'case_description',
            ]);

            // Case documents file update
            if ($request->hasFile('case_documents_file')) {

                // Delete old file if exists
                if (!empty($legalAid->case_documents_file)) {
                    ImageUpload::deleteApplicationStorage($legalAid->case_documents_file);
                }

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('case_documents_file'),
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