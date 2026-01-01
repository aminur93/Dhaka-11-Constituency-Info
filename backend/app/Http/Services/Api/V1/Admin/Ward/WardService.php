<?php

namespace App\Http\Services\Api\V1\Admin\Ward;

use Illuminate\Http\Request;

interface WardService
{
    public function index(Request $request);

    public function getAllWards();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, $id);

    public function destroy(int $id);

    public function updateStatus(int $id);
}