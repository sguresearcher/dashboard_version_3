<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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

public function getSensorStatusJson(){

        $tenantKey = 'ewsdb_'. Auth::user()->role;

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
        ])->timeout(180)->get('http://10.20.100.172:7777/summary/24h');

        $data = $response->json();
        $tenantData = data_get($data, $tenantKey, []);

        $sensorKeys = [
            'conpot' => 'conpot',
            'honeytrap' => 'honeytrap',
            'cowrie' => 'cowrie',
            'dionaea' => 'dionaea',
            'rdpy' => 'rdpy',
            'dionaea_ews' => 'dionaea_ews',
            'elasticpot' => 'elasticpot',
        ];

        $sensorStatus = [];

        foreach ($sensorKeys as $key => $name) {
            $logDataRaw = Arr::get($tenantData, "sensor_latest_logs.$key");

            $logData = is_array($logDataRaw) ? array_values($logDataRaw) : [];

            if (!empty($logData)) {
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


        return response()->json($sensorStatus);
    }

}
