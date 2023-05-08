<?php

namespace App\Controller;

use Carbon\Carbon;
use App\Model\Device;
use App\Model\Received;
use Hyperf\HttpServer\Contract\RequestInterface;


class MqttLogController
{
    public function data($deviceId, RequestInterface $request)
    {
        $device = Device::findOrFail($deviceId);

        $last = $request->input('last', 30);
        // $date = Carbon::now()->subMinutes($last)->format('Y-m-d H:i:s');
        $data = Received::table($device->id)
            ->where('device_id', $device->id)
            // ->where('terminal_time', '>=', $date)
            ->limit(200)
            ->orderBy('terminal_time', 'asc')
            ->get();

        return response($data);
    }
}