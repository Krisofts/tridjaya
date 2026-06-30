<?php

namespace App\Http\Controllers\Api;

use App\Auth\Services\AuthorizationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private AuthorizationService $authorization
    ) {}


    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($request->only('email','password'))) {

            return response()->json([
                'success'=>false,
                'message'=>'Email atau password salah'
            ],401);
        }

        $user = Auth::user();

        $token = $user->createToken('android')->plainTextToken;

        return response()->json([
            'success'=>true,
            'token'=>$token,
            'user'=>$user
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'groups' => $this->authorization->getGroups($user),
                'permissions' => $this->authorization->getPermissions($user),
                'is_superadmin' => $this->authorization->isSuperadmin($user),
            ]
        ]);
    }
}