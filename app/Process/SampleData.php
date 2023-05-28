<?php

declare(strict_types=1);

namespace App\Process;

use Carbon\Carbon;
use App\Model\Device;
use Hyperf\Utils\Str;
use App\Mqtt\Extractor;
use App\Event\MQTTReceived;
use PhpMqtt\Client\MqttClient;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\Annotation\Process;
use Hyperf\Contract\StdoutLoggerInterface;

#[Process(name: 'SampleData')]
class SampleData extends AbstractProcess
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

        $leepack1 = file_get_contents(BASE_PATH . '/mqtt_data/leepack1.json');
        $leepack2 = file_get_contents(BASE_PATH . '/mqtt_data/leepack2.json');
        $leepack3 = file_get_contents(BASE_PATH . '/mqtt_data/leepack3.json');
        $lme1 = file_get_contents(BASE_PATH . '/mqtt_data/lme1.json');
        $lme2 = file_get_contents(BASE_PATH . '/mqtt_data/lme2.json');
        $lme3 = file_get_contents(BASE_PATH . '/mqtt_data/lme3.json');
        $pku1 = file_get_contents(BASE_PATH . '/mqtt_data/pku1.json');
        
        while (true) {
            
            $eleepack1 = json_decode($leepack1, true);
            $eleepack1['ts'] = Carbon::now()->format('Y-m-d H:i:s');

            $eleepack2 = json_decode($leepack2, true);
            $eleepack2['ts'] = Carbon::now()->format('Y-m-d H:i:s');

            $eleepack3 = json_decode($leepack3, true);
            $eleepack3['ts'] = Carbon::now()->format('Y-m-d H:i:s');

            $elme1 = json_decode($lme1, true);
            $elme1['ts'] = Carbon::now()->format('Y-m-d H:i:s');

            $elme2 = json_decode($lme2, true);
            $elme2['ts'] = Carbon::now()->format('Y-m-d H:i:s');

            $elme3 = json_decode($lme3, true);
            $elme3['ts'] = Carbon::now()->format('Y-m-d H:i:s');
            
            $epku1 = json_decode($pku1, true);
            $epku1['ts'] = Carbon::now()->format('Y-m-d H:i:s');

            $mqtt->publish('data/gmk/k/leepack1', json_encode($eleepack1), 0);
            $mqtt->publish('data/gmk/k/leepack2', json_encode($eleepack2), 0);
            $mqtt->publish('data/gmk/k/leepack3', json_encode($eleepack3), 0);
            $mqtt->publish('data/gmk/k/lme1', json_encode($elme1), 0);
            $mqtt->publish('data/gmk/k/lme2', json_encode($elme2), 0);
            $mqtt->publish('data/gmk/k/lme3', json_encode($elme3), 0);


            // $mqtt->publish('data/gmk/k/pku1', json_encode($epku1), 0);

            sleep(1);
        }
        $mqtt->disconnect();
    }

    public function isEnable($server): bool
    {
        return (boolean) env('SAMPLE_ENABLE', false);
    }
}
