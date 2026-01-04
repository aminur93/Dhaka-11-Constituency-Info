<?php

namespace App\Http\Services\Api\V1\Admin\AreaDemographic;

use App\Http\Resources\Api\V1\Admin\AreaDemographicResource;
use App\Models\AreaDemographic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AreaDemographicServiceImpl implements AreaDemographicService
{
    public function index(Request $request)
    {
        $area_demographic = AreaDemographic::with('ward', 'thana', 'user');

        // Sorting (secure)
        $sortableColumns = ['id', 'ward_id', 'thana_id', 'total_population', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $area_demographic->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $area_demographic->where('total_population', 'like', "%{$search}%")
                ->orWhere('male_population', 'like', "%{$search}%")
                ->orWhere('female_population', 'like', "%{$search}%")
                ->orWhere('age_0_18', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $area_demographics = $area_demographic->paginate($itemsPerPage);

        return AreaDemographicResource::collection($area_demographics);
    }

    public function getAllAreaDemographics()
    {
        $area_demographics = AreaDemographic::with('ward', 'thana', 'user')->latest()->get();
        return AreaDemographicResource::collection($area_demographics);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $area_demographic = new AreaDemographic();

            $area_demographic->ward_id = $request->ward_id;
            $area_demographic->thana_id = $request->thana_id;
            $area_demographic->total_population = $request->total_population;
            $area_demographic->male_population = $request->male_population;
            $area_demographic->female_population = $request->female_population;
            $area_demographic->age_0_18 = $request->age_0_18;
            $area_demographic->age_19_35 = $request->age_19_35;
            $area_demographic->age_36_60 = $request->age_36_60;
            $area_demographic->age_above_60 = $request->age_above_60;
            $area_demographic->total_voters = $request->total_voters;
            $area_demographic->literacy_rate = $request->literacy_rate;
            $area_demographic->avg_income = $request->avg_income;
            $area_demographic->updated_year = $request->updated_year;

            $area_demographic->created_by = Auth::id() ? $request->created_by : null;

            $area_demographic->save();

            activity('Area Demographic Store')
            ->performedOn($area_demographic)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => $request->all()])
            ->log('Area Demographic Store Successful');

            DB::commit();

            return new AreaDemographicResource($area_demographic);

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $area_demographic = AreaDemographic::with('ward', 'thana', 'user')->findOrFail($id);

        return new AreaDemographicResource($area_demographic);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {

            $area_demographic = AreaDemographic::findOrFail($id);

            $area_demographic->ward_id = $request->ward_id ?? $area_demographic->ward_id;
            $area_demographic->thana_id = $request->thana_id ?? $area_demographic->thana_id;
            $area_demographic->total_population = $request->total_population ?? $area_demographic->total_population;
            $area_demographic->male_population = $request->male_population ?? $area_demographic->male_population;
            $area_demographic->female_population = $request->female_population ?? $area_demographic->female_population;
            $area_demographic->age_0_18 = $request->age_0_18 ?? $area_demographic->age_0_18;
            $area_demographic->age_19_35 = $request->age_19_35 ?? $area_demographic->age_19_35;
            $area_demographic->age_36_60 = $request->age_36_60 ?? $area_demographic->age_36_60;
            $area_demographic->age_above_60 = $request->age_above_60 ?? $area_demographic->age_above_60;
            $area_demographic->total_voters = $request->total_voters ?? $area_demographic->total_voters;
            $area_demographic->literacy_rate = $request->literacy_rate ?? $area_demographic->literacy_rate;
            $area_demographic->avg_income = $request->avg_income ?? $area_demographic->avg_income;
            $area_demographic->updated_year = $request->updated_year ?? $area_demographic->updated_year;

            $area_demographic->created_by = Auth::id() ? $request->created_by : null;

            $area_demographic->save();

            activity('Area Demographic Update')
            ->performedOn($area_demographic)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => $request->all()])
            ->log('Area Demographic Update Successful');    

            DB::commit();

            return new AreaDemographicResource($area_demographic);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {

            $area_demographic = AreaDemographic::findOrFail($id);
            $area_demographic->delete();

            activity('Area Demographic Delete')
            ->performedOn($area_demographic)
            ->causedBy(Auth::user())
            ->withProperties(['attributes' => ['id' => $id]])
            ->log('Area Demographic Delete Successful');    

            DB::commit();

            return true;

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}