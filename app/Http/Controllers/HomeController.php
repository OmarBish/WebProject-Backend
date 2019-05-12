<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if(auth()->user()->type == "client"){
            return view('client.dashboard');
        }else{
            
            return view('tester.dashboard');
        }
        
    }
    public function setPath()
    {
        if(null!==  auth()->user() ) {
            if(auth()->user()->type == "client"){
                return redirect('/client/home');;
           }else{
               return redirect('/tester/home');
           }
        }
        else{
            return redirect('/');
        }
        
    }
}
