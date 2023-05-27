<?php

declare(strict_types=1);

namespace App\Process;

use App\Model\Device;
use Hyperf\Utils\Str;
use App\Mqtt\Extractor;
use App\Event\MQTTReceived;
use PhpMqtt\Client\MqttClient;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\Annotation\Process;
use Hyperf\Contract\StdoutLoggerInterface;

#[Process(name: 'MQTTReceiver')]
class MQTTReceiver extends AbstractProcess
{
    public function handle(): void
    {
        $listen = 'mqtt_1';
        $config = config('mqtt.servers')[$listen];

        $clientId = Str::random(10);
        
        $logger = $this->container->get(StdoutLoggerInterface::class);
        $event = $this->event;
        $mqtt = new MqttClient($config['host'], $config['port'], $clientId);
        
        $config = (new \PhpMqtt\Client\ConnectionSettings)
            ->setUsername($config['username'])
            ->setPassword($config['password']);

        $mqtt->connect($config, true);

        foreach(Device::active()->where('mqtt_server', $listen)->get() as $device) {
            $mqtt->subscribe($device->topic, function ($topic, $message) use ($logger, $event, $device) {
                $data = (new Extractor($message))->toArray();
                $event->dispatch(new MQTTReceived($data, $message, $topic, $device));
                // $logger->info('Received Topic: '. $topic);
            }, 0);
        }

        $mqtt->loop(true);
        $mqtt->disconnect();
    }
}
