<?php

namespace App\Http\Services\Api\V1\Admin\LegalAid;

use Illuminate\Http\Request;

interface LegalAidService
{
    public function store(Request $request);

    public function update(Request $request, int $id);

    public function destroy(int $id);
}