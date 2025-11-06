<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Models\MqttMessage;

class MqttListener extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Listen to MQTT topics and save messages';

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

        $mqtt->subscribe('#', function ($topic, $message) {
            // Console output
            $this->info("Received message on topic [$topic]: $message");

            // Opslaan in database
            MqttMessage::create([
                'topic' => $topic,
                'message' => $message
            ]);
        }, 0);

        // Infinite loop
        $mqtt->loop(true);
    }
}
