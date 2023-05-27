<?php

return [
    'default' => 'mqtt_1',
    'interval_save' => 60,
    'servers' => [
        'mqtt_1' => [
            'host' => env('MQTT1_HOST', 'broker.hivemq.com'),
            'port' => (int) env('MQTT1_PORT', 1883),
            'username' => env('MQTT1_USER', null),
            'password' => env('MQTT1_PASS', null),
        ]
    ]
];