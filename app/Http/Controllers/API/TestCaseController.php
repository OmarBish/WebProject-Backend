<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Test;
use App\TestCase;

use Auth;
use Validator;

class TestCaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * /testResults
     */
    //TODO
    public function __construct(){
        $this->middleware(['auth:api','scope:tester,client'])->only(['index']);
        $this->middleware(['auth:api','scope:client'])->only(['store']);
        $this->middleware(['auth:api','scope:client,tester'])->only(['show']);
        $this->middleware(['auth:api','scope:client'])->only(['update']);
        $this->middleware(['auth:api','scope:client'])->only(['destroy']);
    }
    public function index(Test $test)
    {
        if(auth()->user()->tokenCan('tester')){
            $user = auth()->guard('tester')->user();
        }else{
            $user = auth()->guard('client')->user();
        }
        
        if($test != null){
            $testCases = $test->testCases()->get();
            return $this->sendResponse($testCases->toArray(), 'testCaseAnswers fetched successfully.');
        }else{
            return $this->sendError("Either you dont have access to this test case answer or it was deleted","" ,404);        
        }
             
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * POST /testResults
     */

    public function store(Request $request,Test $test)
    {
       $user = auth()->guard('client')->user();
        if($user->tests()->find($test->id)){
            $input = $request->all();

            $validator = Validator::make($input, [
                //TODO set to active_url
                'question' => 'required',
                'type' => 'required',
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }
            $testCase = $test->testCases()->create([
                "question"=>$input['question'],
                "type"=>$input['type'],
            ]);
            return $this->sendResponse($testCase->toArray(), 'testCase created successfully.');  
        }else{
            return $this->sendError("Either you dont have access to this test case answer or it was deleted","client can't create test case answer" ,404);        
        }
        
        
    }
        /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * GET /testResults/$id
     */

    public function show(Test $test,TestCase $testCase)
    {
        
        
        if($test){
            return $this->sendResponse($testCase->toArray(), 'testCase fetched successfully.');  
        }else{
            return $this->sendError("Either you dont have access to this test case answer or it was deleted","client can't create test case answer" ,404);        
        } 
    
        
        
        
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * PATCH /testResults/$id
     */

    public function update(Request $request, Test $test, TestCase $testCase)

    {
       
        $user = auth()->guard('client')->user();
        $input = $request->all();
        
        if($user->tests()->find($test->id)){
            if($request->has('question'))
                $testCase->question =$input['question'];
            if($request->has('type'))
                $testCase->type =$input['type'];
            $testCase->save();
            return $this->sendResponse($testCase->toArray(), 'Test updated successfully.');
        }else{
            return $this->sendError("Either you dont have access to this test case answer or it was deleted","client can't create test case answer" ,404);        
        }
        
    }


    /**

     * Remove the specified resource from storage.
     * @param  int  $id
     * Delete /testResults/$id
     */

    public function destroy(Test $test, TestCase $testCase)
    {
        $user = auth()->guard('client')->user();
        if($user->tests()->find($test->id)){
            $testCase->delete();
            return $this->sendResponse($testCase->toArray(), 'Test updated successfully.');
        }else{
            return $this->sendError("Either you dont have access to this test case answer or it was deleted","client can't create test case answer" ,404);        
        }
    }
}
