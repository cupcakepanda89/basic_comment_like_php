<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class IndexController extends Controller
{
    public function index()
    {
        if(Auth::guard('profile')->user() || Auth::guard('post')->user() ){
            return Redirect::to('/home');

        }else{
            return view('index');
        }
    }

    public function home(){
        return view('home');
    }

}

