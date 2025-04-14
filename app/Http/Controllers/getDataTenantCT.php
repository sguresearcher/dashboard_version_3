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

    public function totalAttackGuest() {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
        ])->timeout(180)->get('http://10.20.100.172:7777/summary/24h');
    
        if ($response->successful()) {
            $summary = $response->json();
            $total = collect($summary)->sum('total_entries');
    
            return response()->json([
                'total_attack' => $total
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to fetch data!'
            ], 404);
        }
    }
    
    

    public function getDataTop10Table() {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
        ])->timeout(180)->get('http://10.20.100.172:7777/summary/24h');
    
        if ($response->successful()) {
            $summary = $response->json();
            $combined = collect();
    
            foreach ($summary as $tenant => $data) {
                foreach ($data['combined_attack'] ?? [] as $entry) {
                    $combined->push($entry);
                }
            }
    
            // Gabungkan berdasarkan IP source
            $topAttackers = $combined->groupBy('source_address')
                ->map(function ($items, $ip) {
                    return [
                        'source_address' => $ip,
                        'target_address' => $items->first()['target_address'],
                        'eventid' => $items->first()['protocol'],
                        'target_port' => $items->first()['port'],
                        'total_attack' => collect($items)->sum('total')
                    ];
                })
                ->sortByDesc('total_attack')
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
    
    

    public function getSensorAttackCount() {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
        ])->timeout(180)->get('http://10.20.100.172:7777/summary/24h');
    
        if ($response->successful()) {
            $summary = $response->json();
    
            $combined = collect();
            foreach ($summary as $tenant => $data) {
                foreach ($data['combined_attack'] ?? [] as $entry) {
                    $combined->push($entry);
                }
            }
    
            $sensorCounts = $combined->groupBy('sensor')
                ->map(function ($items, $sensor) {
                    return [
                        'sensor' => $sensor ?? 'unknown',
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
    
    

    public function getSensorAverageAttackCount() {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
        ])->timeout(180)->get('http://10.20.100.172:7777/summary/24h');
    
        if ($response->successful()) {
            $summary = $response->json();
            $totalJam = 24;
    
            $sensorTotals = [];
    
            foreach ($summary as $tenant => $data) {
                foreach ($data['combined_attack'] ?? [] as $entry) {
                    $sensor = $entry['sensor'] ?? 'unknown';
                    $sensorTotals[$sensor] = ($sensorTotals[$sensor] ?? 0) + ($entry['total'] ?? 0);
                }
            }
    
            $data = collect($sensorTotals)->map(function ($total, $sensor) use ($totalJam) {
                return [
                    'sensor' => $sensor,
                    'average_per_hour' => round($total / $totalJam, 2)
                ];
            })->sortByDesc('average_per_hour')->values();
    
            return response()->json([
                'sensor_attack' => [
                    'data' => $data
                ]
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to fetch data!'
            ], 404);
        }
    }
    
    
    

    public function totalAttackTenantDashboard(){
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
        ])->timeout(180)->get('http://10.20.100.172:7777/summary/24h');
    
        if ($response->successful()) {
            $tenant_code = 'ewsdb_hp_' . Auth::user()->user_code . '_1';
    
            $dataTenant = collect($response->json()[$tenant_code]['combined_attack'] ?? []);
    
            return response()->json([
                'total_attack' => $dataTenant->sum('total')
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to fetch data!'
            ], 404);
        }
    }
    
    

    public function getDataTop10TenantDashboard(){
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
        ])->timeout(180)->get('http://10.20.100.172:7777/summary/24h');
    
        if ($response->successful()) {
            $tenant_code = 'ewsdb_hp_' . Auth::user()->user_code . '_1';
    
            $dataTenant = collect($response->json()[$tenant_code]['combined_attack'] ?? [])
                ->filter(function($entry) {
                    return strtolower($entry['protocol']) !== 'heartbeat';
                })
                ->map(function($entry) {
                    $entry['source_address'] = str_replace('::ffff:', '', $entry['source_address']);
                    $entry['target_address'] = str_replace('::ffff:', '', $entry['target_address']);
                    return $entry;
                });
    
            $topAttackers = $dataTenant
                ->sortByDesc('total')
                ->take(10)
                ->values()
                ->map(function($entry) {
                    return [
                        'source_address' => $entry['source_address'],
                        'target_address' => $entry['target_address'],
                        'eventid' => $entry['protocol'],
                        'target_port' => $entry['port'],
                        'total_attack' => $entry['total']
                    ];
                });
    
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
    
    

    public function getDataAverageTenant(){
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
        ])->timeout(180)->get('http://10.20.100.172:7777/summary/24h');
    
        if ($response->successful()) {
            $tenant_code = 'ewsdb_hp_' . Auth::user()->user_code . '_1';
    
            $dataTenant = collect($response->json()[$tenant_code]['combined_attack'] ?? [])
                ->filter(function($entry) {
                    return strtolower($entry['protocol']) !== 'heartbeat';
                });
    
            $sensorCounts = $dataTenant->groupBy('sensor')
                ->map(function ($items, $sensor) {
                    $total = $items->sum('total');
                    return [
                        'sensor' => $sensor,
                        'total' => $total,
                        'average_per_hour' => round($total / 24, 2)
                    ];
                })
                ->sortByDesc('total')
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
