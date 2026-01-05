<?php

namespace App\Http\Services\Api\V1\Admin\Medical;

use Illuminate\Http\Request;

interface MedicalService
{
    public function store(Request $request);

    public function update(Request $request, int $id);

    public function destroy(int $id);
}