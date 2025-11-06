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
    protected $description = 'Listen to MQTT topics and cache latest messages';

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

        // Abonneer enkel op de twee topics die je nodig hebt
        $topics = ['station/status', 'tiltdrop/status'];

        foreach ($topics as $topic) {
            $mqtt->subscribe($topic, function ($topic, $message) {
                $this->info("Received message on [$topic]: $message");

                // Cache het laatste bericht per topic
                Cache::put("mqtt_last_{$topic}", $message, now()->addHours(6));

                // Log optioneel naar DB
                MqttMessage::create([
                    'topic' => $topic,
                    'message' => $message
                ]);
            }, 0);
        }

        $mqtt->loop(true);
    }
}
