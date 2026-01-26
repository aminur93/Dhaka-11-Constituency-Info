<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\PollVoteRequest;
use App\Http\Services\Api\V1\Admin\PollVote\PollVoteService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PollVoteController extends Controller
{
    protected PollVoteService $pollVoteService;

    public function __construct(PollVoteService $pollVoteService)
    {
        $this->pollVoteService = $pollVoteService;
    }

    public function index(Request $request)
    {
         //Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch poll vote options via service
        $poll_vote = $pagination
            ? $this->pollVoteService->index($request)
            : $this->pollVoteService->getAllPollVotes();


        // Return unified response
        $message = $pagination
            ? "All poll votes fetched successfully with pagination"
            : "All poll votes fetched successfully";

        return GlobalResponse::success($poll_vote, $message, Response::HTTP_OK);
    }

    public function store(PollVoteRequest $request)
    {
        if ($this->pollVoteService->alreadyVoted(
            $request->poll_id,
            $request->user_id ?? null
        )) {
            return GlobalResponse::error("",
                'You have already voted in this poll.',
                Response::HTTP_CONFLICT
            );
        }

        $this->pollVoteService->vote(
            $request->poll_id,
            $request->option_id,
            $request->user_id ?? null
        );

        return GlobalResponse::success("", "Vote submitted successfully and queued for processing", Response::HTTP_ACCEPTED);
    }

    public function show(int $id)
    {
        try {

            $poll_vote = $this->pollVoteService->show($id);

            return GlobalResponse::success($poll_vote, "Poll Vote fetch successful", Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Poll Vote not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);
        }catch (Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     public function update(PollVoteRequest $request, $id)
    {
        try {

            $poll_vote = $this->pollVoteService->update($request, $id);

            return GlobalResponse::success($poll_vote, "Poll Vote update successful", \Illuminate\Http\Response::HTTP_OK);

        }catch (ValidationException $exception){

            return GlobalResponse::error($exception->errors(), $exception->getResponse(), $exception->status);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Poll Option not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {

            $this->pollVoteService->destroy($id);

            return GlobalResponse::success("", "Poll Vote delete successful", \Illuminate\Http\Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Poll Vote not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);
            
        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}