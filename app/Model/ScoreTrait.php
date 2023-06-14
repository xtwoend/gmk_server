<?php

namespace App\Model;

use Carbon\Carbon;
use App\Model\Alarm;
use App\Model\Score;
use App\Model\Timesheet;
use App\Model\DeviceStatus;
use App\Model\ScoreSetting;
use Hyperf\DbConnection\Db;
use Hyperf\Database\Schema\Schema;

trait ScoreTrait
{
    public function createScoreDaily($model)
    {
        $date = Carbon::now()->format('Y-m-d');
        
        $score = Score::where([
            'device_id' => $model->device_id,
            'shift_id' => null,
            'production_date' => $date
        ])->first();

        if(is_null($score)) {
            $score = Score::create([
                'device_id' => $model->device_id,
                'shift_id' => null,
                'production_date' => $date,
                'started_at' => Carbon::parse($date . ' 00:00:00'),
                'ended_at' => Carbon::parse($date . ' 23:59:59')
            ]);
        }
        if($score->timesheets()->count() > 0) {
            
            $runTime = Timesheet::select(Db::raw("TIMESTAMPDIFF(SECOND, started_at, ended_at) as runTime"))->where('status', 'run')->where('score_id', $score->id)->get()->sum('runTime');
            $downTime = Timesheet::select(Db::raw("TIMESTAMPDIFF(SECOND, started_at, ended_at) as downTime"))->where('status', 'breakdown')->where('score_id', $score->id)->get()->sum('downTime');
            $stopTime = Timesheet::select(Db::raw("TIMESTAMPDIFF(SECOND, started_at, ended_at) as stopTime"))->where('status', 'idle')->where('score_id', $score->id)->get()->sum('stopTime');
            $availability = $this->getAvailability($model, $score);
            list($ppm1, $ppm2) = $this->avgPerformance($model, $score);
            $perfomance = $ppm1;
            if(!is_null($ppm2)) {
                $perfomance = ($ppm1 + $ppm2) / 2;
            }

            $s = Score::where('id', $score->id)->update([
                'ppm' => $ppm1,
                'ppm_pv' => $model->{$model->ppm_pv} ?: 0,
                'ppm_sv' => $model->{$model->ppm_sv} ?: 0,
                'ppm2' => $ppm2,
                'ppm2_pv' => $model->{$model->ppm2_pv} ?: 0,
                'ppm2_sv' => $model->{$model->ppm2_sv} ?: 0,
                'run_time' => $runTime,
                'down_time' => $downTime,
                'stop_time' => $stopTime,
                'performance' => $perfomance,
                'availability' => $availability,
                'quality' => 1,
                'oee' =>  $perfomance * $availability * 1,
            ]);
            $score = Score::find($score->id);
            
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
        if($shift->id == 3 && Carbon::now()->format('H:i:s') < $shift->ended_at) {
            $date = Carbon::now()->subDay()->format('Y-m-d');
        }
        
        $score = Score::where([
            'device_id' => $model->device_id,
            'shift_id' => $shift->id,
            'production_date' => $date
        ])->first();

        
        if(is_null($score)) {
            $started_at  = Carbon::parse($date.' '.$shift->started_at);
            $score = Score::create([
                'device_id' => $model->device_id,
                'shift_id' => $shift->id,
                'production_date' => $date,
                'started_at' => $started_at,
                'ended_at' => Carbon::parse($date.' '.$shift->started_at)->addHours(8)
            ]);
        }
        
        if($score->timesheets()->count() > 0) {
            
            list($perfomance, $output_qty, $ppm, $ppm2) = $this->leepackPerformance($model, $score);
            
            $runTime = Timesheet::select(Db::raw("TIMESTAMPDIFF(SECOND, started_at, ended_at) as runTime"))->where('status', 'run')->where('score_id', $score->id)->get()->sum('runTime');
            $downTime = Timesheet::select(Db::raw("TIMESTAMPDIFF(SECOND, started_at, ended_at) as downTime"))->where('status', 'breakdown')->where('score_id', $score->id)->get()->sum('downTime');
            $stopTime = Timesheet::select(Db::raw("TIMESTAMPDIFF(SECOND, started_at, ended_at) as stopTime"))->where('status', 'idle')->where('score_id', $score->id)->get()->sum('stopTime');
            
            $availability = $this->getAvailability($model, $score);
            $perfomance = ($perfomance < 1) ? $perfomance: 1;
            $s = Score::where('id', $score->id)
            ->update([
                'output_qty' => $output_qty,
                'reject_qty' => 0,
                'ppm' => $ppm,
                'ppm2' => $ppm2,
                'run_time' => $runTime,
                'down_time' => $downTime,
                'stop_time' => $stopTime,
                'performance' => $perfomance,
                'availability' => $availability,
                'quality' => 1,
                'oee' => $perfomance * $availability * 1,
            ]);
            $score = Score::find($score->id);
            
        }else{
            $score->timesheets()
                ->create([
                    'started_at' => $date . ' ' . $shift->started_at,
                    'status' => 'idle'
                ]);
        }
        
        return $score;
    }

    public function leepackPerformance($model, $score)
    {
        $runTime = $score->timesheets()->select(Db::raw("TIMESTAMPDIFF(SECOND, started_at, ended_at) as runTime"))->whereIn('status', ['run', 'idle'])->get()->sum('runTime');
        
        $ideal_cycle_time_seconds = 30;
        $setting = ScoreSetting::where('device_id', $model->device_id)->limit(1)->first();
        if($setting) {
            $ideal_cycle_time_seconds = $setting->ideal_cycle_time_seconds;
        }
        $output_qty = (int) $model->pv_bag;
        $ppm = $ideal_cycle_time_seconds;
        $ppm2 = (float) 0;
        $perfomance = (float) 0;
       
        if($output_qty > 0){
            $ppm2 = (float) ($runTime / $output_qty);
            
            if($ppm2 > 0){
                $perfomance = (float) ($ideal_cycle_time_seconds / $ppm2);
            }
        }
        
        return [$perfomance, $output_qty, $ppm, $ppm2];
    }

    public function avgPerformance($model, $score)
    {   
        $from = $score->started_at;
        $to = $score->ended_at;
        $nModel = get_class($model);
        $tableName = $nModel::table($model->device, $score->production_date->format('Y-m-d'))->getTable();
        
        $ppm1 = $nModel::table($model->device, $score->production_date->format('Y-m-d'))
            ->whereBetween('terminal_time', [$from, $to])
            ->where($model->statusRun, 1)
            ->avg('performance_per_minutes');
        
        $ppm2 = null;
        if (Schema::hasColumn($tableName, 'performance_per_minutes_2')){
            $ppm2 = $nModel::table($model->device, $score->production_date->format('Y-m-d'))
                ->whereBetween('terminal_time', [$from, $to])
                ->where($model->statusRun, 1)
                ->avg('performance_per_minutes_2');
        }
        
        return [$ppm1, $ppm2];
    }

    public function getAvailability($model, $score)
    {
        $from = Carbon::parse($score->started_at);
        $to = Carbon::now();
        $seconds = $to->diffInSeconds($from);
       
        $runTime = $score->timesheets()->select(Db::raw("TIMESTAMPDIFF(SECOND, started_at, ended_at) as runTime"))->whereIn('status', ['idle', 'run'])->get()->sum('runTime');
       
        return (float) ($runTime / $seconds);
    }
}