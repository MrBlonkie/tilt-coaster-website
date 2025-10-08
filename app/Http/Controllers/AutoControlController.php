<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AutoControlController extends Controller
{
    private $espIp;

    public function __construct()
    {
        $this->espIp = env('ESP_IP');
    }

    public function index()
    {
        return view('auto-control');
    }

    public function dispatchControl()
    {
        $response = Http::post("http://{$this->espIp}/dispatch/go");
        return response()->json($response->json());
    }

    public function status()
    {
        $response = Http::get("http://{$this->espIp}/auto-control/status");
        return response()->json($response->json());
    }


}


