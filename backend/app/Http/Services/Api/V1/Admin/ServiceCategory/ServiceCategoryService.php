<?php

namespace App\Http\Services\Api\V1\Admin\ServiceCategory;

use Illuminate\Http\Request;

interface ServiceCategoryService
{
    public function index(Request $request);

    public function getAllServiceCategories();

    public function store(Request $request);

    public function show($id);

    public function update(Request $request, $id);

    public function destroy($id);

    public function statusUpdate(int $id);
}