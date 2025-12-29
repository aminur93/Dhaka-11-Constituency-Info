<?php

namespace App\Http\Services\Api\V1\Admin\District;

use App\Http\Resources\Api\V1\Admin\DistrictResource;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DistrictServiceImpl implements DistrictService
{
    public function index(Request $request)
    {
        $district = District::with('division');

        // Sorting (secure)
        $sortableColumns = ['id', 'name_en', 'name_bn', 'code', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $district->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $district->where('name_en', 'like', "%{$search}%")
                ->orWhere('name_bn', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $districts = $district->paginate($itemsPerPage);

        return DistrictResource::collection($districts);
    }

    public function getAllDistricts()
    {
        $districts = District::where('is_active', true)->get();
        return DistrictResource::collection($districts);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $district = new District();

            $district->division_id = $request->division_id;
            $district->name_en = $request->name_en;
            $district->name_bn = $request->name_bn;
            $district->code = $request->code;
            $district->is_active = $request->is_active;

            $district->save();

            activity('District Store')
            ->performedOn($district)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => $request->all()])
            ->log('District Store Successful');

            DB::commit();

            return new DistrictResource($district);
            
        } catch (\Throwable $th) {

            DB::rollBack();
            
            throw $th;
        } 
    }

    public function show(int $id)
    {
        $district = District::findOrFail($id);
        return new DistrictResource($district);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            
            $district = District::findOrFail($id);

            $district->division_id = $request->division_id ?? $district->division_id;
            $district->name_en = $request->name_en ?? $district->name_en;
            $district->name_bn = $request->name_bn ?? $district->name_bn;
            $district->code = $request->code ?? $district->code;
            if ($request->has('is_active')) {
                $district->is_active = $request->is_active;
            }

            $district->save();

            activity('District Update')
            ->performedOn($district)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => $request->all()])
            ->log('District Update Successful');

            DB::commit();

            return new DistrictResource($district);
            
        } catch (\Throwable $th) {

            DB::rollBack();
            
            throw $th;
        } 
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            
            $district = District::findOrFail($id);

            $district->delete();

            activity('District Delete')
            ->performedOn($district)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => ['id' => $id]])
            ->log('District Delete Successful');

            DB::commit();

            return true;
            
        } catch (\Throwable $th) {

            DB::rollBack();
            
            throw $th;
        } 
    }

    public function updateStatus(int $id)
    {
        DB::beginTransaction();

        try {
            
            $district = District::findOrFail($id);

            $district->is_active = ! $district->is_active;

            $district->save();

            activity('District Status Update')
            ->performedOn($district)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => ['id' => $id, 'is_active' => $district->is_active]])
            ->log('District Status Update Successful');

            DB::commit();

            return new DistrictResource($district);
            
        } catch (\Throwable $th) {

            DB::rollBack();
            
            throw $th;
        } 
    }
}