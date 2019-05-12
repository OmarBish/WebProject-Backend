<?php

namespace App\Http\Controllers\API;

use App\Tester;
use App\Test;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

use Validator;


class TesterController extends BaseController
{
    //TODO
    public function __construct(){
        $this->middleware('guest')->only('store');
        $this->middleware('auth:tester')->except('store');
        $this->middleware(['auth:api','scope:tester'])->only(['all']);
    }
    /**
     * Display a listing of the clients
     *
     * @param  \App\Tester  $model
     * @return \Illuminate\View\View
     */
    public function index(Tester $testers)
    {
        $data = ['testers' => $testers->paginate(15)];
        return $this->sendResponse($data,"Retrive all clients");
    }

    /**
     * Show the form for creating a new user
     *
     * @return \Illuminate\View\View
     */
    public function show(Tester $tester)
    {
        return $this->sendResponse($tester,"Retrive client");
    }

    /**
     * Store a newly created user in storage
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\Tester  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        
        /**
         * 1- check validation
         * 2- check if exist
         * 3- regisiter
         */
        $validator = Validator::make($request->all(), [

            'name' => 'required',

            'email' => 'required|email',

            'password' => 'required',

            'c_password' => 'required|same:password',

        ]);


        if($validator->fails()){

            return $this->sendError('Validation Error.', $validator->errors());       

        }

        $tester = Tester::where('email',$request->email)->first();
        
        if(isSet($tester)){
            return $this->sendError('Tester already exist', "Tester already exist");       
        }

        $input = $request->all();

        $input['password'] = bcrypt($input['password']);

        $tester = Tester::create($input);

        $success['token'] =  $tester->createToken('MyApp')->accessToken;

        $success['name'] =  $tester->name;


        return $this->sendResponse($tester, 'Tester register successfully.');
    }

    
    /**
     * Update the specified user in storage
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\Tester  $tester
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Tester  $tester)
    {
        $tester->update(
            $request->merge(['password' => Hash::make($request->get('password'))])
                ->except([$request->get('password') ? '' : 'password']
        ));
       
       return  $this->sendResponse($tester,"update client");
    }

    /**
     * Remove the specified user from storage
     *
     * @param  \App\Tester  $tester
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Tester  $tester)
    {
        $tester->delete();

        return  $this->sendResponse("success","tester deleted");
    }
    public function all(Request  $req)
    {
        $user = auth()->guard('tester')->user();
        $data['points']=$user->credits;
        $data['name']=$user->name;
        $data['email']=$user->email;
        $data['about_me']=$user->about_me;
        $data['rate']=$user->rate;
        
        $data['activeTasks']=Test::all();
        $testResults = $user->testResults()->get();

        foreach( $testResults as $key => $testResult){
            $test=$testResult->test()->get()->first();
            if($data['activeTasks']->find($test->id)){
                $data['activeTasks']=$data['activeTasks']->except($test->id);
            }
            if($testResult->status=='working'){
                $data['tasks'][$key]=$test;
            }else if($testResult->status=='completed'){
                $data['completedTasks'][$key]=$test;
            }
        }
        
        
            
        
        if(isset($data['tasks'])){
            foreach ($data['tasks'] as $key => $test) {
                $data['tasks'][$key]['subtasks']= $test->testCases()->get();
                $data['tasks'][$key]['answers'] = $test->testResults()->get();
                if (count($data['tasks'][$key]['answers']) == 0){
                    $data['tasks'][$key]['finished'] =  false;
                }else if((count($data['tasks'][$key]['answers']) - $data['tasks'][$key]['testers'] == 0)){
                    $data['tasks'][$key]['finished'] =  false;
                }else{
                    $data['tasks'][$key]['finished'] =  false;
                }
                foreach ($data['tasks'][$key]['answers'] as $answerKey => $answer) {
                    $data['tasks'][$key]['answers'][$answerKey]['subtask_answers'] = $answer->testCaseAnswers()->get();
                    
                }
            }
        }else{
            $data['tasks']=[];
        }
        if(isset($data['completedTasks'])){
            foreach ($data['completedTasks'] as $key => $test) {
                $data['completedTasks'][$key]['subtasks']= $test->testCases()->get();
                $data['completedTasks'][$key]['answers'] = $test->testResults()->get();
                if (count($data['completedTasks'][$key]['answers']) == 0){
                    $data['completedTasks'][$key]['finished'] =  false;
                }else if((count($data['completedTasks'][$key]['answers']) - $data['completedTasks'][$key]['testers'] == 0)){
                    $data['completedTasks'][$key]['finished'] =  false;
                }else{
                    $data['completedTasks'][$key]['finished'] =  false;
                }
                foreach ($data['completedTasks'][$key]['answers'] as $answerKey => $answer) {
                    $data['completedTasks'][$key]['answers'][$answerKey]['subtask_answers'] = $answer->testCaseAnswers()->get();
                    
                }
            }
        }else{
            $data['completedTasks']=[];
        }
        if(isset($data['activeTasks'])){
            foreach ($data['activeTasks'] as $key => $test) {
                $data['activeTasks'][$key]['subtasks']= $test->testCases()->get();
                $data['activeTasks'][$key]['answers'] = $test->testResults()->get();
                if (count($data['activeTasks'][$key]['answers']) == 0){
                    $data['activeTasks'][$key]['finished'] =  false;
                }else if((count($data['activeTasks'][$key]['answers']) - $data['activeTasks'][$key]['testers'] == 0)){
                    $data['activeTasks'][$key]['finished'] =  false;
                }else{
                    $data['activeTasks'][$key]['finished'] =  false;
                }
                foreach ($data['activeTasks'][$key]['answers'] as $answerKey => $answer) {
                    $data['activeTasks'][$key]['answers'][$answerKey]['subtask_answers'] = $answer->testCaseAnswers()->get();
                    
                }
            }
        }else{
            $data['activeTasks']=[];
        }   
        
        return  $this->sendResponse($data,"client deleted");
    }
}
