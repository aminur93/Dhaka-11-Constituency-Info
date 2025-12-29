<?php

namespace App\Http\Services\Api\V1\Admin\District;

use Illuminate\Http\Request;

interface DistrictService
{
    public function index(Request $request);

    public function getAllDistricts();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id);

    public function updateStatus(int $id);
}