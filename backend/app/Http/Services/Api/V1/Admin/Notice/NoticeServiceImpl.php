<?php

namespace App\Http\Services\Api\V1\Admin\Notice;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\NoticeResource;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NoticeServiceImpl implements NoticeService
{
    public function index(Request $request)
    {
        $notice = Notice::with('ward', 'thana', 'createdBy');

         // Sorting (secure)
        $sortableColumns = ['id', 'title_en', 'content_bn', 'category', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $notice->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $notice->where('title_en', 'like', "%{$search}%")
                ->orWhere('content_bn', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $notices = $notice->paginate($itemsPerPage);

        return NoticeResource::collection($notices);
    }

    public function getAllNotices()
    {
        $notices = Notice::with('ward', 'thana', 'createdBy')->latest()->get();

        return NoticeResource::collection($notices);
    }

    public function store(Request $request)
    {
         DB::beginTransaction();

        try {
            $notice = new Notice();

            $notice->title_en = $request->title_en;
            $notice->title_bn = $request->title_bn;
            $notice->content_en = $request->content_en;
            $notice->content_bn = $request->content_bn;
            $notice->category = $request->category;
            $notice->priority = $request->priority ?? 'normal';
            $notice->target_audience = $request->target_audience;
            $notice->ward_id = $request->ward_id;
            $notice->thana_id = $request->thana_id;
            $notice->is_active = $request->is_active ?? true;

            if ($request->hasFile('attachment_file')) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('attachment_file'),
                    'notice-file'
                );

                // DB columns
                $notice->attachment_file = $imagePath;
                $notice->attachment_file_url = asset('storage/' . $imagePath);
            }
            
            $notice->published_at = $request->published_at ?? now();
            $notice->expires_at = $request->expires_at;
            $notice->created_by = Auth::id() ?? null;

            $notice->save();

            activity('Notice')
                ->performedOn($notice)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Notice created successfully');

            DB::commit();

            return new NoticeResource($notice);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function show(int $id)
    {
        $notice = Notice::with('ward', 'thana', 'createdBy')->findOrFail($id);

        return new NoticeResource($notice);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            $notice = Notice::findOrFail($id);

            $notice->title_en = $request->title_en ?? $notice->title_en;
            $notice->title_bn = $request->title_bn ?? $notice->title_bn;
            $notice->content_en= $request->content_en ?? $notice->content_en;
            $notice->content_bn = $request->content_bn ?? $notice->content_bn;
            $notice->category = $request->category ?? $notice->category;
            $notice->priority = $request->priority ?? $notice->priority;
            $notice->target_audience = $request->target_audience ?? $notice->target_audience;
            $notice->ward_id = $request->ward_id ?? $notice->ward_id;
            $notice->thana_id = $request->thana_id ?? $notice->thana_id;
            $notice->is_active = $request->is_active ?? $notice->is_active;

            if ($request->hasFile('attachment_file')) {

                if ($notice->attachment_file) {
                    ImageUpload::deleteApplicationStorage($notice->attachment_file);
                }

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('attachment_file'),
                    'notice-file'
                );

                // DB columns
                $notice->attachment_file = $imagePath;
                $notice->attachment_file_url = asset('storage/' . $imagePath);
            }
            
            $notice->published_at = $request->published_at ?? $notice->published_at;
            $notice->expires_at = $request->expires_at ?? $notice->expires_at;

            $notice->save();

            activity('Notice')
                ->performedOn($notice)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Notice updated successfully');

            DB::commit();

            return new NoticeResource($notice);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            $notice = Notice::findOrFail($id);
            $notice->delete();

            activity('Notice')
                ->performedOn($notice)
                ->causedBy(Auth::user())
                ->log('Notice deleted successfully');

            DB::commit();

            return true;

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}