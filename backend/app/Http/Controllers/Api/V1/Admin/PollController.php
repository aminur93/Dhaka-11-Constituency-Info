<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\PollRequest;
use App\Http\Services\Api\V1\Admin\Poll\PollService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PollController extends Controller
{
    protected PollService $pollService;

    public function __construct(PollService $pollService)
    {
        $this->pollService = $pollService;
    }

    public function index(Request $request)
    {
        //Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch poll via service
        $polls = $pagination
            ? $this->pollService->index($request)
            : $this->pollService->getAllPolls();


        // Return unified response
        $message = $pagination
            ? "All polls fetched successfully with pagination"
            : "All polls fetched successfully";

        return GlobalResponse::success($polls, $message, Response::HTTP_OK);
    }

    public function store(PollRequest $request)
    {
        try {
           $poll = $this->pollService->store($request);

           return GlobalResponse::success($poll, "Poll Store successful", Response::HTTP_CREATED);

        } catch (ValidationException $exception) {

            return GlobalResponse::error($exception->errors(), $exception->getMessage(), $exception->getCode());

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getCode());
        }
        
    }

    public function show($id)
    {
        try {

            $poll = $this->pollService->show($id);

            return GlobalResponse::success($poll, "Poll fetch successful", Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Poll not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(PollRequest $request, $id)
    {
        try {

            $poll = $this->pollService->update($request, $id);

            return GlobalResponse::success($poll, "Poll update successful", \Illuminate\Http\Response::HTTP_OK);

        }catch (ValidationException $exception){

            return GlobalResponse::error($exception->errors(), $exception->getResponse(), $exception->status);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Poll not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {

            $this->pollService->destroy($id);

            return GlobalResponse::success("", "Poll delete successful", \Illuminate\Http\Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Poll not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeStatus(int $id)
    {
        try {
            $poll = $this->pollService->changeStatus($id);

            return GlobalResponse::success($poll, "Poll status updated successfully", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Poll not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);
            
        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}