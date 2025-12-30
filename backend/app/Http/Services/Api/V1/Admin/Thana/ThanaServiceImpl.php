<?php

namespace App\Http\Services\Api\V1\Admin\Thana;

use App\Http\Resources\Api\V1\Admin\ThanaResource;
use App\Models\Thana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ThanaServiceImpl implements ThanaService
{
    public function index(Request $request)
    {
        $thana = Thana::with('district');

        // Sorting (secure)
        $sortableColumns = ['id', 'name_en', 'name_bn', 'code', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $thana->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $thana->where('name_en', 'like', "%{$search}%")
                ->orWhere('name_bn', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $thanas = $thana->paginate($itemsPerPage);

        return ThanaResource::collection($thanas);
    }

    public function getAllThanas()
    {
        $thanas = Thana::where('is_active', true)->get();

        return ThanaResource::collection($thanas);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $thana = new Thana();

            $thana->district_id = $request->district_id;
            $thana->name_en = $request->name_en;
            $thana->name_bn = $request->name_bn;
            $thana->code = $request->code;
            $thana->constituency = $request->constituency;
            $thana->is_active = $request->is_active;

            $thana->created_by = Auth::id() ?? null;

            $thana->save();

            activity('Thana Store')
            ->performedOn($thana)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => $request->all()])
            ->log('Thana Store Successful');
            DB::commit();

            return new ThanaResource($thana);
            
        } catch (\Throwable $th) {

            DB::rollBack();
            
            throw $th;
        } 
    }

    public function show(int $id)
    {
        $thana = Thana::with('district')->findOrFail($id);

        return new ThanaResource($thana);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {

            $thana = Thana::findOrFail($id);

            $thana->district_id = $request->district_id ?? $thana->district_id;
            $thana->name_en = $request->name_en ?? $thana->name_en;
            $thana->name_bn = $request->name_bn ?? $thana->name_bn;
            $thana->code = $request->code ?? $thana->code;
            $thana->constituency = $request->constituency ?? $thana->constituency;
            $thana->is_active = $request->is_active ?? $thana->is_active;

            $thana->created_by = $thana->created_by ? Auth::id() : null;

            $thana->save();

            activity('Thana Update')
            ->performedOn($thana)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => $request->all()])
            ->log('Thana Update Successful');

            DB::commit();

            return new ThanaResource($thana);
            
        } catch (\Throwable $th) {

            DB::rollBack();
            
            throw $th;
        } 
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {

            $thana = Thana::findOrFail($id);
            $thana->delete();

            activity('Thana Delete')
            ->performedOn($thana)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => $thana])
            ->log('Thana Delete Successful');

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

            $thana = Thana::findOrFail($id);
            $thana->is_active = ! $thana->is_active;
            $thana->save();

            activity('Thana Status Update')
            ->performedOn($thana)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => ['id' => $id, 'is_active' => $thana->is_active]])
            ->log('Thana Status Update Successful');

            DB::commit();

            return new ThanaResource($thana);
            
        } catch (\Throwable $th) {

            DB::rollBack();
            
            throw $th;
        } 
    }
}