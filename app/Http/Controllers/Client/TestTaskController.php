<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TestResult;
use Validator;

class TestTaskController extends Controller
{
    public function __construct(){
        $this->middleware(['auth:api','scope:client'])->only(['create']);
        $this->middleware(['auth:api','scope:client'])->only(['setActive']);
        $this->middleware(['auth:api','scope:client'])->only(['review']);

    }
    public function create(Request $req){
        
        
        
        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'websiteURL' => 'required',
            'credit' => 'required',
            'post_url' => 'url',
            'testers' => 'required',
            'comment'=> 'boolean',
            'video' => 'boolean'
        ]);


        if($validator->fails()){

            return $this->sendError('Validation Error.', $validator->errors());       

        }

        $user = auth()->guard('client')->user();
        if ($user->credits - $req->credit < 0){
            return $this->sendError('insufficient credits.');       
        }
        $test = $user->tests()->create([
            "comment" => $req->comment,
            "credit" => $req->credit,
            "name" => $req->name,
            'websiteURL' => $req->websiteURL,
            'testers' => $req->testers,
            'video' => $req->video,
            'active' => true,
            'tags'=>'undefined'
        ]);
        
        $testCases=$req->subtasks;
        
        foreach ($testCases as $testCase){

            if($testCase['question'] == null){
                $test->delete();
                return $this->sendError('Validation Error.', "question musn't be null");       
            }
            $test->testCases()->create([
                "question" => $testCase['question'],
                "type" => $testCase['type'],
            ]);
        }
        // return $this->sendResponse(["test"=>$test->toArray(),"testcases"=>$test->testCases()->get()->toArray()], 'Test created successfully.');
        return $this->sendResponse($test->toArray(), 'Test created successfully.');
    }
    public function setActive(Request $req){
        $validator = Validator::make($req->all(), [
            'taskID' => 'required',
            'active' => 'required|boolean',
        ]);


        if($validator->fails()){

            return $this->sendError('Validation Error.', $validator->errors());       

        }

        $user = auth()->guard('client')->user();
        $test = $user->tests()->find($req->taskID);
        if (is_null($test)) {
            return $this->sendError('Test not found or you dont have access to this test');
        }

        $test->active=$req->active;
        $test->save();
        return $this->sendResponse($test->active, 'Test updated successfully.');
    }
    public function review(Request $req){
        $validator = Validator::make($req->all(), [
            'taskID' => 'required',
            'answerID' => 'required',
            'subtask_answers' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        $user = auth()->guard('client')->user();
        $test = $user->tests()->find($req->taskID);
        if(is_null($test)){
            return $this->sendError('Test not found or you dont have access to this test');
        }
        
        $testResult=TestResult::find($req->answerID)->first();
        if($testResult->test()->get()->first()->id != $test->id){
            return $this->sendError('this test result dosen\'t belong to this test');
        }
        foreach($req->subtask_answers as $key=>$subtaskAnswer){
            $testCaseAnswer = $testResult->testCaseAnswers()->get()->where("test_case_id",$subtaskAnswer['subtaskID'])->first();
            
            if(is_null( $testCaseAnswer)){
                return $this->sendError('this test answer dosen\'t belong to this test result');
            }
            $testCaseAnswer->clientRate=$subtaskAnswer['subtaskRating'];
            $testCaseAnswer->save();
            
        }
        
        $testResult->updateRate();
        $tester=$testResult->tester()->get()->first();
        $tester->updateRate();
        $tester->updateCredits($test,$user);
        
        
        return $this->sendResponse($testResult->testCaseAnswers()->get()->toArray(), 'review submited successfully.');
    }
}
