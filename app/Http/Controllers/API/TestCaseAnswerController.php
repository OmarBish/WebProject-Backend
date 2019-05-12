<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TestResult;
use App\TestCaseAnswer;

use Auth;
use Validator;

class TestCaseAnswerController extends Controller
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
        $this->middleware(['auth:api','scope:tester'])->only(['store']);
        $this->middleware(['auth:api','scope:client,tester'])->only(['show']);
        $this->middleware(['auth:api','scope:tester'])->only(['update']);
        $this->middleware(['auth:api','scope:tester'])->only(['destroy']);
       
    }
    public function index(TestResult $testResut)
    {
        if(auth()->user()->tokenCan('tester')){

            $user = auth()->guard('tester')->user();
            if($user->testResults()->find($testResut->id)){
                $testCaseAnswers = $testResut->testCaseAnswers()->get();
                return $this->sendResponse($testCaseAnswers->toArray(), 'testCaseAnswers fetched successfully.');  
            }else{
                return $this->sendError("Either you dont have access to this test case answer or it was deleted","" ,404);        
            } 
        }else{
            $user = auth()->guard('client')->user();
            if($user->testResults()->find($testResut->id)){
                $testCaseAnswers = $testResut->testCaseAnswers()->get();
                return $this->sendResponse($testCaseAnswers->toArray(), 'testCaseAnswers fetched successfully.');  
            } 
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

    public function store(Request $request,TestResult $testResut)
    {
        if(auth()->user()->tokenCan('tester')){
            
            $user = auth()->guard('tester')->user();
                if($user->testResults()->find($testResut->id)){
                    $input = $request->all();

                $validator = Validator::make($input, [
                    //TODO set to active_url
                    'answer' => 'required',
                    "test_case_id" => 'required'
                ]);

                if($validator->fails()){
                    return $this->sendError('Validation Error.', $validator->errors());       
                }
                $testCaseAnswer = $testResut->testCaseAnswers()->create([
                    "answer"=>$input['answer'],
                    "test_case_id"=> $request->test_case_id
                ]);
                return $this->sendResponse($testCaseAnswer->toArray(), 'testCaseAnswer created successfully.');  
            }else{
                return $this->sendError("Either you dont have access to this test case answer or it was deleted","client can't create test case answer" ,404);        
            }
        }else{
            return $this->sendError("client can't create test case answer","client can't create test case answer" ,401);       
        }
        
    }
        /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * GET /testResults/$id
     */

    public function show(TestResult $testResut)
    {
        if(auth()->user()->tokenCan('tester')){
            $user = auth()->guard('tester')->user();

            if($testCaseAnswer=$user->testResults()->find($testResut->id)){
                return $this->sendResponse($testCaseAnswer->toArray(), 'testCaseAnswers fetched successfully.');  
            }else{
                return $this->sendError("Either you dont have access to this test case answer or it was deleted","client can't create test case answer" ,404);        
            } 
        }else{
            $user = auth()->guard('client')->user();
            if($testCaseAnswer=$user->testResults()->find($testResut->id)){
                return $this->sendResponse($testCaseAnswer->toArray(), 'testCaseAnswers fetched successfully.');  
            }else{
                return $this->sendError("Either you dont have access to this test case answer or it was deleted","client can't create test case answer" ,404);        
            } 
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

    public function update(Request $request, TestResult $testResut, TestCaseAnswer $testCaseAnswer)

    {
        if(auth()->user()->tokenCan('tester')){
            $user = auth()->guard('tester')->user();
            
            if($user->testResults()->find($testResut->id)){
                
                if($request->has('answer')){
                    $testCaseAnswer->answer =$request->answer;
                }
                if($request->has('userRate')){
                    $testCaseAnswer->userRate =$request->userRate;
                    $testCaseAnswer->save();
                    $testResut->updateRate();
                    $user->updateRate();
                }else{
                    $testCaseAnswer->save();
                }
                
                return $this->sendResponse($testCaseAnswer->toArray(), 'Test updated successfully.');
            }else{
                return $this->sendError("Either you dont have access to this test case answer or it was deleted","client can't create test case answer" ,404);        
            }
        }else{
            return $this->sendError("Either you dont have access to this test case answer or it was deleted","client can't update test case answer" ,404);        
        } 
        
    }


    /**

     * Remove the specified resource from storage.
     * @param  int  $id
     * Delete /testResults/$id
     */

    public function destroy()
    {
        
            return $this->sendError("you can't delete  test case answer", "you can't delete  test case answer",403);
    }
}
