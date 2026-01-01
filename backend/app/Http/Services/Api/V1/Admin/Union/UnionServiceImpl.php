<?php

namespace App\Http\Services\Api\V1\Admin\Union;

use App\Http\Resources\Api\V1\Admin\UnionResource;
use App\Models\Union;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UnionServiceImpl implements UnionService
{

    public function index(Request $request)
    {
        $union = Union::with('thana', 'user');

        // Sorting (secure)
        $sortableColumns = ['id', 'name_en', 'name_bn', 'code', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $union->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $union->where('name_en', 'like', "%{$search}%")
                ->orWhere('name_bn', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $unions = $union->paginate($itemsPerPage);

        return UnionResource::collection($unions);
    }

    public function getAllUnions()
    {
        $unions = Union::with('thana', 'user')->where('is_active', true)->get();

        return UnionResource::collection($unions);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $union = new Union();

            $union->thana_id = $request->thana_id;
            $union->name_en = $request->name_en;
            $union->name_bn = $request->name_bn;
            $union->code = $request->code;
            $union->is_active = $request->is_active;

            $union->created_by = Auth::id() ? $request->created_by : null;
            $union->save();

            activity('Union Store')
            ->performedOn($union)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => $request->all()])
            ->log('Union Store Successful');
            
            DB::commit();

            return new UnionResource($union);
            
        } catch (\Throwable $th) {

            DB::rollBack();
            
            throw $th;
        } 
    }

    public function show($id)
    {
        $union = Union::with('thana', 'user')->findOrFail($id);

        return new UnionResource($union);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            $union = Union::findOrFail($id);

            $union->thana_id = $request->thana_id ?? $union->thana_id;
            $union->name_en = $request->name_en ?? $union->name_en;
            $union->name_bn = $request->name_bn ?? $union->name_bn;
            $union->code = $request->code ?? $union->code;
            $union->is_active = $request->is_active ?? $union->is_active;

            $union->created_by = Auth::id() ? $request->created_by : null;

            $union->save();

            activity('Union Update')
            ->performedOn($union)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => $request->all()])
            ->log('Union Update Successful');
            
            DB::commit();

            return new UnionResource($union);
            
        } catch (\Throwable $th) {

            DB::rollBack();
            
            throw $th;
        } 
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $union = Union::findOrFail($id);
            $union->delete();

            activity('Union Delete')
            ->performedOn($union)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => ['id' => $id]])
            ->log('Union Delete Successful');
            
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

            $union = Union::findOrFail($id);
            $union->is_active = ! $union->is_active;
            $union->save();

            activity('Union Status Update')
            ->performedOn($union)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => ['id' => $id, 'is_active' => $union->is_active]])
            ->log('Union Status Update Successful');
            
            DB::commit();

            return new UnionResource($union);
            
        } catch (\Throwable $th) {

            DB::rollBack();
            
            throw $th;
        } 
    }
}