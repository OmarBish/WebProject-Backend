<?php

namespace App\Http\Controllers\Tester;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class WebController extends Controller
{
    public function __construct(){
        $this->middleware(['auth:api','scope:tester'])->only(['sendAnswer']);
        $this->middleware(['auth:api','scope:tester'])->only(['addtest']);
    }
    public function sendAnswer(Request $req){
        $validator = Validator::make($req->all(), [
            'taskID' => 'required',
            'subtask_answers' => 'required',
            'is_submit' =>'required|boolean',
            'video_link'=>'required',
            'comment_text'=>'required'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $user = auth()->guard('tester')->user();
        
        $testResult = $user->testResults()->where('test_id',$req->taskID)->get()->first();
        // dd($testResult->id);
        if(!is_null($testResult)){
            if($testResult->status == 'completed'){
                return $this->sendError('you can\'t resubmit a test');
            }
            if($req->is_submit){
                $status = 'completed';
            }else{
                $status = 'working';
            }
            $testResult->update([
                'status' =>$status,
                'videoURL'=>$req->video_link,
                'comment_text'=>$req->comment_text
            ]);
            $testCaseAnswers = $testResult->testCaseAnswers()->get();
            if(sizeof($testCaseAnswers)==0){
                $test= $testResult->test()->get()->first();
                $testCases=$test->testCases()->get();
                foreach($testCases as $key=>$testCase){
                    $testResult->testCaseAnswers()->create([
                        'test_case_id'=>$testCase->id,
                        'userRate'=>0,
                        'answer'=>''
                    ]);
                }
                
            } 
            
            foreach($req->subtask_answers as $key=>$subtaskAnswer){
                $testCaseID = $subtaskAnswer['subtaskID'];
                // dd($testCaseID);
                $testCasean = $testResult->testCaseAnswers()->get()->where('test_case_id',$testCaseID)->first();
                $testCasean = \App\TestCaseAnswer::find($testCasean->id)->first();
                $data=['userRate'=>$subtaskAnswer['subtaskRating'],'answer'=>$subtaskAnswer['subtaskAnswer']];
                
                $testCasean->update($data);
            }
            return $this->sendResponse([$testResult->testCaseAnswers()->get()->toArray(),$testResult->toArray()], 'Test answer updated successfully updated successfully.');

        }else{
            return $this->sendError("you didn't choose to particepate in this test please add it to your tests before submitting an asnwer");
        }
    }
    public function addtest(Request $request){
        $validator = Validator::make($request->all(), [
            'taskID' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        $user = auth()->guard('tester')->user();
        
        $testResult = $user->testResults()->where('test_id',$request->taskID)->get()->first();

        if($testResult){
            return $this->sendError('you already applied to this test');       
        }
        $user->testResults()->create([
            'videoURL'=>' ',
            'test_id'=>$request->taskID,
        ]);
        return $this->sendResponse("", 'Test result created successfully ');
    }
}
