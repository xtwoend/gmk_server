<?php
namespace App\Event;

class MQTTReceived
{
    public array $data;
    public $message;
    public $topic;
    public $device;

    public function __construct($data, $message, $topic, $device) {
        $this->data = $data;
        $this->message = $message;
        $this->topic = $topic;
        $this->device = $device;
    }
}