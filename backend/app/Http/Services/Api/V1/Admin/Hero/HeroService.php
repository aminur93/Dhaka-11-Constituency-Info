<?php

namespace App\Http\Services\Api\V1\Admin\Hero;

use Illuminate\Http\Request;

interface HeroService
{
    public function index(Request $request);

    public function getAllHeros();

    public function store(Request $request);
    
    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id);

    public function statusUpdate(int $id);
}