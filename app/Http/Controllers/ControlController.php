<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;

class ControlController extends Controller
{
    public $espIp;

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
        $espIp = env('ESP_IP');
        $res = Http::post("http://$espIp/control/$state");
        return response()->json($res->json());
    }

    public function motorControl($state)
    {
        $espIp = env('ESP_IP');
        $res = Http::post("http://$espIp/motor/$state");
        return response()->json($res->json());
    }

    public function status()
    {
        $espIp = env('ESP_IP');
        $res = Http::get("http://$espIp/control/status");
        return response()->json($res->json());
    }
}
