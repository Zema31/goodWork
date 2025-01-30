<?php

namespace App\Http\Controllers\Api;

use App\Services\UserService\UserService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $service = new UserService;
        $result = $service->loginUser($request->all());
        return $this->constractResponce($result);
    }

    public function signup(Request $request)
    {
        $service = new UserService;
        $result = $service->signupUser($request->all());
        return $this->constractResponce($result);
    }

    public function logout()
    {
        $service = new UserService;
        $result = $service->logoutUser();
        return $this->constractResponce($result);
    }
}
