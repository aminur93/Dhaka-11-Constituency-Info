<?php

namespace App\Http\Services\Api\V1\Admin\ServiceApplicant;

use Illuminate\Http\Request;

interface ServiceApplicantService
{
    public function index(Request $request);

    public function getAllServiceApplicants();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id);

    public function changeStatus(Request $request, int $id);
}