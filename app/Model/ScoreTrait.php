<?php

namespace App\Model;

use Carbon\Carbon;
use App\Model\Alarm;
use App\Model\Score;
use Hyperf\DbConnection\Db;

trait ScoreTrait
{
    public function createScoreDaily($model)
    {
        $date = Carbon::now()->format('Y-m-d');
        $duration = Alarm::table($model->device_id)
            ->whereDate('started_at', $date)
            ->sum(Db::raw("TIMESTAMPDIFF(SECOND, started_at, finished_at)"));
        $score = Score::where('date_score', $date)->where('device_id', $model->device_id)->first();
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
            /**
             * (Shift 1 = 06.00 sd 14.00)
             * (Shift 2 = 14.00 sd 21.00)
             * (Shift 3 = 21.00 sd 06.00)
             */
            $hour = Carbon::now()->format('G');
            $shift = 1;
            if($hour >= 14 && $hour <= 21) {
                $shift = 2;
            }elseif($hour >= 21 && $hour <= 23 || $hour >= 0 && $hour <= 6) {
                $shift = 3;
            }

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
}