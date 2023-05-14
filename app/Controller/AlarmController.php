<?php

declare(strict_types=1);

namespace App\Controller;

use Carbon\Carbon;
use App\Model\Alarm;
use App\Model\Device;
use Hyperf\DbConnection\Db;
use App\Resource\AlarmResource;
use Hyperf\HttpServer\Contract\RequestInterface;

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

        $model = Alarm::table($device->id)->whereBetween('started_at', [$from, $to]);

        if($request->has('sortBy')) {
            $column = $request->input('sortBy');
            $dir = $request->input('sortType');
            $model = $model->orderBy($column, $dir);
        }

        $model = $model->paginate($rpp);
    
        return response(AlarmResource::collection($model));
    }

    public function export($deviceId, RequestInterface $request)
    {
        $device = Device::findOrFail($deviceId);
        $model = Alarm::table($deviceId);

        return $model->export($device, $request);
    }

    public function summary($deviceId, RequestInterface $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d H:i:s'));
        $date = Carbon::parse($date)->timezone('Asia/Jakarta');

        $rows = Alarm::table($deviceId)
            ->select(Db::raw("message, SUM(TIMESTAMPDIFF(SECOND, started_at, finished_at)) as seconds, count(message) as message_count"))
            ->where(Db::raw("YEAR(started_at)"), $date->format('Y'))
            ->where(Db::raw("MONTH(started_at)"), $date->format('m'))
            ->groupBy('message')
            ->orderBy('seconds', 'desc')
            ->limit(10)
            ->get();
       
        return response($rows);
    }
}
