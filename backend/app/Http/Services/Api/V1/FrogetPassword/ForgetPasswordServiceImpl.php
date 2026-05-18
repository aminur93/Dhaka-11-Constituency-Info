<?php

namespace App\Http\Services\Api\V1\FrogetPassword;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class ForgetPasswordServiceImpl implements ForgetPasswordService
{

    public function sendResetLink(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new \DomainException('No account found with this email.', 404);
        }

        $token = Password::createToken($user);

        Mail::to($user->email)->send(new ResetPasswordMail($token, $user->email));
    }

    public function resetPassword(string $token, string $email, string $password): void
    {
        $status = Password::reset(
            ['email' => $email, 'token' => $token, 'password' => $password],
            function (User $user, string $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new \DomainException('Invalid or expired reset token.', 400);
        }
    }
}
