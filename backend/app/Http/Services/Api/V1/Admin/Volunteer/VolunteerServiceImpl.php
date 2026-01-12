<?php

namespace App\Http\Services\Api\V1\Admin\Volunteer;

use App\Http\Resources\Api\V1\Admin\volunteerResource;
use App\Models\volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VolunteerServiceImpl implements VolunteerService
{
    public function index(Request $request)
    {
        $volunteer = volunteer::with('user', 'createdBy');

        // Sorting (secure)
        $sortableColumns = ['id', 'blood_group', 'emergency_contact', 'volunteer_since', 'status', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $volunteer->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $volunteer->where('blood_group', 'like', "%{$search}%")
                ->orWhere('emergency_contact', 'like', "%{$search}%")
                ->orWhere('volunteer_since', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $volunteers = $volunteer->paginate($itemsPerPage);

        return volunteerResource::collection($volunteers);
    }

    public function getAllVolunteers()
    {
        $volunteer = volunteer::with('user', 'createdBy')->latest()->get();

        return volunteerResource::collection($volunteer);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            
            $volunteer = new Volunteer();

            // Assign fields
            $volunteer->user_id = $request->user_id;
            $volunteer->volunteer_id = $request->volunteer_id;
            $volunteer->designation = $request->designation;
            $volunteer->specialization = $request->specialization;
            $volunteer->education = $request->education;
            $volunteer->profession = $request->profession;
            $volunteer->blood_group = $request->blood_group;
            $volunteer->emergency_contact = $request->emergency_contact;
            $volunteer->availability = $request->availability;
            $volunteer->skills = $request->skills;
            $volunteer->languages_spoken = $request->languages_spoken;
            $volunteer->volunteer_since = $request->volunteer_since;
            $volunteer->status = $request->status ?? 'active';
            $volunteer->rating = $request->rating;
            $volunteer->total_tasks_completed = $request->total_tasks_completed ?? 0;

            $volunteer->save();

            // Activity log (optional)
            if (function_exists('activity')) {
                activity('Volunteer store')
                    ->performedOn($volunteer)
                    ->causedBy(Auth::user())
                    ->withProperties(['attributes' => $request->all()])
                    ->log('Volunteer store successful');
            }

            DB::commit();

            return new VolunteerResource($volunteer);
        
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $volunteer = volunteer::with('user', 'createdBy')->findOrFail($id);

        return new volunteerResource($volunteer);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {

            $volunteer = Volunteer::findOrFail($id);

            // Partial update - assign only provided fields
            if ($request->has('user_id')) $volunteer->user_id = $request->user_id;
            if ($request->has('volunteer_id')) $volunteer->volunteer_id = $request->volunteer_id;
            if ($request->has('designation')) $volunteer->designation = $request->designation;
            if ($request->has('specialization')) $volunteer->specialization = $request->specialization;
            if ($request->has('education')) $volunteer->education = $request->education;
            if ($request->has('profession')) $volunteer->profession = $request->profession;
            if ($request->has('blood_group')) $volunteer->blood_group = $request->blood_group;
            if ($request->has('emergency_contact')) $volunteer->emergency_contact = $request->emergency_contact;
            if ($request->has('availability')) $volunteer->availability = $request->availability;
            if ($request->has('skills')) $volunteer->skills = $request->skills;
            if ($request->has('languages_spoken')) $volunteer->languages_spoken = $request->languages_spoken;
            if ($request->has('volunteer_since')) $volunteer->volunteer_since = $request->volunteer_since;
            if ($request->has('status')) $volunteer->status = $request->status;
            if ($request->has('rating')) $volunteer->rating = $request->rating;
            if ($request->has('total_tasks_completed')) $volunteer->total_tasks_completed = $request->total_tasks_completed;

            $volunteer->save();

            // Activity log
            if (function_exists('activity')) {
                activity('Volunteer update')
                    ->performedOn($volunteer)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'old' => $volunteer->getOriginal(),
                        'attributes' => $request->all()
                    ])
                    ->log('Volunteer updated successfully');
            }

            DB::commit();

            return new VolunteerResource($volunteer);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            
            $volunteer = Volunteer::findOrFail($id);

            $volunteer->delete();

            activity('Volunteer Delete')
                ->performedOn($volunteer)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => ['id' => $id]])
                ->log('Volunteer Delete Successful');    

            DB::commit();

            return true;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}