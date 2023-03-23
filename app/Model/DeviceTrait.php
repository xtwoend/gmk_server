<?php

namespace App\Model;

use App\Model\Device;
use Hyperf\Database\Model\Events\Creating;

trait DeviceTrait
{
    /**
     * creating
     */
    public function creating(Creating $event)
    {
        $this->id = \Ramsey\Uuid\Uuid::uuid4()->toString();
    }

    /**
     * device
     */
    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    protected function map(array $data){
        return array_map(function($value){
            return ($value)? 1: 0;
        }, $data); 
    }
}