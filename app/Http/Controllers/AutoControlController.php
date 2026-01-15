<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\MqttMessage;

class AutoControlController extends Controller
{

    public function index()
    {
        $stationJson   = Cache::get('mqtt_last_station/status') ?? MqttMessage::where('topic', 'station/status')->latest()->value('message');
        $tiltdropJson  = Cache::get('mqtt_last_tiltdrop/status') ?? MqttMessage::where('topic', 'tiltdrop/status')->latest()->value('message');
        $brakesJson  = Cache::get('mqtt_last_brakes/status') ?? MqttMessage::where('topic', 'brakes/status')->latest()->value('message');
        $switchtrackJson  = Cache::get('mqtt_last_switchtrack/status') ?? MqttMessage::where('topic', 'switchtrack/status')->latest()->value('message');

        // === Alleen valide JSON decoden ===
        $station  = $this->isJson($stationJson) ? json_decode($stationJson, true) : null;
        $tiltdrop = $this->isJson($tiltdropJson) ? json_decode($tiltdropJson, true) : null;
        $brakes = $this->isJson($brakesJson) ? json_decode($brakesJson, true) : null;
        $switchtrack = $this->isJson($switchtrackJson) ? json_decode($switchtrackJson, true) : null;

        // === Heartbeat initiële status voor frontend ===
        $stationOnline   = 'unknown';
        $tiltdropOnline  = 'unknown';
        $brakesOnline  = 'unknown';
        $switchtrackOnline = 'unknown';

        return view('auto-control', compact('station', 'tiltdrop', 'brakes', 'switchtrack', 'stationOnline', 'tiltdropOnline', 'brakesOnline', 'switchtrackOnline'));
    }

    /**
     * Check of een string valide JSON is
     */
    private function isJson(?string $string): bool
    {
        if (!$string) return false;
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
