<?php

namespace App\Http\Services\Api\V1\Admin\Notice;

use Illuminate\Http\Request;

interface NoticeService
{
    public function index(Request $request);

    public function getAllNotices();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id);
}