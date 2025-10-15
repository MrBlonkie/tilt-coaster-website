<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ManualControlController extends Controller
{
    private $espIp;

    public function __construct()
    {
        $this->espIp = env('ESP_STATION_IP');
    }

    public function index()
    {
        return view('manual-control');
    }

    public function ledControl($state)
    {
        $response = Http::post("http://{$this->espIp}/led/{$state}");
        return response()->json($response->json());
    }

    public function stationMotorControl($state)
    {
        $response = Http::post("http://{$this->espIp}/manual/stationmotor/{$state}");
        return response()->json($response->json());
    }

    public function lifthillMotorControl($state)
    {
        $response = Http::post("http://{$this->espIp}/manual/lifthillmotor/{$state}");
        return response()->json($response->json());
    }

    public function status()
    {
        $response = Http::get("http://{$this->espIp}/auto-control/status");
        return response()->json($response->json());
    }

    public function manualMode($state)
    {
        $response = Http::get("http://{$this->espIp}/manual/{$state}");
        return response()->json($response->json());
    }


}


