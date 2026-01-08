<?php

namespace App\Http\Services\Api\V1\Admin\Medical;

use Illuminate\Http\Request;

interface MedicalService
{
    public function store(array $data, $applicantId);

    public function update(array $data, $applicantId, int $id);

    public function destroy(int $id);
}