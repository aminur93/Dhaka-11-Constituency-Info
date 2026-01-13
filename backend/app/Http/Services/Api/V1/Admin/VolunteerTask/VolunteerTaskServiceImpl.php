<?php

namespace App\Http\Services\Api\V1\Admin\VolunteerTask;

use App\Http\Resources\Api\V1\Admin\VolunteerTaskResource;
use App\Models\VolunteerTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VolunteerTaskServiceImpl implements VolunteerTaskService
{
    public function index(Request $request)
    {
        $volunteer_task = VolunteerTask::with('volunteer', 'assignedBy', 'serviceApplication', 'ward', 'createdBy');

         // Sorting (secure)
        $sortableColumns = ['id', 'task_number', 'volunteer_id', 'assigned_by', 'related_request_id', 'task_type', 'status', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $volunteer_task->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $volunteer_task->where('task_number', 'like', "%{$search}%")
                ->orWhere('task_type', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $volunteer_tasks = $volunteer_task->paginate($itemsPerPage);

        return VolunteerTaskResource::collection($volunteer_tasks);
    }

    public function getAllVolunteerTasks()
    {
        $volunteer_tasks = VolunteerTask::with('volunteer', 'assignedBy', 'serviceApplication', 'ward', 'createdBy')->latest()->get();

        return VolunteerTaskResource::collection($volunteer_tasks);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            // Create new Volunteer Task
            $task = new VolunteerTask();

            $task->task_number = $request->task_number;
            $task->volunteer_id = $request->volunteer_id;
            $task->assigned_by = $request->assigned_by;
            $task->related_request_id = $request->related_request_id;
            $task->task_type = $request->task_type;
            $task->title = $request->title;
            $task->description = $request->description;
            $task->priority = $request->priority ?? 'medium';
            $task->status = $request->status ?? 'assigned';

            $task->ward_id = $request->ward_id;
            $task->location_details = $request->location_details;
            $task->latitude = $request->latitude;
            $task->longitude = $request->longitude;
            $task->deadline = $request->deadline;

            $task->assigned_at = $request->assigned_at ?? now();
            $task->started_at = $request->started_at;
            $task->completed_at = $request->completed_at;

            $task->created_by = Auth::id() ?? null;

            $task->save();

            // Activity log
            activity('Volunteer Task')
                ->performedOn($task)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Volunteer task created successfully');

            DB::commit();

            return new VolunteerTaskResource($task);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $volunteer_tasks = VolunteerTask::with('volunteer', 'assignedBy', 'serviceApplication', 'ward', 'createdBy')->findOrFail($id);

        return new VolunteerTaskResource($volunteer_tasks);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            // Find the task or fail
            $task = VolunteerTask::findOrFail($id);

            // Update fields if provided
            $task->task_number = $request->task_number ?? $task->task_number;
            $task->volunteer_id = $request->volunteer_id ?? $task->volunteer_id;
            $task->assigned_by = $request->assigned_by ?? $task->assigned_by;
            $task->related_request_id = $request->related_request_id ?? $task->related_request_id;
            $task->task_type = $request->task_type ?? $task->task_type;
            $task->title = $request->title ?? $task->title;
            $task->description = $request->description ?? $task->description;
            $task->priority = $request->priority ?? $task->priority;
            $task->status = $request->status ?? $task->status;

            $task->ward_id = $request->ward_id ?? $task->ward_id;
            $task->location_details = $request->location_details ?? $task->location_details;
            $task->latitude = $request->latitude ?? $task->latitude;
            $task->longitude = $request->longitude ?? $task->longitude;
            $task->deadline = $request->deadline ?? $task->deadline;

            $task->assigned_at = $request->assigned_at ?? $task->assigned_at;
            $task->started_at = $request->started_at ?? $task->started_at;
            $task->completed_at = $request->completed_at ?? $task->completed_at;

            $task->created_by = $task->created_by ?? Auth::id();

            $task->save();

            // Activity log
            activity('Volunteer Task')
                ->performedOn($task)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Volunteer task updated successfully');

            DB::commit();

            return new VolunteerTaskResource($task);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            // Find the task or fail
            $task = VolunteerTask::findOrFail($id);

            // Log activity before deleting
            activity('Volunteer Task')
                ->performedOn($task)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $task->toArray()])
                ->log('Volunteer task deleted');

            // Delete the task
            $task->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Volunteer task deleted successfully'
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function changeStatus(Request $request, int $id)
    {
         DB::beginTransaction();

        try {
            // Find the task
            $task = VolunteerTask::findOrFail($id);

            $newStatus = $request->status;

            // Update status
            $task->status = $newStatus;

            // Update timestamps based on status
            switch ($newStatus) {
                case 'assigned':
                    $task->assigned_at = now();
                    $task->started_at = null;
                    $task->completed_at = null;
                    break;

                case 'in_progress':
                    $task->started_at = now();
                    // ensure assigned_at is set
                    $task->assigned_at = $task->assigned_at ?? now();
                    $task->completed_at = null;
                    break;

                case 'completed':
                    $task->completed_at = now();
                    $task->started_at = $task->started_at ?? now();
                    $task->assigned_at = $task->assigned_at ?? now();
                    break;

                case 'cancelled':
                    // optionally reset timestamps
                    $task->assigned_at = $task->assigned_at ?? now();
                    $task->started_at = null;
                    $task->completed_at = null;
                    break;
            }

            $task->save();

            // Activity log
            activity('Volunteer Task')
                ->performedOn($task)
                ->causedBy(Auth::user())
                ->withProperties([
                    'old_status' => $task->getOriginal('status'),
                    'new_status' => $newStatus,
                ])
                ->log('Volunteer task status changed');

            DB::commit();

            return new VolunteerTaskResource($task);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}