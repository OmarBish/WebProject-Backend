<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TestReview;

class TestReviewController extends Controller
{
    public function __construct(){
        $this->middleware(['auth:api','scope:client'])->only(['index']);
        $this->middleware(['auth:api','scope:client'])->only(['store']);
        $this->middleware(['auth:api','scope:client'])->only(['show']);
        $this->middleware(['auth:api','scope:client'])->only(['update']);
        $this->middleware(['auth:api','scope:client'])->only(['destroy']);
    }
    

    public function index(Request $request)
    {   
        $user = auth()->guard('client')->user();
        $testReviews = $user->testReviews()->get();
        return $this->sendResponse($testReviews->toArray(), 'TestReviews retrieved successfully.');
    }

    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {
        $user = auth()->guard('client')->user();
        $testReview = $user->testReviews()->create();
        
        return $this->sendResponse($testReview->toArray(), 'TestReview created successfully.');
    }
        /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        
        $user = auth()->guard('client')->user();
        $testReview =$user->testReviews()->find($id);
        


        if (is_null($testReview)) {
            return $this->sendError('Test not found or you dont have access to this test');
        }


        return $this->sendResponse($testReview->toArray(), 'Test retrieved successfully.');

    }


    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request, $id)

    {    
        $user = auth()->guard('client')->user();
        $testReview =$user->testReviews()->find($id);
        if (is_null($testReview)) {
            return $this->sendError('TestReview not found or you dont have access to this test');
        }
        if($request->has('feedback')){
            $testReview->feedback = $request->feedback;
        }
        if($request->has('testerRate')){
            $testReview->testerRate = $request->testerRate;
        }
        $testReview->save();
        $testReview->tester()->first()->updateRate();
        return $this->sendResponse($testReview->toArray(), 'Test updated successfully.');
    }


    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy(Request $req,TestReview $testReview)
    {
        $user = auth()->guard('client')->user();
        try{    
            $testReview->delete();
            return $this->sendResponse("", 'testReview deleted successfully.');
        }catch(Exception $x){
            return $this->sendError('access error', 'either you dont have access to this record or it was deleted');
        }
        
    }
}
