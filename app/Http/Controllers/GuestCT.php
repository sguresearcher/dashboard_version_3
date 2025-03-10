<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GuestCT extends Controller
{
    public function index(){
        $response = Http::get("http://10.20.100.172:7777/data/telkom/conpot/24h");

        if ($response->successful()) {
            $data = $response->json();
        } else {
            return response()->json(['error'=>'failed to fetch data!']);
        }
        
    }
}
