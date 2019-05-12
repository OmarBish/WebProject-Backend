<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Test;
use App\Client;
use Validator;

class TestController extends BaseController
{

    public function __construct(){
        $this->middleware(['auth:api','scope:tester,client'])->only(['index']);
        $this->middleware(['auth:api','scope:client'])->only(['store']);
        $this->middleware(['auth:api','scope:client,tester'])->only(['show']);
        $this->middleware(['auth:api','scope:client'])->only(['update']);
        $this->middleware(['auth:api','scope:client'])->only(['destroy']);
    }
    
    /**
     * list all Tests for authenticated users
     * GET /api/Test
     */
    public function index(Request $request)
    {   
        $user = auth()->guard('client')->user();
        $tests = $user->tests()->get();
        return $this->sendResponse($tests->toArray(), 'Tests retrieved successfully.');
    }

    /**
     * Store a newly created Test in storage.
     * Post /api/Test
     * 
     */

    public function store(Request $request)

    {

        $input = $request->all();


        $validator = Validator::make($input, [
            'name' => 'required|String',
            'websiteURL' => 'required|URL',
            'credit' => 'required|numeric',
            'post_url' => 'URL',
            'testers' => 'required|numeric|min:1',
            'tags' => 'String'    
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $user = auth()->guard('client')->user();
        $test = $user->tests()->create($input);


        return $this->sendResponse($test->toArray(), 'Test created successfully.');

    }
      /**
     * show a Test
     * GET /api/Test/{id}
     */
    public function show($id)

    {
        if(auth()->user()->tokenCan('tester'))
            $user = auth()->guard('tester')->user();
        else
            $user = auth()->guard('client')->user();

        $test =$user->tests()->find($id)->first();
        
        if (is_null($test)) {
            return $this->sendError('Test not found or you dont have access to this test');
        }
        return $this->sendResponse($test->toArray(), 'Test retrieved successfully.');
    }


   /**
     * update a Test in storage.
     * PUT/PATCH /api/Test/{id}
     */

    public function update(Request $request, Test $test)

    {
        
        $input = $request->all();

        
        $validator = Validator::make($input, [
            'name' => 'String',
            'websiteURL' => 'URL',
            'credit' => 'numeric|min:0',
            'post_url' => 'URL',
            'testers' => 'numeric|min:1',
            'tags' => 'String'    
        ]);


        if($validator->fails()){

            return $this->sendError('Validation Error.', $validator->errors());       

        }
       
        $user = auth()->guard('client')->user();

        $test = $user->tests()->find($test->id);
        if (is_null($test)) {
            return $this->sendError('Test not found or you dont have access to this test');
        }

        if(isset($input['name'])){
            $test->name = $input['name'];
        }
        if(isset($input['websiteURL'])){
            $test->websiteURL = $input['websiteURL'];
        }
        if(isset($input['credit'])){
            $test->credit = $input['credit'];
        }
        if(isset($input['tags'])){
            $test->tags = $input['tags'];
        }
        if(isset($input['pst_url'])){
            $test->tags = $input['post_url'];
        }
        if(isset($input['testers'])){
            $test->testers = $input['testers'];
        }
        $test->save();
        return $this->sendResponse($test->toArray(), 'Test updated successfully.');
    }


    /**
     * Delete a Test from storage.
     * DELETE /api/Test/{id}
     */

    public function destroy(Request $req,Test $test)

    {
        $user = auth()->guard('client')->user();
        try{
            if($user->tests()->find($test->id)){
                $test->delete();
                return $this->sendResponse("", 'Test deleted successfully.');
            }else{
                return $this->sendError('either you dont have access to this record or it was deleted');
            }
        }catch(Exception $x){
            return $this->sendError( 'either you dont have access to this record or it was deleted');
        }
        
    }
}




