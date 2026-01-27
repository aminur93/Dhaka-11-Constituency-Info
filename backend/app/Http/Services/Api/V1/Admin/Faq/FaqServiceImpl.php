<?php

namespace App\Http\Services\Api\V1\Admin\Faq;

use App\Http\Resources\Api\V1\Admin\FaqResource;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FaqServiceImpl implements FaqService
{
    public function index(Request $request)
    {
        $faq = Faq::with('creator');

        // Sorting (secure)
        $sortableColumns = ['id', 'question_en', 'question_bn', 'answer_en', 'answer_bn', 'type', 'status', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $faq->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $faq->where('type', 'like', "%{$search}%")
                ->orWhere('question_en', 'like', "%{$search}%")
                ->orWhere('question_bn', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $faq = $faq->paginate($itemsPerPage);

        return FaqResource::collection($faq);
    }

    public function getAllFaqs()
    {
        $faq = Faq::with('creator')->latest()->get();

        return FaqResource::collection($faq);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $faq = new Faq();

            $faq->type = $request->type;
            $faq->question_en = $request->question_en;
            $faq->question_bn = $request->question_bn;
            $faq->answer_en = $request->answer_en;
            $faq->answer_bn = $request->answer_bn;
            $faq->display_order = $request->display_order;
            $faq->status = $request->status ?? true;
            $faq->created_by = Auth::id() ?? null;

            $faq->save();

            activity()
                ->performedOn($faq)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Faq Store Successful');

            DB::commit();

            return new FaqResource($faq);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $faq = Faq::with('creator')->findOrFail($id);

        return new FaqResource($faq);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            $faq = Faq::with('creator')->findOrFail($id);

            $faq->type = $request->type ?? $faq->type;
            $faq->question_en = $request->question_en ?? $faq->question_en;
            $faq->question_bn = $request->question_bn ?? $faq->question_bn;
            $faq->answer_en = $request->answer_en ?? $faq->answer_en;
            $faq->answer_bn = $request->answer_bn ?? $faq->answer_bn;
            $faq->display_order = $request->display_order ?? $faq->display_order;
            $faq->status = $request->status ?? true;
            $faq->created_by = Auth::id() ?? null;

            $faq->save();

            activity()
                ->performedOn($faq)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Faq Update Successful');

            DB::commit();

            return new FaqResource($faq);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            $faq = Faq::with('creator')->findOrFail($id);

            activity()
            ->performedOn($faq)
            ->causedBy(Auth::user())
            ->withProperties(['id' => $id])
            ->log('Faq Update Successful');

            $faq->delete();

            DB::commit();

            return true;

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function changeStatus(int $id)
    {
        DB::beginTransaction();

        try {
            $faq = Faq::with('creator')->findOrFail($id);

            $faq->status = ! $faq->status;
            $faq->save();

            activity()
                ->performedOn($faq)
                ->causedBy(Auth::user())
                ->withProperties(['id' => $id, 'status' => $faq->status])
                ->log('Faq Status Change Successful');

            DB::commit();

            return new FaqResource($faq);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function viewCount(int $id)
    {
        $faq = Faq::where('id', $id)
        ->where('status', true)
        ->firstOrFail();

        // Atomic increment (safe for concurrent requests)
        $faq->increment('view_count');

        return new FaqResource($faq);
    }
}