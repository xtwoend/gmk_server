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

        if($score->timesheets()->count() > 0) {
            $score->performance = $this->avgPerformance($model, $score);
            $score->availability = 0;
            $score->quality = 1;
            $score->oee = $score->performance * $score->availability * $score->quality;
            $score->save();
        }else{
            $score->timesheets()
                ->create([
                    'started_at' => $date . ' 00:00:00',
                    'status' => 'idle'
                ]);
        }
        
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

    public function setTimesheet($model, $score, array $params = [])
    {
        // 
    }


    public function avgPerformance($model, $score)
    {   
        $from = $score->production_date->format('Y-m-d') . ' ' . $score->started_at;
        $to = $score->production_date->format('Y-m-d') . ' ' . $score->ended_at;
        $nModel = get_class($model);
        $perfomance = $nModel::table($model->device, $score->production_date->format('Y-m-d'))->whereBetween('terminal_time', [$from, $to])->avg('performance_per_minutes');
        return $perfomance;
    }

    public function getAvailability($model, $score)
    {
        $from = Carbon::parse($score->production_date->format('Y-m-d') . ' ' .$score->started_at);
        $to = Carbon::now();

        $hour = $to->diffInHours($from);


        $score->timesheets()->where('status', 'run');
    }
}