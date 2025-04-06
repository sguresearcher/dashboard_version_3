<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class getDataTenantCT extends Controller
{
    private $username;
    private $password;

    public function __construct()
    {
        $this->username = env('API_USERNAME_SECRETO'); 
        $this->password = env('API_PASSWORD_SECRETO');
    }
    public function totalAttack($sensor){
        // $response = Http::get("http://10.20.100.172:7777/data/telkom/conpot/24h");
        $response = Http::withBasicAuth($this->username, $this->password)->get("http://10.20.100.172:7777/data/". Auth::user()->user_code ."/". $sensor ."/24h");

        if ($response->successful()) {
            $data = $response->json();

            return response()->json([
                'total_attack' => count($data)
            ]);
        } else {
            return response()->json(['error'=>'failed to fetch data!']);
        }
        
    }

    public function top10AttackerIp($sensor){
        $response = Http::withBasicAuth($this->username, $this->password)->get("http://10.20.100.172:7777/data/". Auth::user()->user_code ."/". $sensor ."/24h");

        if ($response->successful()) {
            $data = $response->json();
            
            $topIpAttacker = collect($data)->pluck('src_ip')->countBy()->sortDesc()->take(10); //change 5 to other value you want


            return response()->json([
                'data' => $topIpAttacker
            ]);
        } else {
            return response()->json(['error'=>'failed to fetch data!']);
        }
    }

    public function total($sensor){
        $response = Http::withBasicAuth($this->username, $this->password)->get("http://10.20.100.172:7777/data/". Auth::user()->user_code ."/". $sensor ."");

        if ($response->successful()) {


            $data = $response->json();


            return response()->json([
                'data' => $data
            ]);
        } else {
            return response()->json(['error'=>'failed to fetch data!']);
        }
    }

    // Dashboard Guest Here

    public function totalAttackGuest(){


        // $response = Http::withBasicAuth('S6ur3searcher!#', 'pleasechangeme123')
        //         ->withHeaders([
        //             'Accept' => 'application/json'
        //         ])
        //         ->get('http://10.20.100.172:7777/data/all/30d');

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode(''.$this->username.':'.$this->password.'')
        ])
        ->get('http://10.20.100.172:7777/data/all/24h');

        if ($response->successful()) {
            
            $data = $response->json();
            return response()->json([
                'total_attack' => count($data['data'])
            ])->setStatusCode(200);

        } else {
         return response()->json([
            'message' => 'Failed to fetch data!'
         ])->setStatusCode(404);
        }
        
        
    }

    public function getDataTop10Table(){

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode(''.$this->username.':'.$this->password.'')
            ])->get('http://10.20.100.172:7777/data/all/24h');

            if ($response->successful()) {
                $data = collect($response->json()['data'] ?? []);

                $topAttackers = $data->groupBy('source_address')
                    ->map(function ($items, $ip) {
                        return [
                            'source_address' => $ip,
                            'target_address' => $items->first()['target_address'] ?? '-',
                            'eventid' => $items->first()['eventid'] ?? '-',
                            'target_port' => $items->first()['target_port'] ?? '-',
                            'total_attack' => $items->count()
                        ];
                    })
                    ->sortByDesc('time')
                    ->take(10)
                    ->values();

                return response()->json([
                    'total_attack' => [
                        'data' => $topAttackers
                    ]
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Failed to fetch data!'
                ], 404);
            }
    }

    public function getSensorAttackCount(){

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode(''.$this->username.':'.$this->password.'')
        ])->get('http://10.20.100.172:7777/data/all/24h');

        if ($response->successful()) {
            $data = collect($response->json()['data'] ?? []);

            $sensorCounts = $data->groupBy('eventid')
                ->map(function ($items, $sensor) {
                    return [
                        'sensor' => $sensor ?? 'Unknown',
                        'count' => $items->count()
                    ];
                })
                ->sortByDesc('count')
                ->take(10)
                ->values(); 

            return response()->json([
                'sensor_attack' => [
                    'data' => $sensorCounts
                ]
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to fetch data!'
            ], 404);
        }
    }

}
