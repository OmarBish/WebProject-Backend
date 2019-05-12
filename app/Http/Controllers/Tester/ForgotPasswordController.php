<?php

namespace App\Http\Controllers\Tester;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Tester;
use Mail;
use Illuminate\Mail\Message;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:client');
    }
    public function sendEmail(Request $request)
    {
        $user = Tester::where('email', $request->input('email'))->first();
        if(!isset($user)){
            return $this->sendError('invalid email', "email dosent exist");
        }
        
        $token = Password::getRepository()->create($user);

        Mail::raw($token, function (Message $message) use ($user) {
            $message->subject(config('app.name') . ' Password Reset Link');
            $message->to($user->email);
        });
        return $this->sendResponse('', 'password token successfuly send changed');
    }

}
