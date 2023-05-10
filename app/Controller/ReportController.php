<?php

declare(strict_types=1);

namespace App\Controller;

use Carbon\Carbon;
use App\MdModel\Device;
use App\MdModel\Startup;
use App\Resource\ReportResource;
use Hyperf\HttpServer\Contract\RequestInterface;

class ReportController
{
    public function data($id, RequestInterface $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $date = Carbon::parse($date)->format('Y-m-d');

        $device = Device::findOrFail($id);
        $startup = Startup::with('device', 'verifications', 'productions', 'productions.product')->where('device_id', $device->id)->whereDate('started_at', $date)->first();
        
        return response(new ReportResource($startup));
    }
}