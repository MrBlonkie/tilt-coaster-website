<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AutoControlController extends Controller
{
    private $espStationIp;
    private $espTiltDropIp;

    public function __construct()
    {
        $this->espStationIp = env('ESP_STATION_IP');
        $this->espTiltDropIp = env('ESP_TILTDROP_IP');
    }

    // --- Views ---
    public function index()
    {
        return view('auto-control');
    }

    // --- Station ESP ---
    public function dispatchControl()
    {
        $response = Http::post("http://{$this->espStationIp}/dispatch/go");
        return response()->json($response->json());
    }

    public function status()
    {
        $response = Http::get("http://{$this->espStationIp}/auto-control/status");
        return response()->json($response->json());
    }

    // --- TiltDrop ESP ---
    public function tiltdropOpen()
    {
        $response = Http::post("http://{$this->espTiltDropIp}/tiltdrop/open");
        return response()->json($response->json());
    }

    public function tiltdropClose()
    {
        $response = Http::post("http://{$this->espTiltDropIp}/tiltdrop/close");
        return response()->json($response->json());
    }

    public function tiltdropDrop()
    {
        $response = Http::post("http://{$this->espTiltDropIp}/tiltdrop/drop");
        return response()->json($response->json());
    }

    public function tiltdropStatus()
    {
        $response = Http::get("http://{$this->espTiltDropIp}/tiltdrop/status");
        return response()->json($response->json());
    }
}
