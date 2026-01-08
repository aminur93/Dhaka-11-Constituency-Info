<?php

namespace App\Http\Services\Api\V1\Admin\Financial;

use Illuminate\Http\Request;

interface FinancialService
{
    public function store(array $data, $applicantId);

    public function update(array $data, $applicantId, int $id);

    public function destroy(int $id);
}