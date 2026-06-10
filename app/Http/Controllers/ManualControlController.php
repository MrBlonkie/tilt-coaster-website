<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\MqttMessage;

class ManualControlController extends Controller
{

    public function index()
    {
        $stationJson     = Cache::get('mqtt_last_station_status');
        $tiltdropJson    = Cache::get('mqtt_last_tiltdrop_status');
        $brakesJson      = Cache::get('mqtt_last_brakes_status');
        $switchtrackJson = Cache::get('mqtt_last_switchtrack_status');

        if (!$stationJson) {
            $stationJson = MqttMessage::where('topic', 'station/status')->latest()->value('message');
        }
        if (!$tiltdropJson) {
            $tiltdropJson = MqttMessage::where('topic', 'tiltdrop/status')->latest()->value('message');
        }
        if (!$brakesJson) {
            $brakesJson = MqttMessage::where('topic', 'brakes/status')->latest()->value('message');
        }
        if (!$switchtrackJson) {
            $switchtrackJson = MqttMessage::where('topic', 'switchtrack/status')->latest()->value('message');
        }

        $station     = $this->isJson($stationJson) ? json_decode($stationJson, true) : null;
        $tiltdrop    = $this->isJson($tiltdropJson) ? json_decode($tiltdropJson, true) : null;
        $brakes      = $this->isJson($brakesJson) ? json_decode($brakesJson, true) : null;
        $switchtrack = $this->isJson($switchtrackJson) ? json_decode($switchtrackJson, true) : null;

        return view('manual-control', compact('station', 'tiltdrop', 'brakes', 'switchtrack'));
    }

    private function isJson(?string $string): bool
    {
        if (!$string) return false;
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

}