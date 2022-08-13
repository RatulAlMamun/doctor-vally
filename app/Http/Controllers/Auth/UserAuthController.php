<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegistrationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    public function register (UserRegistrationRequest $request)
    {
        $data = $request->all();
        $data['password'] = Hash::make($request->input('password'));

        $user = User::create($data);

        $token = $user->createToken('API Token');

        return response()->json([
            'error' => false,
            'message' => 'Registration Successfull.',
            'data' => [
                'accessToken' => $token->accessToken,
                'expiresAt' => $token->token->expires_at,
                'user' => $user,
            ],
        ]);
    }


    public function login (Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (! auth()->attempt($data)) {
            return response()->json([
                'error' => true,
                'message' => 'Credentials do not match. Please try again.',
            ]);
        }

        $token = auth()->user()->createToken('API Token');

        return response()->json([
            'error' => false,
            'message' => 'Login Successfull.',
            'data' => [
                'accessToken' => $token->accessToken,
                'expiresAt' => $token->token->expires_at,
                'user' => auth()->user(),
            ],
        ]);
    }
}
