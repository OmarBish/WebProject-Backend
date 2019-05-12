<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\Client;
use Validator;

use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:client');
    }
    public function resetEmail(Request $request){
        
        $input = $request->all();
        $validator = Validator::make($input, [
            //TODO set to active_url
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        
        $user = Client::where('email', $request->input('email'))->first();
        if(!isset($user)){
            return $this->sendError('invalid email', "email dosent exist");
        }
        $password = $request->password;
        $response = Password::reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );
        return $response == Password::PASSWORD_RESET
                    ? $this->sendResponse('', 'password successfuly changed')
                    : $this->sendError('invalid token', trans($response)); 
    }
}
