<?php

namespace App\Http\Services\Api\V1\FrogetPassword;

interface ForgetPasswordService
{
    public function sendResetLink(string $email): void;
    public function resetPassword(string $token, string $email, string $password): void;
}
