<?php

namespace App\Task;

use Carbon\Carbon;
use App\Model\Alarm;
use App\Model\Device;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Contract\StdoutLoggerInterface;

#[Crontab(name: "CloseAlarm", rule: "* * * * *", callback: "execute", memo: "Close alarm if opened until 1 minutes")]
class CloseAlarm
{
    #[Inject]
    private StdoutLoggerInterface $logger;

    public function execute()
    {
        $this->logger->info(date('Y-m-d H:i:s', time()));
        foreach(Device::active()->get() as $device) {
            Alarm::table($device->id)
            ->where('status', 1)
            ->where('finished_at', '<', Carbon::now()->subMinutes(2)->format('Y-m-d H:i:s'))
            ->update([
                'status' => 0
            ]);
        }
    }
}