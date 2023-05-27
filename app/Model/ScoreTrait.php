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

        $duration = Alarm::table($model->device_id)
            ->whereDate('started_at', $date)
            ->sum(Db::raw("TIMESTAMPDIFF(SECOND, started_at, finished_at)"));

        $score = Score::where('date_score', $date)
            ->where('shift', NULL)
            ->where('device_id', $model->device_id)
            ->first();

        if($score){
            
            $score->downtime_loss = $duration > 0 ? $duration / (60 * 60): 0;
            
            if($model->pv_bag > 0) {
                if($score->total_production < $model->pv_bag) {
                    $score->total_production = $model->pv_bag ?: 0;
                }

                if($score->good_production < $model->pv_bag) {
                    $score->good_production = $model->pv_bag ?: 0;
                }
            }

            $score->availability = $score->calcAvailability();
            $score->performance  = $score->calcPerformance();
            $score->quality = $score->calcQuality();
            $score->oee = $score->calcOee();
            $score->save();

        }else{
            $score = Score::create([
                'shift' => NULL,
                'number_of_shift' => 3,
                'hours_per_shift' => 8,
                'planned_shutdown_shift' => 1,
                'ideal_cycle_time_seconds' => 30,
                'total_production' => $model->pv_bag ?: 0,
                'good_production' => $model->pv_bag ?: 0,
                'downtime_loss' => $duration > 0 ? $duration / (60 * 60): 0,
            ]);
        }
    }


    public function createScoreShift($model)
    {
        $shift = shift();
        $date = Carbon::now()->format('Y-m-d');
        $hour = Carbon::now()->format('G');
        if($shift == 3 && $hour <= 6) {
            $date = Carbon::now()->subDay()->format('Y-m-d');
        }

        $duration = Alarm::table($model->device_id)
            ->whereDate('started_at', $date)
            ->sum(Db::raw("TIMESTAMPDIFF(SECOND, started_at, finished_at)"));

        $score = Score::where('date_score', $date)
            ->where('shift', $shift)
            ->where('device_id', $model->device_id)
            ->first();

        if($score){
            
            $score->downtime_loss = $duration > 0 ? $duration / (60 * 60): 0;
            
            if($model->pv_bag > 0) {
                if($score->total_production < $model->pv_bag) {
                    $score->total_production = $model->pv_bag ?: 0;
                }

                if($score->good_production < $model->pv_bag) {
                    $score->good_production = $model->pv_bag ?: 0;
                }
            }

            $score->availability = $score->calcAvailability();
            $score->performance  = $score->calcPerformance();
            $score->quality = $score->calcQuality();
            $score->oee = $score->calcOee();
            $score->save();

        }else{
            $score = Score::create([
                'shift' => $shift,
                'number_of_shift' => 3,
                'hours_per_shift' => 8,
                'planned_shutdown_shift' => 1,
                'ideal_cycle_time_seconds' => 30,
                'total_production' => $model->pv_bag ?: 0,
                'good_production' => $model->pv_bag ?: 0,
                'downtime_loss' => $duration > 0 ? $duration / (60 * 60): 0,
            ]);
        }
    }

    public function setLossesTime($model, $attribute, $shift = null)
    {
        $value = $model->$attribute ?: 0;

        $status = DeviceStatus::where([
            'device_id', $model->device_id,
            'shift_id' => $shift,
            'status' => 0,
        ])->first();

        if($status && ! $value) {
            $status->update([
                'ended_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }

        if(! $value && ! $status) {
            DeviceStatus::create([
                'device_id', $model->device_id,
                'shift_id' => $shift,
                'status' => 0,
                'started_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }

        if($value && $status)  {
            $status->update([
                'status' => 1
            ]);
        }
    }
}