<?php

namespace App\Http\Services\Api\V1\Admin\PollOption;

use App\Http\Resources\Api\V1\Admin\PollOptionResource;
use App\Models\PollOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PollOptionServiceImpl implements PollOptionService
{
    public function index(Request $request)
    {
        $poll_option = PollOption::with('poll', 'createdBy', 'updatedBy');

        // Sorting (secure)
        $sortableColumns = ['id', 'option_text_en', 'option_text_bn', 'status','created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $poll_option->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $poll_option->where('option_text_en', 'like', "%{$search}%")
                ->orWhere('option_text_bn', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $poll_options = $poll_option->paginate($itemsPerPage);

        return PollOptionResource::collection($poll_options);
    }

    public function getAllPollOptions()
    {
        $poll_options = PollOption::with('poll', 'createdBy', 'updatedBy')->latest()->get();

        return PollOptionResource::collection($poll_options);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $option = new PollOption();

            $option->poll_id = $request->poll_id;
            $option->option_text_en = $request->option_text_en;
            $option->option_text_bn = $request->option_text_bn;
            $option->display_order = $request->display_order ?? null;
            $option->vote_count = $request->vote_count ?? 0;
            $option->status = $request->status ?? true; // default true     
            $option->created_by = Auth::id() ?? null;

            $option->save();

            // Activity Log
            activity('Poll Option')
                ->performedOn($option)
                ->causedBy(Auth::user())
                ->withProperties([
                    'poll_id' => $option->poll_id,
                    'option_text' => $option->option_text,
                ])
                ->log('Poll option created');

            DB::commit();

            return new PollOptionResource($option);
            
        } catch (\Throwable $th) {
            
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $poll_option = PollOption::with('poll', 'createdBy', 'updatedBy')->findOrFail($id);

        return new PollOptionResource($poll_option);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            $option = PollOption::findOrFail($id);

            $option->poll_id = $request->poll_id;
            $option->option_text_en = $request->option_text_en ?? $option->option_text_en;
            $option->option_text_bn = $request->option_text_bn ?? $option->option_text_bn;
            $option->display_order = $request->display_order ?? null;
            $option->vote_count = $request->vote_count ?? 0;
            $option->status = $request->status ?? true; // default true     

            $option->updated_by = Auth::id() ?? null;

            // Activity Log
            activity('Poll Option')
                ->performedOn($option)
                ->causedBy(Auth::user())
                ->withProperties([
                    'updated_fields' => $request->only([
                        'poll_id',
                        'option_text',
                        'option_text_bn',
                        'display_order',
                    ]),
                ])
                ->log('Poll option updated');

            DB::commit();

            return new PollOptionResource($option);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            
            $option = PollOption::findOrFail($id);

            // Activity Log
            activity('Poll Option')
                ->performedOn($option)
                ->causedBy(Auth::user())
                ->withProperties([
                    'poll_option_id' => $option->id,
                    'option_text' => $option->option_text,
                ])
                ->log('Poll option deleted');

            $option->delete(); 

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
            
            $option = PollOption::findOrFail($id);

            $option->status = ! $option->status;
            $option->save();

            // Activity Log
            activity('Poll Option')
                ->performedOn($option)
                ->causedBy(Auth::user())
                ->withProperties([
                    'id' => $id,
                    'poll_id'   => $option->poll_id
                ])
                ->log('Poll option status changed');

            DB::commit();

            return new PollOptionResource($option);

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

}