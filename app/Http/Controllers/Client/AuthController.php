<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Validator;
use App\Client;
use Carbon\Carbon;


class AuthController extends Controller
{
    //TODO
    public function __construct(){
        $this->middleware('guest:client')->except('logout');
        $this->middleware('auth:client')->only('logout');
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
        
        if(!Auth::attempt($credentials)){
            return $this->sendError('Invalid credentials','Invalid credentials');
        }
        

        $user = $request->user();

        $tokenResult = $user->createToken('Personal Access Token',['client']);
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
            
        $response->withCookie(cookie('client_token', $token, 60));
        return $response;
    }
    public function register(Request $request)
    {
        return app('App\Http\Controllers\API\ClientController')->store($request);         
    }
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        
        return $this->sendResponse('logout succeded', 'Client logout successfully.');
    }
}
