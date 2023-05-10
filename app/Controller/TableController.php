<?php

declare(strict_types=1);

namespace App\Controller;

use Carbon\Carbon;
use App\Model\Device;
use Hyperf\Utils\Arr;
use Hyperf\DbConnection\Db;
use App\Resource\TableResource;
use Hyperf\HttpServer\Contract\RequestInterface;

class TableController
{
    public function data($deviceId, RequestInterface $request)
    {
        $device = Device::findOrFail($deviceId);
        $rpp = (int) $request->input('rowsPerPage', 25);

        $from = $request->input('from', Carbon::now()->format('Y-m-d H:i:s'));
        $to = $request->input('to', Carbon::now()->format('Y-m-d H:i:s'));
        $interval = $request->input('interval', 60);

        $from = Carbon::parse($from)->timezone('Asia/Jakarta');
        $to = Carbon::parse($to)->timezone('Asia/Jakarta');

        $classModel = $device->model;
        if(! class_exists($classModel)) {
            return response(['error' => 401, 'message' => 'model not found']);
        }

        $tableName = (new $classModel)->table($device, $from)->getTable();
        $subQuery = (new $classModel)->table($device, $from)
            ->select(Db::raw("MIN(terminal_time) as times, FLOOR(UNIX_TIMESTAMP(terminal_time)/{$interval}) AS timekey"))
            ->groupBy('timekey');

        $model = (new $classModel)
            ->table($device, $from)
            ->select(Db::raw('*'))
            ->whereBetween('terminal_time', [$from, $to])
            ->joinSub($subQuery, 'ctx', function($join) use ($tableName) {
                $join->on("{$tableName}.terminal_time", "=", "ctx.times");
            });

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

        $classModel = $device->model;
        if(! class_exists($classModel)) {
            return response(['error' => 401, 'message' => 'model not found']);
        }

        return (new $classModel)->export($device, $request);
    }

    public function dataUnion($deviceId, RequestInterface $request)
    {
        $device = Device::findOrFail($deviceId);
        $rpp = (int) $request->input('rowsPerPage', 25);

        $from = $request->input('from', Carbon::now()->format('Y-m-d H:i:s'));
        $to = $request->input('to', Carbon::now()->format('Y-m-d H:i:s'));
        $interval = $request->input('interval', 60);

        // $from = Carbon::parse($from)->timezone('Asia/Jakarta');
        // $to = Carbon::parse($to)->timezone('Asia/Jakarta');

        $classModel = $device->model;
        if(! class_exists($classModel)) {
            return response(['error' => 401, 'message' => 'model not found']);
        }

        $query = [];
        $fromClone = clone $from;
        $toClone = clone $to;

        $fromDiff = $from->format("Y-m-01 00:00:00");
        $toDiff = $to->format("Y-m-01 00:00:00");


        $count = Carbon::parse($fromDiff)->diffInMonths(Carbon::parse($toDiff));
        
        for($i=0; $i <= $count; $i++) {
            $tableName = (new $classModel)->table($device, $from)->getTable();
            $query[] = "
            (select 
                (UNIX_TIMESTAMP(ct.terminal_time) * 1000) as unix_time, ct.*
                from {$tableName} as `ct` 
                    inner join 
                    (
                        SELECT MIN(terminal_time) as times, FLOOR(UNIX_TIMESTAMP(terminal_time)/{$interval}) AS timekey 
                        FROM {$tableName} 
                        WHERE terminal_time BETWEEN '{$fromClone->format('Y-m-d H:i:s')}' AND '{$toClone->format('Y-m-d H:i:s')}' 
                        GROUP BY timekey
                    ) ctx 
                    on `ct`.`terminal_time` = `ctx`.`times`)
            ";
            $from = $from->addMonth();
        }
        $querySql = implode(' UNION ', $query);
        
        $rows = Db::table(Db::raw("($querySql order by terminal_time asc) as datalog"));

        if($request->has('sortBy')) {
            $column = $request->input('sortBy');
            $dir = $request->input('sortType');
            $rows = $rows->orderBy($column, $dir);
        }
        
        $rows = $rows->paginate($rpp);
        $data = (new $classModel)->jsonResource($rows->items());

        $payload['error'] = 0;
        $payload['data'] = $data;
        $payload['meta'] = Arr::except($rows->toArray(), [
            'data',
            'first_page_url',
            'last_page_url',
            'prev_page_url',
            'next_page_url',
        ]);
        return $payload;
    }
}
