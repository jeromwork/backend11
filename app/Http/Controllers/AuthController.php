<?php

namespace App\Http\Controllers;

use App\Services\Response\ResponseService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Modules\Profile\Entities\SmsVerification;
use Modules\Profile\Http\Requests\GetSmsRequest;
use Modules\Profile\Http\Requests\LoginByPhone;
use Modules\Profile\Services\SmsVerificationService;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth:api', ['except' => ['login']]);
    }

    // Register a new user
    public function register(Request  $request)
    {
        // Validation logic here...
        User::create([
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);
        $user = new User($request->all());
        $user->save();

        // Create a JWT token for the user and return it
        $token = auth()->login($user);

        return $this->respondWithToken($token);
    }

    public function sendSms(GetSmsRequest $request)    {
        try {
            $smsVerificationService = new SmsVerificationService($request->input('phone'));
            if($smsVerificationService->sendSms()){
                return ResponseService::ok(['message'=>'Смс отправлено', 'smsTimeout' => $smsVerificationService->getSmsTimeout() ]);
            }
        }catch (\Exception $e){
            //todo @telegram error
            return ResponseService::error($e->getMessage());
        }



    }

    /** Get a JWT via given credentials.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginByPhone(LoginByPhone $request)
    {

        if(!(new SmsVerificationService($request->input('phone')))->checkCode($request->input('code'))){
            return ResponseService::error('Incorrect code', 200);
        }
        $user = User::where('phone_number', $request->input('phone'))->first();
        if(!$user){
            $user = User::create([
                'phone_number' =>$request->input('phone'),
                'password' => Hash::make(Str::random(8)),
            ]);
        }


        // Create a JWT token for the user and return it
        $token = auth()->login($user);

        return $this->respondWithToken($token);
    }

    /** Get a JWT via given credentials.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        if (! $token = auth()->attempt($request->all())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'ok' => true,
            'jwt' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    private function guard()
    {
        return Auth::guard();
    }
}
