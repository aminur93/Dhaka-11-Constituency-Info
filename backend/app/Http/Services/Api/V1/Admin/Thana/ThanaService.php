<?php

namespace App\Http\Services\Api\V1\Admin\Thana;

use Illuminate\Http\Request;

interface ThanaService
{
    public function index(Request $request);

    public function getAllThanas();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id);

    public function updateStatus(int $id);
}