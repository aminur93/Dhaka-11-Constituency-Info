<?php

namespace App\Http\Services\Api\V1\Admin\Division;

use App\Http\Resources\Api\V1\Admin\DivisionResource;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DivisionServiceImpl implements DivisionService
{
    public function index(Request $request)
    {

        // 1 Boolean normalize
        $isActive = $request->has('is_active')
            ? filter_var($request->get('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            : null;

        $search = $request->get('search'); // string | null

        DB::beginTransaction();

        // 2 Call procedure
        DB::statement(
            'CALL sp_get_divisions(?, ?, ?)',
            [$isActive, $search, 'division_cursor']
        );

        // 3 Fetch cursor
        $divisions = DB::select('FETCH ALL FROM division_cursor');

        DB::commit();

        $collection = collect($divisions);

        // 4 Sorting
        $sortableColumns = ['id', 'name_en', 'name_bn', 'code', 'created_at'];

        $sortBy   = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $collection = $collection->sortBy($sortBy);

        if ($sortDesc) {
            $collection = $collection->reverse();
        }

        // 5 Manual pagination
        $perPage = (int) $request->get('itemsPerPage', 10);
        $page    = (int) $request->get('page', 1);

        $paginator = new LengthAwarePaginator(
            $collection->forPage($page, $perPage)->values(),
            $collection->count(),
            $perPage,
            $page,
            [
                'path'  => request()->url(),
                'query' => request()->query(),
            ]
        );

        return DivisionResource::collection($paginator);
    }

    public function getAllDivisions()
    {
        DB::beginTransaction();

        //Call procedure
        DB::statement("CALL sp_get_all_divisions(?)", ['division_cursor']);

        //Fetch cursor result
        $divisions = DB::select("FETCH ALL FROM division_cursor");

        DB::commit();

        //Return resource or collection
        return DivisionResource::collection(collect($divisions));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $division = new Division();
            
            $division->name_en = $request->name_en;
            $division->name_bn = $request->name_bn;
            $division->code = $request->code;
            $division->is_active = $request->is_active;

            $division->save();

            activity('Division Store')
            ->performedOn($division)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => $request->all()])
            ->log('Division Store Successful');

            DB::commit();

            return new DivisionResource($division);
            
        } catch (\Throwable $th) {

            DB::rollBack();
            
            throw $th;
        } 
    }

    public function show(int $id)
    {
        DB::beginTransaction();

        DB::statement(
            'CALL sp_get_division_by_id(?, ?)',
            [$id, 'division_cursor']
        );

        $rows = DB::select('FETCH ALL FROM division_cursor');

        DB::commit();

        if (empty($rows)) {
            abort(404, 'Division not found');
        }

        $division = $rows[0];

        return new DivisionResource($division);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            
            $division = Division::findOrFail($id);
            
            $division->name_en = $request->name_en ?? $division->name_en;
            $division->name_bn = $request->name_bn ?? $division->name_bn;
            $division->code = $request->code ?? $division->code;
            if ($request->has('is_active')) {
                $division->is_active = $request->is_active;
            }

            $division->save();

            activity('Division Update')
            ->performedOn($division)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => $request->all()])
            ->log('Division Update Successful');

            DB::commit();

            return new DivisionResource($division);
            
        } catch (\Throwable $th) {

            DB::rollBack();
            
            throw $th;
        } 
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            
            $division = Division::findOrFail($id);

            $division->delete();

            activity('Division Delete')
            ->performedOn($division)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => ['id' => $id]])
            ->log('Division Delete Successful');

            DB::commit();

            return true;
            
        } catch (\Throwable $th) {

            DB::rollBack();
            
            throw $th;
        } 
    }

    public function updateStatus(int $id, bool $status)
    {
        DB::beginTransaction();

        try {
            
            $division = Division::findOrFail($id);
            
            $division->is_active = $status;

            $division->save();

            activity('Division Status Update')
            ->performedOn($division)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => ['is_active' => $status]])
            ->log('Division Status Update Successful');

            DB::commit();

            return new DivisionResource($division);
            
        } catch (\Throwable $th) {

            DB::rollBack();
            
            throw $th;
        } 
    }
}