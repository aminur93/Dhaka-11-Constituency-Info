<?php

namespace App\Http\Services\Api\V1\Admin\Event;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventServiceImpl implements EventService
{
    public function index(Request $request)
    {
        $event = Event::with('ward', 'thana', 'organizer', 'createdBy');

        // Sorting (secure)
        $sortableColumns = ['id', 'event_number',  'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $event->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $event->where('event_number', 'like', "%{$search}%")
                ->orWhere('title_en', 'like', "%{$search}%")
                ->orWhere('venue_en', 'like', "%{$search}%")
                ->orWhere('event_type', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $events = $event->paginate($itemsPerPage);

        return EventResource::collection($events);
    }

    public function getAllEvents()
    {
        $events = Event::with('ward', 'thana', 'organizer', 'createdBy')->latest()->get();

        return EventResource::collection($events);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $event = new Event();

            $event->event_number = $request->event_number;
            $event->title_en = $request->title_en;
            $event->title_bn = $request->title_bn;
            $event->description = $request->description;

            $event->event_type = $request->event_type;

            $event->venue_en = $request->venue_en;
            $event->venue_bn = $request->venue_bn;

            $event->ward_id = $request->ward_id;
            $event->thana_id = $request->thana_id;

            $event->latitude = $request->latitude;
            $event->longitude = $request->longitude;

            $event->start_datetime = $request->start_datetime;
            $event->end_datetime = $request->end_datetime;

            $event->organizer_id = $request->organizer_id ?? Auth::id();

            $event->max_participants = $request->max_participants;
            $event->registration_required = $request->registration_required ?? false;
            $event->registration_deadline = $request->registration_deadline;

            $event->status = $request->status ?? 'scheduled';

            if ($request->hasFile('banner_image')) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('banner_image'),
                    'banner-image'
                );

                // DB columns
                $event->banner_image = $imagePath;
                $event->banner_image_url = asset('storage/' . $imagePath);
            }

            $event->created_by = Auth::id() ?? null;

            $event->save();

            // Activity Log (optional but recommended)
            activity('Event')
                ->performedOn($event)
                ->causedBy(Auth::user())
                ->withProperties([
                    'event' => $event->toArray(),
                ])
                ->log('Event created successfully');

            DB::commit();

            return new EventResource($event);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $event = Event::with('ward', 'thana', 'organizer', 'createdBy')->findOrFail($id);

        return new EventResource($event);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {

            $event = Event::findOrFail($id);

            $event->event_number = $request->event_number ?? $event->event_number;
            $event->title_en = $request->title_en ?? $event->title_en;
            $event->title_bn = $request->title_bn ?? $event->title_bn;
            $event->description = $request->description ?? $event->description;

            $event->event_type = $request->event_type ?? $event->event_type;

            $event->venue_en = $request->venue_en ?? $event->venue_en;
            $event->venue_bn = $request->venue_bn ?? $event->venue_bn;

            $event->ward_id = $request->ward_id ?? $event->ward_id;
            $event->thana_id = $request->thana_id ?? $event->thana_id;

            $event->latitude = $request->latitude ?? $event->latitude;
            $event->longitude = $request->longitude ?? $event->longitude;

            $event->start_datetime = $request->start_datetime ?? $event->start_datetime;
            $event->end_datetime = $request->end_datetime ?? $event->end_datetime;

            $event->organizer_id = $request->organizer_id ?? $event->organizer_id;

            $event->max_participants = $request->max_participants ?? $event->max_participants;
            $event->registration_required = $request->registration_required ?? $event->registration_required;
            $event->registration_deadline = $request->registration_deadline ?? $event->registration_deadline;

            $event->status = $request->status ?? $event->status;

            // Banner Image Update
            if ($request->hasFile('banner_image')) {

                if($event->banner_image != null)
                {
                    ImageUpload::uploadImageApplicationStorage($event->banner_image);
                }

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('banner_image'),
                    'banner-image'
                );

                $event->banner_image = $imagePath;
                $event->banner_image_url = asset('storage/' . $imagePath);
            }

            $event->created_by = Auth::id() ?? $event->created_by;

            $event->save();

            // Activity Log
            activity('Event')
                ->performedOn($event)
                ->causedBy(Auth::user())
                ->withProperties([
                    'event' => $event->toArray(),
                ])
                ->log('Event updated successfully');

            DB::commit();

            return new EventResource($event);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {

            $event = Event::findOrFail($id);

            if ($event->banner_image) {
                
                ImageUpload::uploadImageApplicationStorage($event->banner_image);
            }

            // Activity log before delete
            activity('Event')
                ->performedOn($event)
                ->causedBy(Auth::user())
                ->withProperties([
                    'event' => $event->toArray(),
                ])
                ->log('Event deleted');

            $event->delete();

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
            $event = Event::findOrFail($id);

            $oldStatus = $event->status;
            $event->status = $request->status;

            // Optional: status wise time column update must
            if ($request->status === 'ongoing') {
                $event->start_datetime = $event->start_datetime ?? now();
            } elseif ($request->status === 'completed') {
                $event->end_datetime = $event->end_datetime ?? now();
            }

            $event->save();

            // Activity log
            activity('Event Status Change')
                ->performedOn($event)
                ->causedBy(Auth::user())
                ->withProperties([
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                ])
                ->log('Event status updated successfully');

            DB::commit();

            return new EventResource($event);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}