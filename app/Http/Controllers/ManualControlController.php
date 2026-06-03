<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\MqttMessage;

class ManualControlController extends Controller
{

    public function index()
    {
        $stationJson = Cache::get('mqtt_last_station/status');
        $tiltdropJson = Cache::get('mqtt_last_tiltdrop/status');
        $brakesJson = Cache::get('mqtt_last_brakes/status');
        $switchtrackJson = Cache::get('mqtt_last_switchtrack/status');
        
        $stationOnline = 'unknown';
        $tiltdropOnline = 'unknown';
        $brakesOnline = 'unknown';
        $switchtrackOnline = 'unknown';


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

        return view('manual-control', compact('station', 'tiltdrop', 'brakes', 'switchtrack', 'stationOnline', 'tiltdropOnline', 'brakesOnline', 'switchtrackOnline'));
    }

}