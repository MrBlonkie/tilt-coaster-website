<?php

namespace App\Http\Controllers;

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

    public function toggle($state)
    {
        $url = "http://{$this->espIp}/control/$state";

        // POST request naar ESP
        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n",
            ]
        ];
        $context = stream_context_create($options);

        $result = file_get_contents($url, false, $context);

        return response($result)->header('Content-Type', 'application/json');
    }

    public function espStatus()
    {
        $espIp = env('ESP_IP');

        try {
            $response = file_get_contents("http://$espIp/control/status");
            return response($response)->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Kan ESP niet bereiken'], 500);
        }
    }
}
