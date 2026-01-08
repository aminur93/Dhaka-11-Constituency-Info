<?php

namespace App\Http\Services\Api\V1\Admin\Disaster;

use Illuminate\Http\Request;

interface DisasterService
{
    public function store(array $data, $applicantId);

    public function update(array $data, $applicantId, int $id);

    public function destroy(int $id);
}