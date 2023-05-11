<?php

namespace App\Task;

use Carbon\Carbon;
use App\Model\Alarm;
use App\Model\Device;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Contract\StdoutLoggerInterface;

#[Crontab(name: "CloseAlarm", rule: "*\/5 * * * * *", callback: "execute", memo: "Close alarm if opened until 1 minutes")]
class CloseAlarm
{
    #[Inject]
    private StdoutLoggerInterface $logger;

    public function execute()
    {
        $this->logger->info('Crontab alarm runing '. date('Y-m-d H:i:s', time()));

        foreach(Device::active()->get() as $device) {
            $alarm = Alarm::table($device->id)
            ->where('status', 1)
            ->where('finished_at', '<', Carbon::now()->subMinutes(2)->format('Y-m-d H:i:s'))
            ->first();

            if($alarm) {
                $alarm->update(['status' => 0]);

                $topics = [
                    1 => 'data/gmk/k/leepack1/alarm',
                    2 => 'data/gmk/k/leepack2/alarm',
                    3 => 'data/gmk/k/leepack3/alarm',
                    7 => 'data/gmk/k/lme1/alarm',
                    8 => 'data/gmk/k/lme2/alarm',
                    9 => 'data/gmk/k/lme3/alarm'
                ];

                $topic = $topics[$device->id] ?? 'data/gmk/k/general';
                $listen = 'mqtt_1';
                $config = config('mqtt.servers')[$listen];
                $clientId = \Hyperf\Utils\Str::random(10);
                $mqtt = new \PhpMqtt\Client\MqttClient($config['host'], $config['port'], $clientId);
                
                $config = (new \PhpMqtt\Client\ConnectionSettings)
                    ->setUsername($config['username'])
                    ->setPassword($config['password']);

                $mqtt->connect($config, true);
                $mqtt->publish($topic, json_encode($alarm->toArray()), 0);
                $mqtt->disconnect();
            }
        }
    }
}