<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
        ])->timeout(180)->get('http://10.20.100.172:7777/summary/24h');
        
        $data = $response->json();

        $sensors = [
            'conpot' => 'conpot',
            'honeytrap' => 'honeytrap',
            'cowrie' => 'cowrie',
            'dionaea' => 'dionaea',
            'rdpy' => 'rdpy',
            'dionaea_ews' => 'dionaea_ews',
            'elasticpot' => 'elasticpot',
        ];

        $sensorStatus = [];

        foreach ($sensors as $key => $name) {
            $logData = data_get($data, "sensor_latest_logs.$key", null);
    
            if ($logData && is_array($logData) && count($logData) > 0) {
                $latest = collect($logData)->sortByDesc('timestamp')->first();
                $status = 'ACTIVE';
                $timestamp = \Carbon\Carbon::parse($latest['timestamp'])->format('d M Y H:i:s');
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


        return view('pages.monitoring-sensor');
    }

    public function getSensorStatusJson() {
        $role = Auth::user()->role;
        $tenantKey = 'ewsdb_' . $role;
        
        // Log tenant key yang akan digunakan
        Log::info("Tenant Key:", ['tenantKey' => $tenantKey]);
    
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
        ])->timeout(180)->get('http://10.20.100.172:7777/summary/24h');
    
        $data = $response->json();
        
        // Log seluruh response API untuk melihat struktur data mentah
        Log::info("API Response Keys:", ['keys' => array_keys($data)]);
        
        // Log khusus data tenant yang kita cari
        $tenantData = data_get($data, $tenantKey, []);
        Log::info("Tenant Data:", ['tenantKey' => $tenantKey, 'exists' => !empty($tenantData), 'keys' => empty($tenantData) ? [] : array_keys($tenantData)]);
        
        // Jika tenant data kosong, coba lihat semua tenant yang tersedia
        if (empty($tenantData)) {
            $possibleTenants = array_filter(array_keys($data), function($key) {
                return strpos($key, 'ewsdb_') === 0;
            });
            Log::info("Possible tenant keys:", ['tenants' => $possibleTenants]);
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
        Log::info("Sensor Logs:", [
            'exists' => !empty($sensorLogs), 
            'keys' => empty($sensorLogs) ? [] : array_keys($sensorLogs)
        ]);
    
        $sensorStatus = [];
    
        foreach ($sensorKeys as $key => $name) {
            $logDataRaw = Arr::get($sensorLogs, $key);
            
            // Log data untuk setiap sensor
            Log::info("Sensor {$key}:", [
                'exists' => !empty($logDataRaw),
                'type' => gettype($logDataRaw),
                'data' => $logDataRaw
            ]);
            
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

}
