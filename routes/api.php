<?php

use Illuminate\Http\Request;
header('Access-Control-Allow-Origin:  *');
   header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
   header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user', function (Request $request) {
    try{
        if(auth()->guard('tester')->user()==null){
            return response()->json("unauthnticated", 401);
        }
        if(auth()->guard('tester')->user()->tokenCan('client')){
            $user=auth()->guard('client')->user();
            if($user  != null){
                return ['token'=>$user->token()->only(['user_id','scopes','revoked','expires_at'
                        ]),'user'=>$user];
            }else{
                return response()->json("unauthnticated", 401);
            }
        }else{
            $user=auth()->guard('tester')->user();
            if($user  != null){
                return ['token'=>$user->token()->only(['user_id','scopes','revoked','expires_at'
                        ]),'user'=>$user];
            }else{
                return response()->json("unauthnticated", 401);
            }
        }
        
    }catch(Exception $e){
        return response()->json("unauthnticated", 401);
    }
    
    
});


Route::resource('test', 'API\TestController',['except' => ['create']]);
Route::resource('test/{test}/testResult', 'API\TestResultsController',['except' => ['create']]);
Route::resource('testResult/{testResult}/testCaseAnswer', 'API\TestCaseAnswerController',['except' => ['create']]);
Route::resource('test/{test}/testCase', 'API\TestCaseController',['except' => ['create']]);
Route::resource('test/{test}/testReview', 'API\TestReviewController');

Route::middleware('auth:api')->group( function () {

    //TODO
    Route::resource('testCaseReview', 'API\TestCaseReviewController');
});
Route::resource('client', 'API\ClientController',['except' => ['create']]);
Route::resource('tester', 'API\TesterController',['except' => ['create']]);




