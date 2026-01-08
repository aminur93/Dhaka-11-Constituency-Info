<?php

namespace App\Http\Services\Api\V1\Admin\Job;

use Illuminate\Http\Request;

interface JobService
{
    public function store(array $data, $applicantId);

    public function update(array $data, $applicantId, int $id);

    public function destroy(int $id);
}