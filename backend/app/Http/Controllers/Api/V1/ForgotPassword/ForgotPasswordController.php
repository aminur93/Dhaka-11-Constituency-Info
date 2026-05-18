<?php

namespace App\Http\Controllers\Api\V1\ForgotPassword;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ForgotPassword\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\ResetPassword\ResetPasswordRequest;
use App\Http\Services\Api\V1\FrogetPassword\ForgetPasswordService;
use Illuminate\Http\Response;

class ForgotPasswordController extends Controller
{
    public function __construct(
        private ForgetPasswordService $forgetPasswordService
    ) {}

    public function sendResetLink(ForgotPasswordRequest $request)
    {
        try {
            $this->forgetPasswordService->sendResetLink($request->email);

            return GlobalResponse::success([], 'Reset link sent to your email.', Response::HTTP_OK);
        } catch (\DomainException $e) {
            return GlobalResponse::error('', $e->getMessage(), $e->getCode());
        } catch (\Throwable $e) {
            $code = ($e->getCode() >= 100 && $e->getCode() < 600) ? $e->getCode() : 500;
            return GlobalResponse::error('', $e->getMessage(), $code);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $this->forgetPasswordService->resetPassword(
                $request->token,
                $request->email,
                $request->password
            );

            return GlobalResponse::success([], 'Password reset successful.', Response::HTTP_OK);
        } catch (\DomainException $e) {
            return GlobalResponse::error('', $e->getMessage(), $e->getCode());
        } catch (\Throwable $e) {
            $code = ($e->getCode() >= 100 && $e->getCode() < 600) ? $e->getCode() : 500;
            return GlobalResponse::error('', $e->getMessage(), $code);
        }
    }
}
