<?php

namespace App\Task;

use Carbon\Carbon;
use App\Model\Alarm;
use App\Model\Score;
use App\Model\Device;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Contract\StdoutLoggerInterface;

#[Crontab(name: "CalculateScore", rule: "*\/5 * * * * *", callback: "execute", memo: "calculate score")]
class CalculateScore
{
    #[Inject]
    private StdoutLoggerInterface $logger;

    public function execute()
    {
        $this->logger->info('Crontab time runing '. date('Y-m-d H:i:s', time()));

        $scores = Score::where('production_date', '>=', Carbon::now()->subDays(7)->format('Y-m-d H:i:s'))->get();
        foreach($scores as $score) {
            $run_time = $score->timesheets()->select(Db::raw("TIMESTAMPDIFF(SECOND, started_at, ended_at) as runTime"))->where('status', 'run')->get()->sum('runTime');
            $down_time = $score->timesheets()->select(Db::raw("TIMESTAMPDIFF(SECOND, started_at, ended_at) as downTime"))->where('status', 'breakdown')->get()->sum('downTime');
            $stop_time = $score->timesheets()->select(Db::raw("TIMESTAMPDIFF(SECOND, started_at, ended_at) as stopTime"))->where('status', 'idle')->get()->sum('stopTime');

            $score->update([
                'run_time' => $run_time,
                'down_time' => $down_time,
                'stop_time' => $stop_time,
                'oee' => ($score->availability * $score->performance * $score->quality)
            ]);
        }
    }
}