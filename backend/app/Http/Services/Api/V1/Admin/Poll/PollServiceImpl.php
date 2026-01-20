<?php

namespace App\Http\Services\Api\V1\Admin\Poll;

use App\Http\Resources\Api\V1\Admin\PollResource;
use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PollServiceImpl implements PollService
{
    public function index(Request $request)
    {
        $poll = Poll::with('ward', 'thana', 'createdBy', 'updatedBy');

         // Sorting (secure)
        $sortableColumns = ['id', 'title_en', 'title_bn', 'poll_type', 'start_date', 'end_date', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $poll->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $poll->where('title_en', 'like', "%{$search}%")
                ->orWhere('title_bn', 'like', "%{$search}%")
                ->orWhere('poll_type', 'like', "%{$search}%")
                ->orWhere('start_date', 'like', "%{$search}%")
                ->orWhere('end_date', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $polls = $poll->paginate($itemsPerPage);

        return PollResource::collection($polls);
    }

    public function getAllPolls()
    {
        $polls = Poll::with('ward', 'thana', 'createdBy', 'updatedBy')->latest()->get();

        return PollResource::collection($polls);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $poll = new Poll();

            $poll->title_en = $request->title_en;
            $poll->title_bn = $request->title_bn;
            $poll->description_en = $request->description_en;
            $poll->description_bn = $request->description_bn;

            $poll->poll_type = $request->poll_type;
            $poll->target_audience = $request->target_audience;

            $poll->ward_id = $request->ward_id;
            $poll->thana_id = $request->thana_id;

            $poll->is_anonymous = $request->boolean('is_anonymous', false);
            $poll->allow_multiple_votes = $request->boolean('allow_multiple_votes', false);
            $poll->status = $request->boolean('is_active', true);

            $poll->start_date = $request->start_date ?? now();
            $poll->end_date = $request->end_date;

            $poll->created_by = Auth::id() ?? null;

            $poll->save();

            // Activity Log
            activity('Poll')
                ->performedOn($poll)
                ->causedBy(Auth::user())
                ->withProperties([
                    'poll_id' => $poll->id,
                    'title' => $poll->title,
                ])
                ->log('Poll created successfully');

            DB::commit();

            return new PollResource($poll);
            
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $poll = Poll::with('ward', 'thana', 'createdBy', 'updatedBy')->findOrFail($id);

        return new PollResource($poll);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            
            $poll = Poll::findOrFail($id);

            $poll->title_en = $request->has('title_en') ? $request->title_en : $poll->title_en;
            $poll->title_bn = $request->has('title_bn') ? $request->title_bn : $poll->title_bn;
            $poll->description_en = $request->has('description_en') ? $request->description_en : $poll->description_en;
            $poll->description_bn = $request->has('description_bn') ? $request->description_bn : $poll->description_bn;

            $poll->poll_type = $request->has('poll_type') ? $request->poll_type : $poll->poll_type;
            $poll->target_audience = $request->has('target_audience') ? $request->target_audience : $poll->target_audience;

            $poll->ward_id = $request->has('ward_id') ? $request->ward_id : $poll->ward_id;
            $poll->thana_id = $request->has('thana_id') ? $request->thana_id : $poll->thana_id;

            if ($request->has('is_anonymous')) {
                $poll->is_anonymous = $request->boolean('is_anonymous');
            }

            if ($request->has('allow_multiple_votes')) {
                $poll->allow_multiple_votes = $request->boolean('allow_multiple_votes');
            }

            if ($request->has('is_active')) {
                $poll->is_active = $request->boolean('is_active');
            }

            $poll->start_date = $request->has('start_date')
                ? $request->start_date
                : $poll->start_date;

            $poll->end_date = $request->has('end_date')
                ? $request->end_date
                : $poll->end_date;

            $poll->save();

            // Activity Log
            activity('Poll')
                ->performedOn($poll)
                ->causedBy(Auth::user())
                ->withProperties([
                    'poll_id' => $poll->id,
                    'updated_fields' => array_keys($request->all()),
                ])
                ->log('Poll updated successfully');

            DB::commit();

            return new PollResource($poll);
            
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            
            $poll = Poll::findOrFail($id);

            $poll->delete(); 

            // Activity Log
            activity('Poll')
                ->performedOn($poll)
                ->causedBy(Auth::user())
                ->withProperties([
                    'poll_id' => $poll->id,
                    'title_en' => $poll->title_en,
                ])
                ->log('Poll deleted successfully');

            DB::commit();

            return true;
            
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function changeStatus(int $id)
    {
        DB::beginTransaction();

        try {
            $poll = Poll::findOrFail($id);

            // Toggle status
            $poll->status = ! $poll->status;
            $poll->save();

            // Activity Log
            activity('Poll')
                ->performedOn($poll)
                ->causedBy(Auth::user())
                ->withProperties([
                    'poll_id' => $poll->id,
                    'new_status' => $poll->status ? 1 : 0,
                ])
                ->log('Poll status changed');

            DB::commit();

            return new PollResource($poll);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}