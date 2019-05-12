<?php

namespace App\Http\Controllers\Tester;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tester;
use Auth;
use Validator;
use Carbon\Carbon;

class AuthController extends Controller
{
    //TODO
    public function __construct(){
            // $this->middleware('guest')->except();
            $this->middleware('guest:tester')->except('logout');
            $this->middleware('auth:api')->only('logout');
    }
    public function login(Request $request)
    {
        /**
         * 1- check validation
         * 2- login
         */
        $credentials = $request->only('email', 'password');
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        if(!Auth::guard('wTester')->attempt($credentials)){
            return $this->sendError('Invalid credentials','Invalid credentials');
        }

        $user = Auth::guard('wTester')->user();

        $tokenResult = $user->createToken('Personal Access Token',['tester']);
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addHours(1);
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);

        $token->save();
        $response= $this->sendResponse([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
            ],"Login successful");
            
        $response->withCookie(cookie('tester_token', $token, 60));
        return $response;

        
    }
    public function register(Request $request)
    {
        return app('App\Http\Controllers\API\TesterController')->store($request);
    }
    public function logout(Request $request)
    {
        auth()->guard('api')->user()->token()->revoke();
        return $this->sendResponse('logout succeded', 'Client logout successfully.');
    }
}
