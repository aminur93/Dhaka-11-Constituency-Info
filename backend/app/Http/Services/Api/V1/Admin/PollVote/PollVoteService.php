<?php

namespace App\Http\Services\Api\V1\Admin\PollVote;

use Illuminate\Http\Request;

interface PollVoteService
{
    public function vote(int $pollId, int $optionId, ?int $userId): void;

    public function alreadyVoted(int $pollId, ?int $userId): bool;

    public function index(Request $request);

    public function getAllPollVotes();

    public function show(int $id);

    public function update(Request $request, int $id);

    public function destroy(int $id): void;
}