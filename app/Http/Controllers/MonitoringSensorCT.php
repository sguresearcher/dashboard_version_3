<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MonitoringSensorCT extends Controller
{
    private $username;
    private $password;

    public function __construct()
    {
        $this->username = env('API_USERNAME_SECRETO'); 
        $this->password = env('API_PASSWORD_SECRETO');
    }

    public function index()
    {

        // $response = Http::withHeaders([
        //     'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
        // ])->timeout(180)->get('http://10.20.100.172:7777/summary/24h');
        
        // $data = $response->json();

        // $sensors = [
        //     'conpot' => 'conpot',
        //     'honeytrap' => 'honeytrap',
        //     'cowrie' => 'cowrie',
        //     'dionaea' => 'dionaea',
        //     'rdpy' => 'rdpy',
        //     'dionaea_ews' => 'dionaea_ews',
        //     'elasticpot' => 'elasticpot',
        // ];

        // $sensorStatus = [];

        // foreach ($sensors as $key => $name) {
        //     $logData = data_get($data, "sensor_latest_logs.$key", null);
    
        //     if ($logData && is_array($logData) && count($logData) > 0) {
        //         $latest = collect($logData)->sortByDesc('timestamp')->first();
        //         $status = 'ACTIVE';
        //         $timestamp = \Carbon\Carbon::parse($latest['timestamp'])->format('d M Y H:i:s');
        //     } else {
        //         $status = 'UNACTIVE';
        //         $timestamp = null;
        //     }
    
        //     $sensorStatus[] = [
        //         'name' => $name,
        //         'status' => $status,
        //         'timestamp' => $timestamp
        //     ];
        // }


        return view('pages.monitoring-sensor');
    }

    public function getSensorStatusJson() {

        $tenantKey = 'ewsdb_' . Auth::user()->user_code;
        
        // Log tenant key yang akan digunakan
    
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
        ])->timeout(180)->get('http://10.20.100.172:7777/summary/24h');
    
        $data = $response->json();
        
        // Log seluruh response API untuk melihat struktur data mentah
        
        // Log khusus data tenant yang kita cari
        $tenantData = data_get($data, $tenantKey, []);
        
        // Jika tenant data kosong, coba lihat semua tenant yang tersedia
        if (empty($tenantData)) {
            $possibleTenants = array_filter(array_keys($data), function($key) {
                return strpos($key, 'ewsdb_') === 0;
            });
        }
    
        $sensorKeys = [
            'conpot' => 'conpot',
            'honeytrap' => 'honeytrap',
            'cowrie' => 'cowrie',
            'dionaea' => 'dionaea',
            'rdpy' => 'rdpy',
            'dionaea_ews' => 'dionaea_ews',
            'elasticpot' => 'elasticpot',
        ];
    
        // Log keberadaan sensor_latest_logs
        $sensorLogs = Arr::get($tenantData, 'sensor_latest_logs', []);
    
        $sensorStatus = [];
    
        foreach ($sensorKeys as $key => $name) {
            $logDataRaw = Arr::get($sensorLogs, $key);
            
            
            // Sisanya sama seperti sebelumnya...
            if (!empty($logDataRaw)) {
                if (is_array($logDataRaw) && isset($logDataRaw[0])) {
                    $latest = collect($logDataRaw)->sortByDesc('timestamp')->first();
                } else {
                    $latest = $logDataRaw;
                }
                
                if (isset($latest['timestamp'])) {
                    $status = 'ACTIVE';
                    $timestamp = \Carbon\Carbon::parse($latest['timestamp'])->format('d M Y H:i:s');
                } else {
                    $status = 'UNACTIVE';
                    $timestamp = null;
                }
            } else {
                $status = 'UNACTIVE';
                $timestamp = null;
            }
    
            $sensorStatus[] = [
                'name' => $name,
                'status' => $status,
                'timestamp' => $timestamp
            ];
        }
    
        // Log hasil akhir
        Log::info("Final Sensor Status:", ['status' => $sensorStatus]);
    
        return response()->json($sensorStatus);
    }

    public function getSensorAllTenantStatus() {
        // Mengambil data dari API
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
        ])->timeout(180)->get('http://10.20.100.172:7777/summary/24h');
    
        $data = $response->json();
    
        // Log available tenant keys di API
        $apiTenantKeys = array_keys($data);
        Log::info("API Tenant Keys:", ['keys' => $apiTenantKeys]);
    
        // Array untuk menyimpan hasil status sensor per tenant
        $allTenantSensorStatus = [];
    
        // Daftar sensor yang ingin dipantau
        $sensorKeys = [
            'conpot' => 'conpot',
            'honeytrap' => 'honeytrap',
            'cowrie' => 'cowrie',
            'dionaea' => 'dionaea',
            'rdpy' => 'rdpy',
            'dionaea_ews' => 'dionaea_ews',
            'elasticpot' => 'elasticpot',
        ];
    
        // Loop melalui semua tenant key yang tersedia di API
        foreach ($apiTenantKeys as $tenantKey) {
            // Periksa apakah ini tenant yang valid (dimulai dengan 'ewsdb_')
            if (strpos($tenantKey, 'ewsdb_') !== 0) {
                continue; // Skip jika bukan tenant key
            }
    
            $tenantData = $data[$tenantKey];
            
            // Periksa apakah sensor_latest_logs ada di tenant
            if (!isset($tenantData['sensor_latest_logs'])) {
                Log::warning("sensor_latest_logs not found:", ['tenantKey' => $tenantKey]);
                continue; // Skip tenant tanpa sensor logs
            }
    
            $sensorLogs = $tenantData['sensor_latest_logs'];
            
            $tenantSensorStatus = [
                'tenant' => $tenantKey,
                'sensors' => []
            ];
    
            // Loop untuk setiap sensor
            foreach ($sensorKeys as $key => $name) {
                $logDataRaw = Arr::get($sensorLogs, $key);
                
                if (!empty($logDataRaw)) {
                    if (is_array($logDataRaw)) {
                        if (isset($logDataRaw[0])) {
                            // Jika array dengan index numerik
                            $latest = collect($logDataRaw)->sortByDesc('timestamp')->first();
                        } else {
                            // Jika associative array
                            $latest = $logDataRaw;
                        }
                    } else {
                        $latest = $logDataRaw;
                    }
                    
                    if (isset($latest['timestamp'])) {
                        $status = 'ACTIVE';
                        $timestamp = \Carbon\Carbon::parse($latest['timestamp'])->format('d M Y H:i:s');
                    } else {
                        $status = 'UNACTIVE';
                        $timestamp = null;
                    }
                } else {
                    $status = 'UNACTIVE';
                    $timestamp = null;
                }
    
                $tenantSensorStatus['sensors'][] = [
                    'name' => $name,
                    'status' => $status,
                    'timestamp' => $timestamp
                ];
            }
    
            $allTenantSensorStatus[] = $tenantSensorStatus;
        }
    
        return response()->json($allTenantSensorStatus);
    }
    

}
