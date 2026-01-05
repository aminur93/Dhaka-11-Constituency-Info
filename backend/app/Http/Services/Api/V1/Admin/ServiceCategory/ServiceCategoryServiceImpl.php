<?php

namespace App\Http\Services\Api\V1\Admin\ServiceCategory;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\ServiceCategoryResource;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceCategoryServiceImpl implements ServiceCategoryService
{
    public function index(Request $request)
    {
        $query = ServiceCategory::query();

        // Sorting (secure)
        $sortableColumns = ['id', 'name_en', 'name_bn', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $query->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $query->where('title_en', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $serviceCategories = $query->paginate($itemsPerPage);

        return ServiceCategoryResource::collection($serviceCategories);
    }

    public function getAllServiceCategories()
    {
        $serviceCategories = ServiceCategory::orderBy('id', 'desc')->get();

        return ServiceCategoryResource::collection($serviceCategories);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $service_category = new ServiceCategory();

            $service_category->name_en = $request->name_en;
            $service_category->name_bn = $request->name_bn;
            $service_category->code = $request->code;
            $service_category->description_en = $request->description_en;
            $service_category->description_bn = $request->description_bn;

            // Image upload
             if ($request->hasFile('image')) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('image'),
                    'heros'
                );

                // DB columns
                $service_category->image = $imagePath;
                $service_category->image_url = asset('storage/' . $imagePath);
            }

            $service_category->status = $request->status;

            $service_category->save();

            activity('Service Category Store')
                ->performedOn($service_category)
                ->causedBy($service_category)
                ->withProperties(['attributes' => $request->all()])
                ->log('Service Category Store Successful');

            DB::commit();

            return new ServiceCategoryResource($service_category);


        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show($id)
    {
        $service_category = ServiceCategory::findOrFail($id);

        return new ServiceCategoryResource($service_category);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            
            $service_category = ServiceCategory::findOrFail($id);

            $service_category->name_en = $request->name_en ?? $service_category->name_en;
            $service_category->name_bn = $request->name_bn ?? $service_category->name_bn;
            $service_category->code = $request->code ?? $service_category->code;
            $service_category->description_en = $request->description_en ?? $service_category->description_en;
            $service_category->description_bn = $request->description_bn ?? $service_category->description_bn;

            // Image upload
             if ($request->hasFile('image')) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('image'),
                    'heros'
                );

                // DB columns
                $service_category->image = $imagePath;
                $service_category->image_url = asset('storage/' . $imagePath);
            }

            $service_category->status = $request->status;

            $service_category->save();

            activity('Service Category Update')
                ->performedOn($service_category)
                ->causedBy($service_category)
                ->withProperties(['attributes' => $request->all()])
                ->log('Service Category Update Successful');

            DB::commit();

            return new ServiceCategoryResource($service_category);
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            
            $service_category = ServiceCategory::findOrFail($id);

            $service_category->delete();

            activity('Service Category Delete')
                ->performedOn($service_category)
                ->causedBy($service_category)
                ->withProperties(['attributes' => ['id' => $id]])
                ->log('Service Category Delete Successful');

            DB::commit();

            return response()->json(['message' => 'Service Category deleted successfully.'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function statusUpdate($id)
    {
        DB::beginTransaction();

        try {
            
            $service_category = ServiceCategory::findOrFail($id);

            $service_category->status = !$service_category->status;

            $service_category->save();

            activity('Service Category Status Update')
                ->performedOn($service_category)
                ->causedBy($service_category)
                ->withProperties(['attributes' => ['id' => $id, 'status' => $service_category->status]])
                ->log('Service Category Status Update Successful');

            DB::commit();

            return new ServiceCategoryResource($service_category);
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}