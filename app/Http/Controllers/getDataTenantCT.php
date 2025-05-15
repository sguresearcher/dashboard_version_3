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
    
    public function totalAttack($sensor)
{
    $response = Http::withBasicAuth($this->username, $this->password)
        ->get("http://10.20.100.172:7777/data/" . Auth::user()->user_code . "/" . $sensor . "/24h");

    if ($response->successful()) {
        $rawData = $response->json();

        $data = collect($rawData['data'] ?? $rawData);

        $filtered = match (strtolower($sensor)) {
            'honeytrap' => $data->filter(function ($item) {
                return isset($item['category']) &&
                       strtolower($item['category']) !== 'heartbeat' &&
                       isset($item['source-ip']) &&
                       filter_var($item['source-ip'], FILTER_VALIDATE_IP);
            }),

            'cowrie', 'conpot', 'dionaea', 'dionaea_ews' => $data->filter(function ($item) {
                return isset($item['src_ip']) && filter_var($item['src_ip'], FILTER_VALIDATE_IP);
            }),

            'rdpy' => $data->filter(function ($item) {
                return isset($item['client_ip']) && filter_var($item['client_ip'], FILTER_VALIDATE_IP);
            }),

            default => collect()
        };

        return response()->json([
            'total_attack' => $filtered->count()
        ]);
    }

    return response()->json(['error' => 'failed to fetch data!'], 404);
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
                'total_attack' => number_format($total),
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
    
            $topAttackers = $combined->groupBy('source_address')
                ->map(function ($items, $ip) {
                    return [
                        'source_address' => $ip,
                        'target_address' => $items->first()['target_address'],
                        'eventid' => $items->first()['protocol'],
                        'target_port' => $items->first()['port'],
                        'total_attack' => number_format(collect($items)->sum('total')),
                        'total_raw' => collect($items)->sum('total'),
                    ];
                })
                ->sortByDesc('total_raw')
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
                        'total' => number_format($items->count()),
                        'average_per_hour' => number_format(round($items->sum('total') / (24 * 60))),
                        'average_per_day' => number_format(round($items->sum('total') / 24)),
                        'total_raw' => $items->count()
                    ];
                })
                ->sortByDesc('total_raw')
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
    
    
    public function getSensorAverageAttackCount()
    {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
            ])->timeout(180)->get('http://10.20.100.172:7777/summary/24h');

            if (!$response->successful()) {
                return response()->json(['message' => 'Failed to fetch data!'], 404);
            }

            $summary = $response->json();
            $totalJam = 24;
            $sensorTotals = [];

            foreach ($summary as $data) {
                foreach ($data['combined_attack'] ?? [] as $entry) {
                    $sensor = $entry['sensor'] ?? 'unknown';
                    $sensorTotals[$sensor] = ($sensorTotals[$sensor] ?? 0) + ($entry['total'] ?? 0);
                }
            }

            $data = collect($sensorTotals)->map(function ($total, $sensor) use ($totalJam) {
                return [
                    'sensor' => $sensor,
                    'average_per_hour' => number_format(round($total / $totalJam)),
                    'total_per_day' => number_format($total),
                    'total_raw' => $total,
                    'average_per_minute' => number_format(round($total / ($totalJam * 60))),

                ];
            })->sortByDesc('total_raw')->values();

            return response()->json([
                'sensor_attack' => [
                    'data' => $data
                ]
            ], 200);
    }


    public function getAttackCountBySensorName($sensor)
    {
        $response = Http::withBasicAuth($this->username, $this->password)
            ->timeout(180)
            ->get("http://10.20.100.172:7777/summary/24h");
    
        if ($response->successful()) {
            $rawCode = strtolower(Auth::user()->user_code);
            $cleanCode = str_starts_with($rawCode, 'hp_') ? $rawCode : 'hp_' . $rawCode;
            $tenantKey = 'ewsdb_' . $cleanCode;
    
            $rawData = $response->json();
    
            $entries = collect($rawData[$tenantKey]['combined_attack'] ?? [])
                ->filter(function ($item) use ($sensor) {
                    return isset($item['sensor'], $item['source_address']) &&
                           strtolower($item['sensor']) === strtolower($sensor);
                });
    
            $grouped = $entries->groupBy('source_address')->map(function ($group, $ip) {
                $total = $group->sum('total');
                return [
                    'source_address' => $ip,
                    'total' => $total
                ];
            })->sortByDesc('total')->values();
    
            return response()->json([
                'sensor' => $sensor,
                'data' => $grouped
            ]);
        }
    
        return response()->json([
            'sensor' => $sensor,
            'data' => []
        ], 404);
    }
    

    public function getTop10AttackersBySensor($sensor)
{
    $response = Http::withHeaders([
        'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
    ])->timeout(180)->get("http://10.20.100.172:7777/summary/24h");

    if (!$response->successful()) {
        return response()->json([
            'sensor' => $sensor,
            'data' => []
        ], 404);
    }

    // Build tenant key, with fallback prefix "hp_" if needed
    $rawCode = strtolower(Auth::user()->user_code);
    $cleanCode = str_starts_with($rawCode, 'hp_') ? $rawCode : 'hp_' . $rawCode;
    $tenantKey = 'ewsdb_' . $cleanCode;

    $rawData = $response->json();

    // Cek apakah tenant key tersedia
    if (!isset($rawData[$tenantKey]['combined_attack'])) {
        return response()->json([
            'sensor' => $sensor,
            'data' => [],
            'message' => 'Tenant key not found: ' . $tenantKey
        ]);
    }

    $data = collect($rawData[$tenantKey]['combined_attack']);

    // Filter berdasarkan sensor, ignore case
    $filtered = $data->filter(function ($item) use ($sensor) {
        return isset($item['sensor'], $item['source_address']) &&
               strtolower($item['sensor']) === strtolower($sensor);
    });

    // Group by IP dan jumlahkan totalnya
    $grouped = $filtered->groupBy('source_address')->map(function ($items, $ip) {
        $total = $items->sum('total');
        return [
            'source_address' => $ip,
            'count' => $total,
            'total' => $total
        ];
    });

    return response()->json([
        'sensor' => $sensor,
        'data' => $grouped->sortByDesc('total')->take(10)->values()
    ]);
}



    public function totalAttackTenantDashboard(){
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
        ])->timeout(180)->get('http://10.20.100.172:7777/summary/24h');
    
        if ($response->successful()) {
            $rawCode = strtolower(Auth::user()->user_code);
            $cleanCode = str_starts_with($rawCode, 'hp_') ? $rawCode : 'hp_' . $rawCode;
            $tenant_code = 'ewsdb_' . $cleanCode;           

            //$tenant_code = 'ewsdb_hp_' . strtolower(Auth::user()->user_code) . '_1';
    
            $dataTenant = collect($response->json()[$tenant_code]['combined_attack'] ?? []);
    
            return response()->json([
                'total_attack' => number_format(round($dataTenant->sum('total'))),
                'average_total_attack_per_day' => number_format(round($dataTenant->sum('total') / 24)),
                'average_total_attack_per_minute' => number_format(round($dataTenant->sum('total') / (24 * 60))),
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
            $rawCode = strtolower(Auth::user()->user_code);
            $cleanCode = str_starts_with($rawCode, 'hp_') ? $rawCode : 'hp_' . $rawCode;
            $tenant_code = 'ewsdb_' . $cleanCode;


            //$tenant_code = 'ewsdb_hp_' . strtolower(Auth::user()->user_code) . '_1';
    
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
                        'total_attack' => number_format($entry['total']),
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
    
    public function getSensorAttackCountTenant() {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
        ])->timeout(180)->get('http://10.20.100.172:7777/summary/24h');
    
        if ($response->successful()) {
            $rawCode = strtolower(Auth::user()->user_code);
            $cleanCode = str_starts_with($rawCode, 'hp_') ? $rawCode : 'hp_' . $rawCode;
            $tenant_code = 'ewsdb_' . $cleanCode;
    
            $dataTenant = collect($response->json()[$tenant_code]['combined_attack'] ?? []);
    
            $sensorCounts = $dataTenant->groupBy('sensor')
                ->map(function ($items, $sensor) {
                    return [
                        'sensor' => $sensor ?? 'unknown',
                        'count' => number_format($items->count()),
                        'total_raw' => $items->count(),
                    ];
                })
                ->sortByDesc('total_raw')
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
    
    

    public function getDataAverageTenant(){
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
        ])->timeout(180)->get('http://10.20.100.172:7777/summary/24h');
    
        if ($response->successful()) {
            $rawCode = strtolower(Auth::user()->user_code);
            $cleanCode = str_starts_with($rawCode, 'hp_') ? $rawCode : 'hp_' . $rawCode;
            $tenant_code = 'ewsdb_' . $cleanCode;

            //$tenant_code = 'ewsdb_hp_' . strtolower(Auth::user()->user_code) . '_1';
    
            $dataTenant = collect($response->json()[$tenant_code]['combined_attack'] ?? [])
                ->filter(function($entry) {
                    return strtolower($entry['protocol']) !== 'heartbeat';
                });
    
            $sensorCounts = $dataTenant->groupBy('sensor')
                ->map(function ($items, $sensor) {
                    $total = $items->sum('total');
                    return [
                        'sensor' => $sensor,
                        'average_per_hour' => number_format(round($total / 24)),
                        'total_per_day' => number_format($total),
                        'average_per_minute' => number_format(round($total / (24 * 60))),
                            
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

    public function getDataTotalAttackAverageGuestDashboard(){
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
        ])->timeout(180)->get('http://10.20.100.172:7777/summary/24h');
    
        if ($response->successful()) {
            $summary = $response->json();
            $totalJam = 24;
            
            $totalAttack = 0;
        
            foreach ($summary as $tenant => $data) {
                foreach ($data['combined_attack'] ?? [] as $entry) {
                    $totalAttack += $entry['total'] ?? 0;
                }
            }
        
            $totalPerHour = round($totalAttack / $totalJam);
            $averagePerMinute = round($totalAttack / count($summary) / $totalJam);
        
            return response()->json([
                'average_total_attack_per_day' => number_format($totalPerHour),
                'total_attack' => number_format($totalAttack),
                'hours_counted' => number_format($totalJam),
                'average_total_attack_per_minute' => number_format($averagePerMinute),
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to fetch data!'
            ], 404);
        }
        
    }

    public function getTop10SourceIpGuest(){

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
    
            $topAttackersSourceIp = $combined->groupBy('source_address')
                ->map(function ($items, $ip) {
                    return [
                        'source_address' => $ip,
                        'total_attack' => collect($items)->sum('total')
                    ];
                })
                ->sortByDesc('total_attack')
                ->take(10)
                ->values();
    
            return response()->json([
                'total_attack' => [
                    'data' => $topAttackersSourceIp
                ]
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to fetch data!'
            ], 404);
        }

    }

    public function getDataDoughnutChart($sensor){
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
        ])->timeout(180)->get('http://10.20.100.172:7777/summary/24h');
    
        if (!$response->successful()) {
            return response()->json(['message' => 'Failed to fetch data!'], 404);
        }
    
        $rawCode = strtolower(Auth::user()->user_code);
        $cleanCode = str_starts_with($rawCode, 'hp_') ? $rawCode : 'hp_' . $rawCode;
        $tenant_code = 'ewsdb_' . $cleanCode;
    
        $dataTenant = collect($response->json()[$tenant_code]['combined_attack'] ?? [])
            ->filter(function($entry) {
                return strtolower($entry['protocol']) !== 'heartbeat';
            });
    
        // Filter berdasarkan sensor yang diberikan
        $filteredSensorData = $dataTenant->where('sensor', $sensor);
    
        // Kelompokkan dan jumlahkan berdasarkan source_address
        $topIps = $filteredSensorData->groupBy('source_address')
            ->map(function ($items, $ip) {
                return $items->sum('total');
            })
            ->sortDesc()
            ->take(10);
    
        // Format untuk Doughnut Chart dan detail
        $labels = $topIps->keys()->values();
        $data = $topIps->values();
        $details = $topIps->map(function ($total, $ip) {
            return ['ip' => $ip, 'total' => $total];
        })->values();
    
        return response()->json([
            'sensor' => $sensor,
            'ip_attack_chart' => [
                'labels' => $labels,
                'data' => $data
            ],
            'ip_attack_details' => $details
        ]);

    }


}
