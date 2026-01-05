<?php

namespace App\Http\Services\Api\V1\Admin\Disaster;

use Illuminate\Http\Request;

interface DisasterService
{
    public function store(Request $request);

    public function update(Request $request, int $id);

    public function destroy(int $id);
}