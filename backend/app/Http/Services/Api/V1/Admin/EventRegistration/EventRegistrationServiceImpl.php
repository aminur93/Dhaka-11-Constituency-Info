<?php

namespace App\Http\Services\Api\V1\Admin\EventRegistration;

use App\Http\Resources\Api\V1\Admin\EventRegistrationResource;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventRegistrationServiceImpl implements EventRegistrationService
{
    public function index(Request $request)
    {
        $event_registration = EventRegistration::with('event', 'user');

        // Sorting (secure)
        $sortableColumns = ['id', 'event_id', 'user_id', 'attendance_status',  'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $event_registration->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $event_registration->where(function ($q) use ($search) {

                // attendance_status search
                $q->where('attendance_status', 'like', "%{$search}%")

                // Event search (title_en, title_bn)
                ->orWhereHas('event', function ($eventQuery) use ($search) {
                    $eventQuery->where('title_en', 'like', "%{$search}%")
                                ->orWhere('title_bn', 'like', "%{$search}%");
                })

                // User search (name, email, phone)
                ->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                });
            });
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $event_registrations = $event_registration->paginate($itemsPerPage);

        return EventRegistrationResource::collection($event_registrations);
    }

    public function getAllEventRegistration()
    {
        $event_registrations = EventRegistration::with('event', 'user')->latest()->get();

        return EventRegistrationResource::collection($event_registrations);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $eventRegistration = new EventRegistration();

            $eventRegistration->event_id = $request->event_id;
            $eventRegistration->user_id  = $request->user_id ?? Auth::id();

            $eventRegistration->attendance_status =
                $request->attendance_status ?? 'registered';

            $eventRegistration->registered_at = now();

            // Activity Log
            activity('Event Registration')
                ->performedOn($eventRegistration)
                ->causedBy(Auth::id())
                ->withProperties([
                    'event_id' => $eventRegistration->event_id,
                    'user_id'  => $eventRegistration->user_id,
                    'attendance_status' => $eventRegistration->attendance_status
                ])
            ->log('User registered for the event successfully');

            $eventRegistration->save();

            DB::commit();

            return new EventRegistrationResource($eventRegistration);
            
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $event_registration = EventRegistration::with('event', 'user')->findOrFail($id);

        return new EventRegistrationResource($event_registration);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            
            $eventRegistration = EventRegistration::findOrFail($id);

            // Update only provided fields
            if ($request->has('attendance_status')) {
                $eventRegistration->attendance_status = $request->attendance_status;
            }

            if ($request->has('event_id')) {
                $eventRegistration->event_id = $request->event_id;
            }

            if ($request->has('user_id')) {
                $eventRegistration->user_id = $request->user_id;
            }

            $eventRegistration->save();

            // Activity Log
            activity('Event Registration')
                ->performedOn($eventRegistration)
                ->causedBy(Auth::id())
                ->withProperties([
                    'event_id' => $eventRegistration->event_id,
                    'user_id' => $eventRegistration->user_id,
                    'attendance_status' => $eventRegistration->attendance_status
                ])
                ->log('Event registration updated successfully');

            DB::commit();

            return new EventRegistrationResource($eventRegistration);
            
        } catch (\Throwable $th) {
            DB::rollBack();
            
            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            
            $eventRegistration = EventRegistration::findOrFail($id);

            activity('Event Registration')
                ->performedOn($eventRegistration)
                ->causedBy(Auth::id())
                ->withProperties([
                    'event_id' => $eventRegistration->event_id,
                    'user_id' => $eventRegistration->user_id,
                    'attendance_status' => $eventRegistration->attendance_status
                ])
                ->log('Event registration deleted successfully');

            $eventRegistration->delete();

            DB::commit();

            return true;
            
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function changeStatus(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            $eventRegistration = EventRegistration::findOrFail($id);

            $oldStatus = $eventRegistration->attendance_status;
            $eventRegistration->attendance_status = $request->attendance_status;
            $eventRegistration->save();

            // Activity log
            activity('Event Registration')
                ->performedOn($eventRegistration)
                ->causedBy(Auth::id())
                ->withProperties([
                    'old_status' => $oldStatus,
                    'new_status' => $request->attendance_status,
                    'event_id' => $eventRegistration->event_id,
                    'user_id' => $eventRegistration->user_id
                ])
                ->log('Event registration status changed');

            DB::commit();

            return new EventRegistrationResource($eventRegistration);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}