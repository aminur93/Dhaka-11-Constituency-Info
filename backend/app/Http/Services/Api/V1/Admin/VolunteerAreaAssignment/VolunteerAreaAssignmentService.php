<?php

namespace App\Http\Services\Api\V1\Admin\VolunteerAreaAssignment;

use Illuminate\Http\Request;

interface VolunteerAreaAssignmentService
{
    public function index(Request $request);

    public function getAllVolunteerAreaAssignments();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id);
}