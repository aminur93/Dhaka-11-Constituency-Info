<?php

namespace App\Http\Services\Api\V1\Admin\Poll;

use Illuminate\Http\Request;

interface PollService
{
    public function index(Request $request);

    public function getAllPolls();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id);

    public function changeStatus(int $id);
}