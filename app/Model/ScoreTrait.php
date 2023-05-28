<?php

namespace App\Model;

use Carbon\Carbon;
use App\Model\Alarm;
use App\Model\Score;
use App\Model\DeviceStatus;
use App\Model\ScoreSetting;
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
            $score->availability = $this->getAvailability($model, $score);
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

        if($score->timesheets()->count() > 0) {
            $score->performance = $this->leepackPerformance($model, $score);
            $score->availability = $this->getAvailability($model, $score);
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

    public function leepackPerformance($model, $score)
    {
        $runTime = $score->timesheets()->select(Db::raw("TIMESTAMPDIFF(SECOND, started_at, ended_at) as runTime"))->where('status', 'run')->get()->sum('runTime');
        $setting = ScoreSetting::where('device_id', $model->device_id)->first();

        $output = $model->pv_bag;
        
        $score->output = $output;
        $score->ppm = ($runTime / $output);
        $score->save();

        return (float) ($setting->ideal_cycle_time_seconds / $score->ppm);
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
        $seconds = $to->diffInSeconds($from);
       
        $runTime = $score->timesheets()->select(Db::raw("TIMESTAMPDIFF(SECOND, started_at, ended_at) as runTime"))->where('status', 'run')->get()->sum('runTime');
       
        return (float) ($runTime / $seconds);
    }
}