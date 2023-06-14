<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;

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
        $this->line('Hello Hyperf!', 'info');
        $scores = Score::where('production_date', '>', Carbon::now()->subDay()->format('Y-m-d'))->get();
        foreach($scores as $score) {
            $timesheet = $score->timesheets()->where('progress', 1)->get();
        }
    }
}
