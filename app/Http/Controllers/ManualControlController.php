<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\MqttMessage;

class ManualControlController extends Controller
{
    private $espStationIp;
    private $espTiltDropIp;

    public function __construct()
    {
        $this->espStationIp = env('ESP_STATION_IP');
        $this->espTiltDropIp = env('ESP_TILTDROP_IP');
    }

    public function index()
    {
        $stationJson = Cache::get('mqtt_last_station/status');
        $tiltdropJson = Cache::get('mqtt_last_tiltdrop/status');
        
        // === HEARTBEAT AANPASSING START ===
        // De status ('online'/'offline') wordt nu volledig door de frontend (browser) beheerd
        // met behulp van de Heartbeat-timer. We zetten de initiële waarden op 'unknown'.
        $stationOnline = 'unknown';
        $tiltdropOnline = 'unknown';
        // === HEARTBEAT AANPASSING EIND ===


        if (!$stationJson) {
            $stationJson = MqttMessage::where('topic', 'station/status')->latest()->value('message');
        }
        if (!$tiltdropJson) {
            $tiltdropJson = MqttMessage::where('topic', 'tiltdrop/status')->latest()->value('message');
        }

        $station = $stationJson ? json_decode($stationJson, true) : null;
        $tiltdrop = $tiltdropJson ? json_decode($tiltdropJson, true) : null;

        return view('manual-control', compact('station', 'tiltdrop', 'stationOnline', 'tiltdropOnline'));
    }

}