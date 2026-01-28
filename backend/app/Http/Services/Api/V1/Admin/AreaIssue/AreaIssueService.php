<?php

namespace App\Http\Services\Api\V1\Admin\AreaIssue;

use Illuminate\Http\Request;

interface AreaIssueService
{
    public function index(Request $request);

    public function getAllAreaIssues();

    public function store(Request $request);

    public function show($id);

    public function update(Request $request, $id);

    public function destroy($id);

    public function bulkDestroy(Request $request);

    public function changeStatus(Request $request, $id);
}