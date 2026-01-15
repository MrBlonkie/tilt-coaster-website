<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Models\MqttMessage;
use Illuminate\Support\Facades\Cache;

class MqttListener extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Listen to MQTT topics and cache latest messages, including LWT';

    public function handle()
    {
        $server = env('PI_IP');
        $port = env('PI_PORT');
        $clientId = 'laravel_mqtt_listener';
        $clean_session = true;
        $mqtt_version = MqttClient::MQTT_3_1;

        $connectionSettings = (new ConnectionSettings)
            ->setConnectTimeout(10)
            ->setUseTls(false)
            ->setTlsSelfSignedAllowed(true)
            ->setKeepAliveInterval(60);

        $mqtt = new MqttClient($server, $port, $clientId, $mqtt_version);
        $mqtt->connect($connectionSettings, $clean_session);
        $this->info("MQTT client connected");

        // Luister naar ALLE topics die relevant zijn
        $topics = [
            'station/status',
            'tiltdrop/status',
            'brakes/status',
            'switchtrack/status',
            'rollercoaster/station/status',
            'rollercoaster/tiltdrop/status',
            'rollercoaster/brakes/status',
            'rollercoaster/switchtrack/status',
        ];

        foreach ($topics as $topic) {
            $mqtt->subscribe($topic, function ($topic, $message) {
                $this->info("Received message on [$topic]: $message");

                // --- 1. Detecteer LWT berichten ---
                if (str_starts_with($topic, 'rollercoaster/')) {
                    // Cache LWT status apart
                    Cache::put("mqtt_lwt_" . str_replace('/', '_', $topic), $message, now()->addMinutes(10));
                    $this->info("→ LWT cached as mqtt_lwt_" . str_replace('/', '_', $topic));
                }

                // --- 2. Cache gewone status JSON ---
                else {
                    Cache::put("mqtt_last_" . str_replace('/', '_', $topic), $message, now()->addHours(6));
                    $this->info("→ Status cached as mqtt_last_" . str_replace('/', '_', $topic));
                }

                // --- 3. Optioneel loggen in DB ---
                MqttMessage::create([
                    'topic' => $topic,
                    'message' => $message
                ]);
            }, 0);
        }

        $mqtt->loop(true);
    }
}
