<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
$fe = Auth::guard('admin');

            if($admin = $fe->user()){
                return response()->json([
                    'ok' => true,
                    'api_token' => $admin->createToken('control')->plainTextToken,
                    'token_type' => 'bearer',
                    'isAdmin' => true,
                    'expires_in' => auth()->factory()->getTTL() * 60
                ]);
            }
            else{
                return response()->json(['error' => 'Неправильный логин или пароль'], 401);
            }


            if (!$token = Auth::guard('admin')->attempt($credentials)) {
                return response()->json(['error' => 'Неправильный логин или пароль'], 401);
            }

            if(!$admin =  Auth::guard('admin')->user()){
                return response()->json(['error' => 'Неправильный логин или пароль'], 401);
            }
            $token = Auth::guard('admin')->claims(['roles' => ['admin']])->attempt($credentials);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not create token'.$e->getMessage()], 500);
        }
        return $this->respondWithToken($token);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function profile()
    {
        $user = Auth::guard('admin')->user();
        return response()->json($user);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */


    public function refresh():JsonResponse
    {
        try {
            $newToken = Auth::guard('admin')->refresh();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not refresh token'], 500);
        }
        return $this->respondWithToken(compact('newToken'));
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'ok' => true,
            'jwtToken' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
