<?php

namespace App\Http\Services\Api\V1\Admin\Faq;

use Illuminate\Http\Request;

interface FaqService
{
    public function index(Request $request);

    public function getAllFaqs();

    public function store(Request $request);

    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id);

    public function changeStatus(int $id);

    public function viewCount(int $id);
}