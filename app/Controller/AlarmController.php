<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Alarm;
use App\Model\Device;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

class AlarmController
{
    public function data($deviceId, RequestInterface $request)
    {
        $device = Device::findOrFail($deviceId);
        $rpp = (int) $request->input('rowsPerPage', 25);

        $from = $request->input('from', Carbon::now()->format('Y-m-d H:i:s'));
        $to = $request->input('to', Carbon::now()->format('Y-m-d H:i:s'));
        
        $from = Carbon::parse($from)->timezone('Asia/Jakarta');
        $to = Carbon::parse($to)->timezone('Asia/Jakarta');

        $model = Alarm::table($device->id);

        if($request->has('sortBy')) {
            $column = $request->input('sortBy');
            $dir = $request->input('sortType');
            $model = $model->orderBy($column, $dir);
        }

        $model = $model->paginate($rpp);

        return response(TableResource::collection($model));
    }

    public function export($deviceId, RequestInterface $request)
    {
        $device = Device::findOrFail($deviceId);
        $model = Alarm::table($deviceId);

        return $model->export($device, $request);
    }
}
