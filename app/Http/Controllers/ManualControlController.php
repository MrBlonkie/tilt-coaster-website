<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ManualControlController extends Controller
{
    private $espStationIp;
    private $espTiltDropIp;

    public function __construct()
    {
        $this->espStationIp = env('ESP_STATION_IP');
        $this->espTiltDropIp = env('ESP_TILTDROP_IP');
    }

    public function index()
    {
        return view('manual-control');
    }

    public function ledControl($state)
    {
        $response = Http::post("http://{$this->espStationIp}/led/{$state}");
        return response()->json($response->json());
    }

    public function stationMotorControl($state)
    {
        $response = Http::post("http://{$this->espStationIp}/manual/stationmotor/{$state}");
        return response()->json($response->json());
    }

    public function lifthillMotorControl($state)
    {
        $response = Http::post("http://{$this->espStationIp}/manual/lifthillmotor/{$state}");
        return response()->json($response->json());
    }

    public function tiltdropMotorControl($state)
    {
        $response = Http::post("http://{$this->espTiltDropIp}/manual/tiltdropmotor/{$state}");
        return response()->json($response->json());
    }

    public function releasedropMotorControl($state)
    {
        $response = Http::post("http://{$this->espTiltDropIp}/manual/releasedropmotor/{$state}");
        return response()->json($response->json());
    }

    public function status()
    {
        try {
            $stationResponse = Http::timeout(1)->get("http://{$this->espStationIp}/auto-control/status")->json();
        } catch (\Exception $e) {
            $stationResponse = ['error' => 'Station ESP niet bereikbaar'];
        }

        try {
            $tiltdropResponse = Http::timeout(1)->get("http://{$this->espTiltDropIp}/auto-control/status")->json();
        } catch (\Exception $e) {
            $tiltdropResponse = ['error' => 'Tiltdrop ESP niet bereikbaar'];
        }

        return response()->json([
            'station' => $stationResponse,
            'tiltdrop' => $tiltdropResponse,
        ]);
    }


    public function manualMode($state)
    {
        $stationResponse = Http::get("http://{$this->espStationIp}/manual/{$state}");
        $tiltdropResponse = Http::get("http://{$this->espTiltDropIp}/manual/{$state}");

        return response()->json([
            'station' => $stationResponse->json(),
            'tiltdrop' => $tiltdropResponse->json(),
        ]);
    }



}


