<?php

namespace App\Http\Services\Api\V1\Admin\PollVote;

use App\Events\PollVotedEvent;
use App\Http\Resources\Api\V1\Admin\PollVoteResource;
use App\Models\PollVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PollVoteServiceImpl implements PollVoteService
{
    public function vote(int $pollId, int $optionId, ?int $userId): void
    {
        event(new PollVotedEvent($pollId, $optionId, $userId));
    }

    public function alreadyVoted(int $pollId, ?int $userId): bool
    {
        if (!$userId) {
            return false; // anonymous users handled differently
        }

        return PollVote::where('poll_id', $pollId)
            ->where('user_id', $userId)
            ->exists();
    }

    public function index(Request $request)
    {
        $poll_vote = PollVote::with('poll', 'option', 'user');

        // Sorting (secure)
        $sortableColumns = ['id', 'poll_id', 'option_id', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $poll_vote->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $poll_vote->where('poll_id', 'like', "%{$search}%")
                ->orWhere('option_id', 'like', "%{$search}%")
                ->orWhere('user_id', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $poll_votes = $poll_vote->paginate($itemsPerPage);

        return PollVoteResource::collection($poll_votes);
    }

    public function getAllPollVotes()
    {
        $poll_votes = PollVote::with('poll', 'option', 'user')->latest()->get();

        return PollVoteResource::collection($poll_votes);
    }

    public function show(int $id)
    {
        $poll_vote = PollVote::with('poll', 'option', 'user')->findOrFail($id);

        return new PollVoteResource($poll_vote);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            
            $poll_vote = PollVote::findOrFail($id);

            $poll_vote->poll_id = $request->poll_id ?? $poll_vote->poll_id;
            $poll_vote->option_id = $request->option_id ?? $poll_vote->option_id;
            $poll_vote->user_id = $request->user_id ?? $poll_vote->user_id;

            $poll_vote->save();

            activity('Poll Vote')
                ->performedOn($poll_vote)
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => $poll_vote->getAttributes(),
                ])
                ->log('Poll Vote updated');

            DB::commit();

            return new PollVoteResource($poll_vote);
            
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function destroy(int $id): void
    {
        DB::beginTransaction();

        try {
            
            $poll_vote = PollVote::findOrFail($id);
            $poll_vote->delete();

            activity('Poll Vote')
                ->performedOn($poll_vote)
                ->causedBy(Auth::user())
                ->withProperties([
                    'poll_vote_id' => $id,
                ])
                ->log('Poll Vote deleted');

            DB::commit();
            
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}