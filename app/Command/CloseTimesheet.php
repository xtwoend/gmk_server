<?php

declare(strict_types=1);

namespace App\Command;

use Carbon\Carbon;
use App\Model\Score;
use Psr\Container\ContainerInterface;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;

#[Command]
class CloseTimesheet extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('score:timesheet');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Close Timesheet on latest');
    }

    public function handle()
    {
        $this->line('Closess score', 'info');
        $scores = Score::where('production_date', '>', Carbon::now()->subDay()->format('Y-m-d'))->get();
        foreach($scores as $score) {
            $timesheet = $score->timesheets()->where('progress', 1)->get();
        }
    }
}
