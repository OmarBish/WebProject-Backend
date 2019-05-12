<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use Socialite;
use Auth;
use Exception;


class GoogleAuthController extends Controller
{
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleProviderCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $existUser = Client::where('email', $googleUser->email)->first();

            if ($existUser) {
                Auth::loginUsingId($existUser->id);
            } else {
                $user = new Client;
                $user->name = $googleUser->name;
                $user->email = $googleUser->email;
                // $user->google_id = $googleUser->id;
                $user->password = md5(rand(1, 10000));
                $user->save();
                Auth::loginUsingId($user->id);
            }
            return $this->sendResponse("success","success");
        } catch (Exception $e) {
            return 'error';
        }
    }
}