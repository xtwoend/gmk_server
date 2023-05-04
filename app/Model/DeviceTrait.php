<?php

namespace App\Model;

use Carbon\Carbon;
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
     * alram
     */
    public function alarmDb($model, string $property)
    {
        foreach($model->{$property} as $key => $alarm) {
            $alarm = is_bool($alarm)? $alarm : (bool) $alarm;
            if($alarm) {
                $al = Alarm::table($model->device_id)
                    ->firstOrCreate([
                        'device_id' => $model->device_id,
                        'property' => $property,
                        'property_index' => $key,
                        'status' => 1
                    ]);
                if(is_null($al->started_at)) {
                    $al->started_at = Carbon::now()->format('Y-m-d H:i:s');
                }
                $alarmCode = "desc_{$property}";
                $al->message = $this->{$alarmCode}[$key];
                $al->finished_at = Carbon::now()->format('Y-m-d H:i:s');
                $al->save();
            }
        }
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

    public function export($device, $request)
    {
        $classModel = self::class;

        $from = $request->input('from', Carbon::now()->format('Y-m-d H:i:s'));
        $to = $request->input('to', Carbon::now()->format('Y-m-d H:i:s'));
        $interval = $request->input('interval', 60);

        $from = Carbon::parse($from)->timezone('Asia/Jakarta');
        $to = Carbon::parse($to)->timezone('Asia/Jakarta');

        $query = [];
        $fromClone = clone $from;
        $toClone = clone $to;

        $fromDiff = $from->format("Y-m-01 00:00:00");
        $toDiff = $to->format("Y-m-01 00:00:00");


        $count = Carbon::parse($fromDiff)->diffInMonths(Carbon::parse($toDiff));
        
        for($i=0; $i <= $count; $i++) {
            $tableName = (new $classModel)->table($device->id, $from)->getTable();
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

        $rows = $rows->get()->toArray();

        return export($this->headersExport, $rows, 'report-'.$from.'-'.$to);
    }
}