<?php

namespace App\Model;

use Carbon\Carbon;
use App\Model\Alarm;
use App\Model\Score;
use App\Model\DeviceStatus;
use Hyperf\DbConnection\Db;

trait ScoreTrait
{
    public function createScoreDaily($model)
    {
        $date = Carbon::now()->format('Y-m-d');

        $score = Score::firstOrCreate([
            'device_id' => $model->device_id,
            'shift_id' => null,
            'production_date' => $date,
            'started_at' => '00:00:00',
            'ended_at' => '23:59:59'
        ]);

        return $score;
    }


    public function createScoreShift($model)
    {
        $shift = shift();
        $date = Carbon::now()->format('Y-m-d');
        if($shift->id == 3 && Carbon::now()->format('G') < $shift->ended_at) {
            $date = Carbon::now()->subDay()->format('Y-m-d');
        }
        $score = Score::firstOrCreate([
            'device_id' => $model->device_id,
            'shift_id' => $shift->id,
            'production_date' => $date,
            'started_at' => $shift->started_at,
            'ended_at' => $shift->ended_at
        ]);

        return $score;
    }

    public function setTimesheet($model, $score)
    {
        // 
    }
}