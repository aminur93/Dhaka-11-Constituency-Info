<?php

namespace App\Http\Services\Api\V1\Admin\Union;

use Illuminate\Http\Request;

interface UnionService
{
    public function index(Request $request);

    public function getAllUnions();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id);   

    public function updateStatus(int $id);
}