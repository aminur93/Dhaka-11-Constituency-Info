<?php

namespace App\Http\Services\Api\V1\Admin\FieldReport;

use Illuminate\Http\Request;

interface FieldReportService
{
    public function index(Request $request);

    public function getAllFieldReports();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id);
}