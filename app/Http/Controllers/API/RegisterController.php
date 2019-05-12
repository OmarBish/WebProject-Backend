<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Client;
use App\Tester;
use Illuminate\Support\Facades\Auth;
use Validator;

class RegisterController extends BaseController
{
    /**

     * Register api

     *

     * @return \Illuminate\Http\Response

     */

    public function clientRegister(Request $request)

    {

        $validator = Validator::make($request->all(), [

            'name' => 'required',

            'email' => 'required|email',

            'password' => 'required',

            'c_password' => 'required|same:password',

        ]);


        if($validator->fails()){

            return $this->sendError('Validation Error.', $validator->errors());       

        }


        $input = $request->all();

        $input['password'] = bcrypt($input['password']);

        $client = Client::create($input);

        $success['token'] =  $client->createToken('MyApp')->accessToken;

        $success['name'] =  $client->name;


        return $this->sendResponse($success, 'Client register successfully.');

    }

    public function testerRegister(Request $request)

    {

        $validator = Validator::make($request->all(), [

            'name' => 'required',

            'email' => 'required|email',

            'password' => 'required',

            'c_password' => 'required|same:password',

        ]);


        if($validator->fails()){

            return $this->sendError('Validation Error.', $validator->errors());       

        }


        $input = $request->all();

        $input['password'] = bcrypt($input['password']);

        $tester = Tester::create($input);

        $success['token'] =  $tester->createToken('MyApp')->accessToken;

        $success['name'] =  $tester->name;


        return $this->sendResponse($success, 'Tester register successfully.');

    }
}
