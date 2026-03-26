<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
        
        $user = User::withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
            ->where('email', $request->email)
            ->first();

        //Check Password
        if (!$user || ! Hash::check($request->password, $user->password)){
            throw ValidationException::withMessages([
                'email' => ['the credentials provided are incorrect'],
            ]);
        }

        //Create a token named 'auth_token'
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }
}
