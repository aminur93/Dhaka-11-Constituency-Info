<?php

namespace App\Http\Services\Api\V1\Admin\AreaDemographic;

use Illuminate\Http\Request;

interface AreaDemographicService
{
    public function index(Request $request);

    public function getAllAreaDemographics();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);
    
    public function destroy(int $id);
}