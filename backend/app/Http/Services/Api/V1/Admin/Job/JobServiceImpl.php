<?php

namespace App\Http\Services\Api\V1\Admin\Job;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\JobSkillRegistrationResource;
use App\Models\JobSkillRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobServiceImpl implements JobService
{
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $data = $request->only([
                'request_id',
                'qualification',
                'experience_years',
                'skills',
                'preferred_sector',
                'training_interest',
                'employment_status',
            ]);

            // CV file upload
            if ($request->hasFile('cv_file')) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('cv_file'),
                    'cv-file'
                );

                // DB columns
                $data['cv_file'] = $imagePath;
                $data['cv_file_url'] = asset('storage/' . $imagePath);
            }

            $jobSkill = JobSkillRegistration::create($data);

            DB::commit();

            return new JobSkillRegistrationResource($jobSkill);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            $jobSkill = JobSkillRegistration::findOrFail($id);

            $data = $request->only([
                'qualification',
                'experience_years',
                'skills',
                'preferred_sector',
                'training_interest',
                'employment_status',
            ]);

            // CV file update
            if ($request->hasFile('cv_file')) {

                // Delete old file if exists
                if (!empty($jobSkill->cv_file)) {
                    ImageUpload::deleteApplicationStorage($jobSkill->cv_file);
                }

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('cv_file'),
                    'cv-file'
                );

                $data['cv_file'] = $imagePath;
                $data['cv_file_url'] = asset('storage/' . $imagePath);
            }

            $jobSkill->update($data);

            DB::commit();

            return new JobSkillRegistrationResource($jobSkill->fresh());

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            
            $jobSkill = JobSkillRegistration::findOrFail($id);

            $jobSkill->delete();

            DB::commit();

            return true;

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}