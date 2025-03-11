<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersCT extends Controller
{
    public function index(){
        
        return view('pages.dashboard');
    }

    public function bySensor($sensor){
        $sensor = $sensor;
        return view('pages.bysensor', compact('sensor'));
    }

    public function setting(){
        return view('pages.setting');
    }
}
