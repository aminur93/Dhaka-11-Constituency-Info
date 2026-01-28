<?php

namespace App\Http\Services\Api\V1\Admin\AreaIssue;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\AreaIssueResource;
use App\Models\AreaIssue;
use App\Models\AreaIssueFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AreaIssueServiceImpl implements AreaIssueService
{
    public function index(Request $request)
    {
        $area_issue = AreaIssue::with(['category', 'ward', 'thana', 'reporter', 'assignee', 'areaIssueFile']);

         // Sorting (secure)
        $sortableColumns = ['id', 'issue_code', 'title_en', 'description_en', 'status', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $area_issue->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $area_issue->where('issue_code', 'like', "%{$search}%")
                ->orWhere('title_en', 'like', "%{$search}%")
                ->orWhere('description_en', 'like', "%{$search}%")
                ->orWhere('description_bn', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $area_issues = $area_issue->paginate($itemsPerPage);

        return AreaIssueResource::collection($area_issues);
    }

    public function getAllAreaIssues()
    {
        $area_issue = AreaIssue::with(['category', 'ward', 'thana', 'reporter', 'assignee', 'areaIssueFile'])->latest()->get();

        return AreaIssueResource::collection($area_issue);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            

             // 🔹 Generate Issue Code (ISS-2026-000001 format)
            $latestId = AreaIssue::max('id') + 1;
            $issueCode = 'ISS-' . date('Y') . '-' . str_pad($latestId, 6, '0', STR_PAD_LEFT);

            // 🔹 Create Issue
            $issue = new AreaIssue();
            $issue->issue_code = $issueCode;
            $issue->issue_category_id = $request->issue_category_id;
            $issue->ward_id = $request->ward_id;
            $issue->thana_id = $request->thana_id;
            $issue->reported_by = $request->reported_by;
            $issue->title_en = $request->title_en;
            $issue->title_bn = $request->title_bn;
            $issue->description_en = $request->description_en;
            $issue->description_bn = $request->description_bn;
            $issue->severity = $request->severity;
            $issue->status = $request->status;
            $issue->latitude = $request->latitude;
            $issue->longitude = $request->longitude;
            $issue->source = $request->source;
            $issue->is_anonymous = $request->is_anonymous;
            $issue->assigned_to = $request->assigned_to;
            $issue->priority_score = $request->priority_score;
            $issue->reported_at = now();

            $issue->save();

             activity('Area Issue Store')
                ->performedOn($issue)
                ->causedBy(Auth::user())
                ->withProperties(['issue_code' => $issue->issue_code])
                ->log('Area Issue created');

            // 🔹 Handle Multiple File Uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {

                    $areaIssueFile = new AreaIssueFile();
                    $areaIssueFile->area_issue_id = $issue->id;

                    // Upload each file separately
                    $filePath = ImageUpload::uploadImageApplicationStorage(
                        $file,
                        'issue-file'
                    );

                    $areaIssueFile->file_path = $filePath;
                    $areaIssueFile->file_url = asset('storage/' . $filePath);
                    $areaIssueFile->file_name = $file->getClientOriginalName();
                    $areaIssueFile->file_type = $file->getClientMimeType();
                    $areaIssueFile->file_size = round($file->getSize() / 1024); // KB
                    $areaIssueFile->uploaded_by = Auth::id() ?? null;

                    $areaIssueFile->save();
                    
                }
            }

            DB::commit();

            return new AreaIssueResource($issue);
            
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show($id)
    {
        $area_issue = AreaIssue::with(['category', 'ward', 'thana', 'reporter', 'assignee', 'areaIssueFile'])->findOrFail($id);

        return new AreaIssueResource($area_issue);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $issue = AreaIssue::findOrFail($id);

            $issue->issue_category_id = $request->issue_category_id ?? $issue->issue_category_id;
            $issue->ward_id = $request->ward_id ?? $issue->ward_id;
            $issue->thana_id = $request->thana_id ?? $issue->thana_id;
            $issue->reported_by = $request->reported_by ?? $issue->reported_by;
            $issue->title_en = $request->title_en ?? $issue->title_en;
            $issue->title_bn = $request->title_bn ?? $issue->title_bn;
            $issue->description_en = $request->description_en ?? $issue->description_en;
            $issue->description_bn = $request->description_bn ?? $issue->description_bn;
            $issue->severity = $request->severity ?? $issue->severity;
            $issue->status = $request->status ?? $issue->status;
            $issue->latitude = $request->latitude ?? $issue->latitude;
            $issue->longitude = $request->longitude ?? $issue->longitude;
            $issue->source = $request->source ?? $issue->source;
            $issue->is_anonymous = $request->is_anonymous ?? $issue->is_anonymous;
            $issue->assigned_to = $request->assigned_to ?? $issue->assigned_to;
            $issue->priority_score = $request->priority_score ?? $issue->priority_score;

            $issue->save();

            //  Handle Multiple File Uploads
            if ($request->hasFile('files')) {

                // Step 1: Delete old files FIRST (only once)
                $oldFiles = AreaIssueFile::where('area_issue_id', $issue->id)->get();

                foreach ($oldFiles as $oldFile) {
                    ImageUpload::deleteApplicationStorage($oldFile->file_path);
                    $oldFile->delete();
                }

                // Step 2: Upload new files
                foreach ($request->file('files') as $file) {

                    $filePath = ImageUpload::uploadImageApplicationStorage(
                        $file,
                        'issue-file'
                    );

                    AreaIssueFile::create([
                        'area_issue_id' => $issue->id,
                        'file_path'     => $filePath,
                        'file_url'      => asset('storage/' . $filePath),
                        'file_name'     => $file->getClientOriginalName(),
                        'file_type'     => $file->getClientMimeType(),
                        'file_size'     => round($file->getSize() / 1024),
                        'uploaded_by'   => Auth::id() ?? null,
                    ]);
                }
            }

             activity('Area Issue Update')
                ->performedOn($issue)
                ->causedBy(Auth::user())
                ->withProperties(['issue_code' => $issue->issue_code])
                ->log('Area Issue updated');

            DB::commit();

            return new AreaIssueResource($issue);
            
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $area_issue = AreaIssue::findOrFail($id);

            // Delete associated files
            $files = AreaIssueFile::where('area_issue_id', $area_issue->id)->get();

            foreach ($files as $file) {
                ImageUpload::deleteApplicationStorage($file->file_path);
                $file->delete();
            }

             activity('Area Issue Delete')
                ->performedOn($area_issue)
                ->causedBy(Auth::user())
                ->withProperties(['id' => $id])
                ->log('Area Issue Delete Successful');

            $area_issue->delete();

            DB::commit();

            return true;
            
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function bulkDestroy(Request $request)
    {
        DB::beginTransaction();

        try {
            $ids = $request->ids;

            foreach ($ids as $id) {
                $this->destroy($id);
            }

            DB::commit();

            return true;
            
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function changeStatus(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $area_issue = AreaIssue::findOrFail($id);

            $area_issue->status = $request->status;

            $area_issue->save();

             activity('Area Issue status change')
                ->performedOn($area_issue)
                ->causedBy(Auth::user())
                ->withProperties(['id' => $id, 'new_status' => $request->status])
                ->log('Area Issue status change Successful');

            DB::commit();

            return new AreaIssueResource($area_issue);
            
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}