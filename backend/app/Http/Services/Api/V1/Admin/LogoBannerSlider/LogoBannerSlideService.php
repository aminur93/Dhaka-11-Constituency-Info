<?php

namespace App\Http\Services\Api\V1\Admin\LogoBannerSlider;

use Illuminate\Http\Request;

interface LogoBannerSlideService
{
    public function index(Request $request);

    public function getAllLogoBannerSlides();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id);

    public function statusUpdate(int $id);
}