<?php

namespace App\Http\Services\Api\V1\Admin\Job;

use Illuminate\Http\Request;

interface JobService
{
    public function store(Request $request);

    public function update(Request $request, int $id);

    public function destroy(int $id);
}