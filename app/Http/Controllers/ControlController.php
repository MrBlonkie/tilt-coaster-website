<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ControlController extends Controller
{
    private $espIp;

    public function __construct()
    {
        $this->espIp = env('ESP_IP');
    }

    public function index()
    {
        return view('control');
    }

    public function ledControl($state)
    {
        $response = Http::post("http://{$this->espIp}/led/{$state}");
        return response()->json($response->json());
    }

    public function stationMotorControl($state)
    {
        $response = Http::post("http://{$this->espIp}/motor/station/{$state}");
        return response()->json($response->json());
    }

    public function lifthillMotorControl($state)
    {
        $response = Http::post("http://{$this->espIp}/motor/lifthill/{$state}");
        return response()->json($response->json());
    }

    public function status()
    {
        $response = Http::get("http://{$this->espIp}/control/status");
        return response()->json($response->json());
    }
}


