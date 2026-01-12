<?php

namespace App\Http\Services\Api\V1\Admin\Volunteer;

use Illuminate\Http\Request;

interface VolunteerService
{
    public function index(Request $request);

    public function getAllVolunteers();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id);
}