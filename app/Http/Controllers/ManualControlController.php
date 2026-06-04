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

        $station = $stationJson ? json_decode($stationJson, true) : null;
        $tiltdrop = $tiltdropJson ? json_decode($tiltdropJson, true) : null;
        $brakes = $brakesJson ? json_decode($brakesJson, true) : null;
        $switchtrack = $switchtrackJson ? json_decode($switchtrackJson, true) : null;

        return view('manual-control', compact('station', 'tiltdrop', 'brakes', 'switchtrack'));
    }

    public function indexV2()
    {
        $stationJson     = Cache::get('mqtt_last_station_status');
        $tiltdropJson    = Cache::get('mqtt_last_tiltdrop_status');
        $brakesJson      = Cache::get('mqtt_last_brakes_status');
        $switchtrackJson = Cache::get('mqtt_last_switchtrack_status');

        if (!$stationJson)     $stationJson     = MqttMessage::where('topic', 'station/status')->latest()->value('message');
        if (!$tiltdropJson)    $tiltdropJson    = MqttMessage::where('topic', 'tiltdrop/status')->latest()->value('message');
        if (!$brakesJson)      $brakesJson      = MqttMessage::where('topic', 'brakes/status')->latest()->value('message');
        if (!$switchtrackJson) $switchtrackJson = MqttMessage::where('topic', 'switchtrack/status')->latest()->value('message');

        $station     = $stationJson     ? json_decode($stationJson,     true) : null;
        $tiltdrop    = $tiltdropJson    ? json_decode($tiltdropJson,    true) : null;
        $brakes      = $brakesJson      ? json_decode($brakesJson,      true) : null;
        $switchtrack = $switchtrackJson ? json_decode($switchtrackJson, true) : null;

        return view('manual-control-v2', compact('station', 'tiltdrop', 'brakes', 'switchtrack'));
    }

}