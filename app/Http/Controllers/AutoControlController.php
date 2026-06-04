<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\MqttMessage;

class AutoControlController extends Controller
{

    public function index()
    {
        $stationJson     = Cache::get('mqtt_last_station_status') ?? MqttMessage::where('topic', 'station/status')->latest()->value('message');
        $tiltdropJson    = Cache::get('mqtt_last_tiltdrop_status') ?? MqttMessage::where('topic', 'tiltdrop/status')->latest()->value('message');
        $brakesJson      = Cache::get('mqtt_last_brakes_status') ?? MqttMessage::where('topic', 'brakes/status')->latest()->value('message');
        $switchtrackJson = Cache::get('mqtt_last_switchtrack_status') ?? MqttMessage::where('topic', 'switchtrack/status')->latest()->value('message');

        $station     = $this->isJson($stationJson) ? json_decode($stationJson, true) : null;
        $tiltdrop    = $this->isJson($tiltdropJson) ? json_decode($tiltdropJson, true) : null;
        $brakes      = $this->isJson($brakesJson) ? json_decode($brakesJson, true) : null;
        $switchtrack = $this->isJson($switchtrackJson) ? json_decode($switchtrackJson, true) : null;

        return view('auto-control', compact('station', 'tiltdrop', 'brakes', 'switchtrack'));
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
