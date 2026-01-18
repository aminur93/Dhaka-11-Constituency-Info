<?php

namespace App\Http\Services\Api\V1\Admin\Event;

use Illuminate\Http\Request;

interface EventService
{
    public function index(Request $request);

    public function getAllEvents();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);
    
    public function destroy(int $id);

    public function changeStatus(Request $request, int $id);
}