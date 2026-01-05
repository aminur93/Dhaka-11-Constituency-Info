<?php

namespace App\Http\Services\Api\V1\Admin\Financial;

use Illuminate\Http\Request;

interface FinancialService
{
    public function store(Request $request);

    public function update(Request $request, int $id);

    public function destroy(int $id);
}