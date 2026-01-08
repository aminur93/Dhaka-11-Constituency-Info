<?php

namespace App\Http\Services\Api\V1\Admin\Education;

use Illuminate\Http\Request;

interface EducationService
{
    public function store(array $data, $applicantId);

    public function update(array $data, $applicantId, int $id);

    public function destroy(int $id);
}