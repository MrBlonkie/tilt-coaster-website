<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ControlController extends Controller
{
    public function index()
    {
        return view('control');
    }

    public function toggle(Request $request, $state)
    {
        $espIp = env('ESP_IP', '192.168.1.50');
        $url = "http://{$espIp}/control/{$state}";

        try {
            $resp = Http::timeout(3)->get($url);
            return response()->json(['ok' => true, 'esp' => $resp->json()]);
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

}
