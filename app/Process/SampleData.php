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
        
        while (true) {
            $jayParsedAry = [
                "alarm_leepack1" => [
                    false, 
                    true, 
                    true, 
                    false, 
                    false, 
                    false, 
                    false, 
                    false, 
                    false, 
                    false, 
                    false, 
                    false, 
                    false, 
                    false, 
                    false, 
                    false, 
                    false, 
                    false, 
                    false, 
                    false, 
                    false, 
                    false, 
                    false, 
                    false 
                ], 
                "mc_run" => false, 
                "mc_stop" => true, 
                "filling_speed" => 800, 
                "sv_speed_bpm" => 20, 
                "pv_speed_bpm" => 0, 
                "sv_bag" => 0, 
                "pv_bag" => 0, 
                "sv_filling_speed_rpm" => 800, 
                "pv_filling_speed_rpm" => 400, 
                "sv_gripper_width" => 155, 
                "sp_gripper_width" => 155, 
                "ts" => Carbon::now()->format('Y-m-d H:i:s')
            ]; 
            
            $mqtt->publish('data/gmk/k/leepack1', json_encode($jayParsedAry), 0);
            
            sleep(1);
        }
        $mqtt->disconnect();
    }
}
