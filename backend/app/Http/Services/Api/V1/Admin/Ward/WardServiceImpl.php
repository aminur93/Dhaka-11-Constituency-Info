<?php

namespace App\Http\Services\Api\V1\Admin\Ward;

use App\Http\Resources\Api\V1\Admin\WardResource;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WardServiceImpl implements WardService
{
    public function index(Request $request)
    {
        $ward = Ward::with('thana', 'union', 'user');

        // Sorting (secure)
        $sortableColumns = ['id', 'name_en', 'name_bn', 'ward_number', 'area_type', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $ward->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $ward->where('name_en', 'like', "%{$search}%")
                ->orWhere('name_bn', 'like', "%{$search}%")
                ->orWhere('ward_number', 'like', "%{$search}%")
                ->orWhere('area_type', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $wards = $ward->paginate($itemsPerPage);

        return WardResource::collection($wards);
    }

    public function getAllWards()
    {
        $wards = Ward::with('thana', 'union', 'user')->where('is_active', true)->get();

        return WardResource::collection($wards);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $ward = new Ward();
            
            $ward->thana_id = $request->thana_id;
            $ward->union_id = $request->union_id;
            $ward->name_en = $request->name_en;
            $ward->name_bn = $request->name_bn;
            $ward->ward_number = $request->ward_number;
            $ward->area_type = $request->area_type;
            $ward->population_estimate = $request->population_estimate;
            $ward->total_households = $request->total_households;
            $ward->is_active = $request->is_active;

            $ward->created_by = Auth::id() ? $request->created_by : null;

            $ward->save();

            activity('Ward Store')
            ->performedOn($ward)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => $request->all()])
            ->log('Ward Store Successful');

            DB::commit();

            return new WardResource($ward);
        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        } 
    }

    public function show($id)
    {
        $ward = Ward::with('thana', 'union', 'user')->findOrFail($id);

        return new WardResource($ward);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            $ward = Ward::findOrFail($id);

            $ward->thana_id = $request->thana_id ?? $ward->thana_id;
            $ward->union_id = $request->union_id ?? $ward->union_id;
            $ward->name_en = $request->name_en ?? $ward->name_en;
            $ward->name_bn = $request->name_bn ?? $ward->name_bn;
            $ward->ward_number = $request->ward_number ?? $ward->ward_number;
            $ward->area_type = $request->area_type ?? $ward->area_type;
            $ward->population_estimate = $request->population_estimate ?? $ward->population_estimate;
            $ward->total_households = $request->total_households ?? $ward->total_households;
            $ward->is_active = $request->is_active ?? $ward->is_active;

            $ward->created_by = Auth::id() ? $request->created_by : null;

            $ward->save();

            activity('Ward Update')
            ->performedOn($ward)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => $request->all()])
            ->log('Ward Update Successful');

            DB::commit();

            return new WardResource($ward);
            
        } catch (\Throwable $th) {

            DB::rollBack();
            
            throw $th;
        } 
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $ward = Ward::findOrFail($id);
            $ward->delete();

            activity('Ward Delete')
            ->performedOn($ward)
            ->causedBy(Auth::user())
            ->withProperties(['id' => $id])
            ->log('Ward Delete Successful');

            DB::commit();

            return response()->noContent();
            
        } catch (\Throwable $th) {

            DB::rollBack();
            
            throw $th;
        } 
    }

    public function updateStatus($id)
    {
        DB::beginTransaction();

        try {

            $ward = Ward::findOrFail($id);
            $ward->is_active = ! $ward->is_active;
            $ward->save();

            activity('Ward Status Update')
            ->performedOn($ward)
            ->causedBy(Auth::user())
            ->withProperties(['id' => $id, 'is_active' => $ward->is_active])
            ->log('Ward Status Update Successful');

            DB::commit();

            return new WardResource($ward);
            
        } catch (\Throwable $th) {

            DB::rollBack();
            
            throw $th;
        } 
    }
}