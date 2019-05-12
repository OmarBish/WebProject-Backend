<?php
//App\Http\Controllers\Client\ClientController
namespace App\Http\Controllers\API;

use App\Client;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Client\AuthController;

use Validator;


class ClientController extends BaseController
{
    //TODO
    public function __construct(){
        $this->middleware('auth:client')->except('store');
        $this->middleware('guest')->only('store');
        $this->middleware(['auth:api','scope:client'])->only(['all']);
    }
    /**
     * Display a listing of the clients
     *
     * @param  \App\Client  $model
     * @return \Illuminate\View\View
     */
    public function index(Client $model)
    {
        $data = ['clients' => $model->paginate(15)];
        return $this->sendResponse($data,"Retrive all clients");
    }

    /**
     * Display a listing of the clients
     *
     * @param  \App\Client  $model
     * @return \Illuminate\View\View
     */
    public function show(Client $client)
    {
        return $this->sendResponse($client,"Retrive client");
    }



    /**
     * Store a newly created user in storage
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\Client  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
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


        $client = Client::where('email',$request->email)->first();
        
        if(isSet($client)){
            return $this->sendError('Client already exist', "Client already exist");       
        }

        $input = $request->all();

        $input['password'] = bcrypt($input['password']);

        $client = Client::create($input);

        $success['token'] =  $client->createToken('MyApp')->accessToken;

        $success['name'] =  $client->name;


        return $this->sendResponse($client, 'Client register successfully.');

    }


    /**
     * Update the specified user in storage
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\Client  $client
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Client  $client)
    {
        
         $client->update(
             $request->merge(['password' => Hash::make($request->get('password'))])
                 ->except([$request->get('password') ? '' : 'password']
         ));
        
        return  $this->sendResponse($client,"update client");
    }

    /**
     * Remove the specified user from storage
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Client  $client)
    {
        $client->delete();

        return  $this->sendResponse("success","client deleted");
    }
    public function all(Client  $client)
    {
        $user = auth()->guard('client')->user();
        
        
        $data['points']=$user->credits;

        $data['name']=$user->name;
        $data['email']=$user->email;
        $data['about_me']=$user->about_me;

        $data['tasks']=$user->tests()->get();
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
        


        return  $this->sendResponse($data,"client deleted");
    }
}
