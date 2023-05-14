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
        
        $score = Score::updateOrCreate([
            'date_score' => $date,
            'device_id' => $model->device_id,
        ], [
            // 'number_of_shift' => 3,
            // 'hours_per_shift' => 8,
            // 'planned_shutdown_shift' => 1,
            // 'ideal_cycle_time_seconds' => 30,
            // 'total_production' => $model->pv_bag ?: 0,
            // 'good_production' => $model->pv_bag ?: 0,
            'downtime_loss' => $duration > 0 ? $duration / (60 * 60): 0,
        ]);

        $score->update([
            'availability' => $score->calcAvailability(),
            'performance' => $score->calcPerformance(),
            'quality' => $score->calcQuality(),
            'oee' => $score->calcOee()
        ]);
    }
}