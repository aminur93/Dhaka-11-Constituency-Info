<?php

namespace App\Http\Services\Api\V1\Admin\VolunteerAreaAssignment;

use App\Http\Resources\Api\V1\Admin\VolunteerAreaAssignmentResource;
use App\Models\VolunteerAreaAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VolunteerAreaAssignmentServiceImpl implements VolunteerAreaAssignmentService
{
    public function index(Request $request)
    {
        $volunteer_area_assignment = VolunteerAreaAssignment::with('volunteer', 'ward', 'assignedBy', 'createdBy');

        // Sorting (secure)
        $sortableColumns = ['id', 'assigned_at', 'is_primary', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $volunteer_area_assignment->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $volunteer_area_assignment->where(function ($q) use ($search) {
                // Search in volunteer's emergency_contact
                $q->whereHas('volunteers', function ($vol) use ($search) {
                    $vol->where('emergency_contact', 'like', "%{$search}%");
                });

                // Search in ward name_en or name_bn
                $q->orWhereHas('wards', function ($ward) use ($search) {
                    $ward->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_bn', 'like', "%{$search}%");
                });

                // Optionally, search in volunteer_area_assignments table itself
                $q->orWhere('is_primary', 'like', "%{$search}%");
            });
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $volunteer_area_assignments = $volunteer_area_assignment->paginate($itemsPerPage);

        return VolunteerAreaAssignmentResource::collection($volunteer_area_assignments);
    }

    public function getAllVolunteerAreaAssignments()
    {
        $volunteer_area_assignments = VolunteerAreaAssignment::with('volunteer', 'ward', 'assignedBy', 'createdBy')->latest()->get();

        return VolunteerAreaAssignmentResource::collection($volunteer_area_assignments);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            // Create new assignment
            $assignment = new VolunteerAreaAssignment();

            $assignment->volunteer_id = $request->volunteer_id;
            $assignment->assigned_by = $request->assigned_by;
            $assignment->ward_id = $request->ward_id;
            $assignment->thana_id = $request->thana_id;
            $assignment->is_primary = $request->is_primary ?? false;
            $assignment->assigned_at = $request->assigned_at ?? now();
            $assignment->created_by = Auth::id();

            $assignment->save();

            // Activity log
            activity('Volunteer Area Assignment')
                ->performedOn($assignment)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Volunteer area assignment created successfully');

            DB::commit();

            return new VolunteerAreaAssignmentResource($assignment);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $volunteer_area_assignment = VolunteerAreaAssignment::with('volunteer', 'ward', 'assignedBy', 'createdBy')->findOrFail($id);

        return new VolunteerAreaAssignmentResource($volunteer_area_assignment);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            // Find the assignment or fail
            $assignment = VolunteerAreaAssignment::findOrFail($id);

            // Update fields if provided
            $assignment->volunteer_id = $request->volunteer_id ?? $assignment->volunteer_id;
            $assignment->assigned_by = $request->assigned_by ?? $assignment->assigned_by;
            $assignment->ward_id = $request->ward_id ?? $assignment->ward_id;
            $assignment->thana_id = $request->thana_id ?? $assignment->thana_id;
            $assignment->is_primary = $request->has('is_primary') ? $request->is_primary : $assignment->is_primary;
            $assignment->assigned_at = $request->assigned_at ?? $assignment->assigned_at;
            $assignment->created_by = $assignment->created_by ?? Auth::id();

            $assignment->save();

            // Activity log
            activity('Volunteer Area Assignment')
                ->performedOn($assignment)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Volunteer area assignment updated successfully');

            DB::commit();

            return new VolunteerAreaAssignmentResource($assignment);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            // Find the assignment or fail
            $assignment = VolunteerAreaAssignment::findOrFail($id);

            // Activity log before deleting
            activity('Volunteer Area Assignment')
                ->performedOn($assignment)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $assignment->toArray()])
                ->log('Volunteer area assignment deleted');

            // Delete the assignment
            $assignment->delete();

            DB::commit();

            return true;

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}