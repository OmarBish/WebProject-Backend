<?php
use Illuminate\Http\Request;
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

    Route::prefix('client')->group(function () {
        /**
         * auth
         */
        //pass
        Route::post('login', 'Client\AuthController@login')->name('Client-Login');
        //pass
        Route::get('logout', 'Client\AuthController@logout')->name('Client-Logout');
        //pass
        Route::post('register', 'Client\AuthController@register')->name('Client-Logout');

        /**
         * resources
         */
        //pass
        Route::post('/createtask', 'Client\TestTaskController@create');
        Route::post('/reviewanswer', 'Client\TestTaskController@review');
        Route::post('/settaskactive', 'Client\TestTaskController@setActive')->name('Client-Login');
        Route::get('all', 'API\ClientController@all')->name('Client-Logout');

    });
    
    
    Route::prefix('tester')->group(function () {
        /**
         * auth
         */
        // Route::post('password/email', 'Tester\ForgotPasswordController@sendEmail')->name('password.email');
        // Route::post('password/reset', 'Tester\ResetPasswordController@resetEmail')->name('password.update');
        Route::post('login', 'Tester\AuthController@login')->name('Tester-Login');
        Route::post('/register', 'Tester\AuthController@register')->name('Tester-Register');
        Route::get('logout', 'Tester\AuthController@logout')->name('Tester-Logout');
        /**
         * resources
         */
        Route::get('all', 'API\TesterController@all');
        //pass
        Route::post('sendanswer', 'Tester\WebController@sendAnswer');
        //pass
        Route::post('addtest', 'Tester\WebController@addTest');

    });
    Route::get('logout', 'Tester\AuthController@logout')->name('Tester-Logout');
    Route::post('reset/email', 'Client\ForgotPasswordController@sendEmail');
    // Route::post('reset/code', 'Client\ForgotPasswordController@sendCode');
    Route::post('reset/password', 'Client\ResetPasswordController@resetEmail');

    
    Route::get('paypal-confirm', 'PayPalController@confirm')->name('Client-Login');
    Route::get('/google-login', 'GoogleAuthController@redirectToProvider');
    Route::get('/google-callback', 'GoogleAuthController@handleProviderCallback');
    Route::post('/SFreq', function (Request $request) {
        
        return response()->json($request->all(), 401);
            
        
    });
    
    // Route::get('/home', function (Request $request) {
    //         return response()->json("unauth", 401);
    // })->name('home');
    











