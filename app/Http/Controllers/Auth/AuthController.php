<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Concerns\ReturnsPlaceholderResponses;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    use ReturnsPlaceholderResponses;

    public function showLogin()
    {
        return $this->placeholder(__METHOD__);
    }

    public function login()
    {
        return $this->placeholder(__METHOD__);
    }

    public function showRegister()
    {
        return $this->placeholder(__METHOD__);
    }

    public function register()
    {
        return $this->placeholder(__METHOD__);
    }

    public function showForgotPassword()
    {
        return $this->placeholder(__METHOD__);
    }

    public function sendResetLink()
    {
        return $this->placeholder(__METHOD__);
    }

    public function showResetForm()
    {
        return $this->placeholder(__METHOD__);
    }

    public function resetPassword()
    {
        return $this->placeholder(__METHOD__);
    }

    public function logout()
    {
        return $this->placeholder(__METHOD__);
    }
}
