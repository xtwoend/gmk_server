<?php

namespace App\Task;

use Carbon\Carbon;
use App\Model\Device;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Contract\StdoutLoggerInterface;

#[Crontab(name: "CheckConnection", rule: "* * * * *", callback: "execute", memo: "Check connection every 1 minutes")]
class CheckConnection
{
    #[Inject]
    private StdoutLoggerInterface $logger;

    public function execute()
    {
        $this->logger->info('run check connection '. date('Y-m-d H:i:s', time()));
        Device::where('last_connection', '<=', Carbon::now()->subMinute())->update(['connected' => false]);
        $offline = Device::where('connected', false)->get();

        foreach($offline as $off){
            $topic = 'data/gmk/k/connection';
            $listen = 'mqtt_1';
            $config = config('mqtt.servers')[$listen];
            $clientId = \Hyperf\Utils\Str::random(10);
            $mqtt = new \PhpMqtt\Client\MqttClient($config['host'], $config['port'], $clientId);
            
            $config = (new \PhpMqtt\Client\ConnectionSettings)
                ->setUsername($config['username'])
                ->setPassword($config['password']);

            $mqtt->connect($config, true);
            $mqtt->publish($topic, json_encode($off->toArray()), 0);
            $mqtt->disconnect();
        }
    }
}