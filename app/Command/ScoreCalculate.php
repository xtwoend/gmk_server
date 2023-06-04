<?php

declare(strict_types=1);

namespace App\Command;

use Carbon\Carbon;
use App\Model\Score;
use Hyperf\DbConnection\Db;
use Psr\Container\ContainerInterface;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;

#[Command]
class ScoreCalculate extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('score:calculate');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Calculate score run idle end breakdown time');
    }

    public function handle()
    {
        $this->line('calculate now', 'info');

        $scores = Score::where('production_date', '>=', Carbon::now()->subDays(7)->format('Y-m-d H:i:s'))->get();
        foreach($scores as $score) {
            $run_time = $score->timesheets()->select(Db::raw("TIMESTAMPDIFF(SECOND, started_at, ended_at) as runTime"))->where('status', 'run')->get()->sum('runTime');
            $down_time = $score->timesheets()->select(Db::raw("TIMESTAMPDIFF(SECOND, started_at, ended_at) as downTime"))->where('status', 'breakdown')->get()->sum('downTime');
            $stop_time = $score->timesheets()->select(Db::raw("TIMESTAMPDIFF(SECOND, started_at, ended_at) as stopTime"))->where('status', 'idle')->get()->sum('stopTime');

            $score->update([
                'run_time' => $run_time,
                'down_time' => $down_time,
                'stop_time' => $stop_time,
            ]);
        }
    }
}
