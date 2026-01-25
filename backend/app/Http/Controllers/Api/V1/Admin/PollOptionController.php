<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\PollOptionRequest;
use App\Http\Services\Api\V1\Admin\PollOption\PollOptionService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PollOptionController extends Controller
{
    protected PollOptionService $pollOptionService;

    public function __construct(PollOptionService $pollOptionService)
    {
        $this->pollOptionService = $pollOptionService;
    }

    public function index(Request $request)
    {
        //Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch poll options via service
        $poll_options = $pagination
            ? $this->pollOptionService->index($request)
            : $this->pollOptionService->getAllPollOptions();


        // Return unified response
        $message = $pagination
            ? "All poll options fetched successfully with pagination"
            : "All poll options fetched successfully";

        return GlobalResponse::success($poll_options, $message, Response::HTTP_OK);
    }

    public function store(PollOptionRequest $request)
    {
        //return $request->all();
        
        try {
           $poll_option = $this->pollOptionService->store($request);

           return GlobalResponse::success($poll_option, "Poll Option Store successful", Response::HTTP_CREATED);

        } catch (ValidationException $exception) {

            return GlobalResponse::error($exception->errors(), $exception->getMessage(), $exception->getCode());

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getCode());
        }
    }

    public function show(int $id)
    {
         try {

            $poll_option = $this->pollOptionService->show($id);

            return GlobalResponse::success($poll_option, "Poll Option fetch successful", Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Poll Option not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(PollOptionRequest $request, $id)
    {
        try {

            $poll_option = $this->pollOptionService->update($request, $id);

            return GlobalResponse::success($poll_option, "Poll Option update successful", \Illuminate\Http\Response::HTTP_OK);

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

            $this->pollOptionService->destroy($id);

            return GlobalResponse::success("", "Poll Option delete successful", \Illuminate\Http\Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Poll Option not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeStatus(int $id)
    {
        try {
            $poll_option = $this->pollOptionService->changeStatus($id);

            return GlobalResponse::success($poll_option, "Poll Option status updated successfully", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Poll Option not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);
            
        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}