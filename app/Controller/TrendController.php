<?php

declare(strict_types=1);

namespace App\Controller;

use Carbon\Carbon;
use App\Model\Device;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Contract\RequestInterface;

class TrendController
{
    public function data($deviceId, RequestInterface $request)
    {
        $device = Device::findOrFail($deviceId);

        $from = $request->input('from', Carbon::now()->format('Y-m-d H:i:s'));
        $to = $request->input('to', Carbon::now()->format('Y-m-d H:i:s'));
        $interval = $request->input('interval', 1800);

        // $from = Carbon::parse($from)->timezone('Asia/Jakarta');
        // $to = Carbon::parse($to)->timezone('Asia/Jakarta');

        $limit = $request->input('limit', 360);
        $select = $request->input('select', ['*']);

        $query = [];
        $fromClone = clone $from;
        $toClone = clone $to;

        $fromDiff = $from->format("Y-m-01 00:00:00");
        $toDiff = $to->format("Y-m-01 00:00:00");

        $count = Carbon::parse($fromDiff)->diffInMonths(Carbon::parse($toDiff));

        $formattingSelect = array_map(function($val){
            $eval = $val;
            $ex = explode('[', $val);
            if(count($ex) > 1) {
                $eval = $ex[0];
            }
            return "ct.{$eval}";
        }, $select);

        $select_column = implode(',', $formattingSelect);

        $classModel = $device->model;
        if(! class_exists($classModel)) {
            return response(['error' => 401, 'message' => 'model not found']);
        }

        $select_column = 'ct.*';
        for($i=0; $i <= $count; $i++) {
            $tableName = (new $classModel)->table($device, $from)->getTable();
            $query[] = "
            (select 
                (UNIX_TIMESTAMP(ct.terminal_time) * 1000) as unix_time, {$select_column}
                from {$tableName} as `ct` 
                    inner join 
                    (
                        SELECT MIN(terminal_time) as times, FLOOR(UNIX_TIMESTAMP(terminal_time)/{$interval}) AS timekey 
                        FROM {$tableName} 
                        WHERE terminal_time BETWEEN '{$fromClone->format('Y-m-d H:i:s')}' AND '{$toClone->format('Y-m-d H:i:s')}' 
                        GROUP BY timekey
                    ) ctx 
                    on `ct`.`terminal_time` = `ctx`.`times` order by unix_time)
            ";
            $from = $from->addMonth();
        }
        $query = implode(' UNION ', $query);
        $rows = Db::select($query);
        $rows = (new $classModel)->jsonResource($rows);
        
        return response($rows);
    }
}
