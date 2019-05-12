<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestCaseReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * /testResults
     */

    public function index()

    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * POST /testResults
     */

    public function store(Request $request)

    {

        
    }
        /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * GET /testResults/$id
     */

    public function show($id)

    {


    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * PATCH /testResults/$id
     */

    public function update(Request $request, Test $test)

    {
        
        

    }


    /**

     * Remove the specified resource from storage.
     * @param  int  $id
     * Delete /testResults/$id
     */

    public function destroy(Test $test)

    {


    }
}
