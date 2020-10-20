<?php

namespace App\Http\Controllers;

use App\Extensions\Response;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->all())) {
            return Response::error('User not found');
        }

        $token = Auth::user()->createToken('authToken')->accessToken;

        return Response::success([
            'access_token' => $token,
        ]);
    }

    public function register(LoginRequest $request)
    {
        $user = new User();
        $user->email = $request->get('email');
        $user->password = Hash::make($request->get('password'));
        $user->name = $request->get('email');

        $user->save();
    }
}
