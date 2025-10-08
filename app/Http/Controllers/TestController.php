<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    private $espIp;

    public function __construct()
    {
        $this->espIp = env('ESP_IP');
    }

    public function index()
    {
        return view('test');
    }

    public function status()
    {
        $response = Http::get("http://{$this->espIp}/auto-control/status");
        return response($response->body(), 200)->header('Content-Type', 'application/json');
    }

    public function manualOn()
    {
        return $this->forwardPost("/manual/on");
    }

    public function manualOff()
    {
        return $this->forwardPost("/manual/off");
    }

    public function stationMotorOn()
    {
        return $this->forwardPost("/manual/stationmotor/on");
    }

    public function stationMotorOff()
    {
        return $this->forwardPost("/manual/stationmotor/off");
    }

    public function lifthillMotorOn()
    {
        return $this->forwardPost("/manual/lifthillmotor/on");
    }

    public function lifthillMotorOff()
    {
        return $this->forwardPost("/manual/lifthillmotor/off");
    }

    public function dispatchGo()
    {
        return $this->forwardPost("/dispatch/go");
    }

    private function forwardPost(string $path)
    {
        $response = Http::post("http://{$this->espIp}{$path}");
        return response($response->body(), 200)->header('Content-Type', 'application/json');
    }
}
