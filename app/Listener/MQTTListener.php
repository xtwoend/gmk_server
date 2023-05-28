<?php

declare(strict_types=1);

namespace App\Listener;

use Carbon\Carbon;
use App\Event\MQTTReceived;
use Hyperf\Event\Annotation\Listener;
use Psr\Container\ContainerInterface;
use Hyperf\Event\Contract\ListenerInterface;

#[Listener]
class MQTTListener implements ListenerInterface
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
            $this->save($event);
        }
    }

    public function save($event)
    {
        $data = $event->data;
        $message = $event->message;
        $topic = $event->topic;
        $device = $event->device;

        $modelClass = $device->model;
       
        if(! class_exists($modelClass)) {
            return;
        }
        
        $ts = (new $modelClass);
        $date = $data[$ts->ts];
        $data = $ts->format($data);

        $model = $modelClass::table($device, $date);
       
        $last = $model->orderBy('terminal_time', 'desc')->first();
        $now = Carbon::parse($date);

        // save interval 60 detik
        if($last && $now->diffInSeconds($last->terminal_time) < config('mqtt.interval_save', 60) ) {   
            return;
        }
        
        return $model->updateOrCreate([
            'device_id' => $device->id,
            'terminal_time' => $now->format('Y-m-d H:i:s'),
        ], $data);
    }
}
