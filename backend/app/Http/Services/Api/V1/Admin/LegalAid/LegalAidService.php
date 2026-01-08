<?php

namespace App\Http\Services\Api\V1\Admin\LegalAid;

use Illuminate\Http\Request;

interface LegalAidService
{
    public function store(array $data, $applicantId);

    public function update(array $data, $applicantId, int $id);

    public function destroy(int $id);
}