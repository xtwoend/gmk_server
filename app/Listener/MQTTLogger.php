<?php

declare(strict_types=1);

namespace App\Listener;

use Carbon\Carbon;
use App\Model\Device;
use App\Model\Received;
use App\Event\MQTTReceived;
use Hyperf\Utils\Codec\Json;
use Hyperf\Event\Annotation\Listener;
use Psr\Container\ContainerInterface;
use Hyperf\Event\Contract\ListenerInterface;

#[Listener]
class MQTTLogger implements ListenerInterface
{
    public function __construct(protected ContainerInterface $container)
    {
    }

    public function listen(): array
    {
        return [
            MQTTReceived::class
        ];
    }

    public function process(object $event): void
    {
        if($event instanceof MQTTReceived) {
            $device = $event->device;
            $data = $event->data;
            $message = $event->message;
            $topic = $event->topic;

            
            Device::where(['id' => $device->id])->update([
                'connected' => true,
                'last_connection' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
                
            $modelClass = $device->model;
       
            if(! class_exists($modelClass)) {
                return;
            }

            $ts = (new $modelClass);
            $date = $data[$ts->ts];
            
            // logger mqtt reveived not for query
            Received::table($device->id)
                ->updateOrCreate([
                    'device_id' => $device->id,
                    'terminal_time' => $date,
                ], [
                    'topic' => $topic,
                    'data' => Json::encode($ts->format($data))
                ]);

    
            Received::table($device->id)->whereDate('terminal_time', '<', Carbon::now()->subHours(1)->format('Y-m-d'))->delete();
        }
    }
}
