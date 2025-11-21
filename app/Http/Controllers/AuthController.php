<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\{
    ForgotPasswordRequest,
    LoginRequest,
    RegisterRequest,
    ResetPasswordRequest,
    VerifyAccountRequest,
    VerifyCodeRequest,
};

class AuthController extends Controller
{
    public function register(RegisterRequest $request) {
        return $request->store();
    }

    public function login(LoginRequest $request) {
        return $request->check();
    }

    public function logout() {
        $user = request()->user();
        $user->tokens()->delete();
        return $this->generalResponse(null, null, 200);
    }


    public function verify_account(VerifyAccountRequest $request) {
        return $request->verify_account();
    }

    public function forgot_password(ForgotPasswordRequest $request) {
        return $request->send_code();
    }

    public function reset_password(ResetPasswordRequest $request) {
        return $request->reset_password();
    }

    public function resend_code(ForgotPasswordRequest $request) {
        return $request->send_code();
    }

    public function verify_code(VerifyCodeRequest $request) {
        return $request->verify_code();
    }

    public function delete_account() {
        $user = request()->user();
        $user->delete();
        return $this->generalResponse(null, 'Deleted Successfully', 200);
    }
}
