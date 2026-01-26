<?php

namespace App\Jobs;

use App\Models\PollOption;
use App\Models\PollVote;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class PollVotedJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public int $pollId;
    public int $optionId;
    public ?int $userId;


    /**
     * Create a new job instance.
     */
    public function __construct(int $pollId, int $optionId, ?int $userId)
    {
        $this->pollId   = $pollId;
        $this->optionId = $optionId;
        $this->userId   = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {

            PollVote::create([
                'poll_id'   => $this->pollId,
                'option_id' => $this->optionId,
                'user_id'   => $this->userId,
            ]);

            PollOption::where('id', $this->optionId)
                ->increment('vote_count');
        });
    }
}