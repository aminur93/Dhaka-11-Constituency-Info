<?php

namespace App\Http\Services\Api\V1\Admin\IssueCategory;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\IssueCategoryResource;
use App\Models\IssueCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IssueCategoryServiceImpl implements IssueCategoryService
{
    public function index(Request $request)
    {
        $issue_category = IssueCategory::with('createdBy', 'updatedBy');

         // Sorting (secure)
        $sortableColumns = ['id', 'name_en', 'name_bn', 'description_en', 'description_bn', 'status', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $issue_category->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $issue_category->where('name_en', 'like', "%{$search}%")
                ->orWhere('name_bn', 'like', "%{$search}%")
                ->orWhere('description_en', 'like', "%{$search}%")
                ->orWhere('description_bn', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $issue_categories = $issue_category->paginate($itemsPerPage);

        return IssueCategoryResource::collection($issue_categories);
    }

    public function getAllIssueCategories()
    {
        $issue_category = IssueCategory::with('createdBy', 'updatedBy')->latest()->get();

        return IssueCategoryResource::collection($issue_category);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $issue_category = new IssueCategory();

            $issue_category->name_en = $request->name_en;
            $issue_category->name_bn = $request->name_bn;
            $issue_category->description_en = $request->description_en;
            $issue_category->description_bn = $request->description_bn;

            // Image upload
            if ($request->hasFile('image')) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('image'),
                    'issue-category'
                );

                // DB columns
                $issue_category->image = $imagePath;
                $issue_category->image_url = asset('storage/' . $imagePath);
            }

            $issue_category->status = $request->status;

            $issue_category->save();

             activity('Issue Category Store')
                ->performedOn($issue_category)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Issue Category Store Successful');

            DB::commit();

            return new IssueCategoryResource($issue_category);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $issue_category = IssueCategory::with('createdBy', 'updatedBy')->findOrFail($id);

        return new IssueCategoryResource($issue_category);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            
            $issue_category = IssueCategory::findOrFail($id);

            $issue_category->name_en = $request->name_en;
            $issue_category->name_bn = $request->name_bn;
            $issue_category->description_en = $request->description_en;
            $issue_category->description_bn = $request->description_bn;

            // Image upload
            if ($request->hasFile('image')) {

                if ($issue_category->image && file_exists(storage_path('app/public/' . $issue_category->image))) {
                    ImageUpload::deleteApplicationStorage($issue_category->image);
                }

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('image'),
                    'issue-category'
                );

                // DB columns
                $issue_category->image = $imagePath;
                $issue_category->image_url = asset('storage/' . $imagePath);
            }

            $issue_category->status = $request->status;

            $issue_category->save();

             activity('Issue Category Store')
                ->performedOn($issue_category)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Issue Category Update Successful');
            DB::commit();

            return new IssueCategoryResource($issue_category);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            
            $issue_category = IssueCategory::findOrFail($id);

            if ($issue_category->image && file_exists(storage_path('app/public/' . $issue_category->image))) {
                    
                ImageUpload::deleteApplicationStorage($issue_category->image);
            }

            activity('Issue Category Delete')
                ->performedOn($issue_category)
                ->causedBy(Auth::user())
                ->withProperties(['id' => $id])
                ->log('Issue Category Delete Successful');

            $issue_category->delete();

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
            
            $issue_category = IssueCategory::findOrFail($id);

            $issue_category->status = ! $issue_category->status;

            $issue_category->save();

            activity('Issue Category status change')
                ->performedOn($issue_category)
                ->causedBy(Auth::user())
                ->withProperties(['id' => $id])
                ->log('Issue Category status change Successful');

            DB::commit();

            return new IssueCategoryResource($issue_category);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}