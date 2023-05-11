<?php

declare(strict_types=1);

namespace App\Controller;

use Carbon\Carbon;
use App\MdModel\Device;
use App\MdModel\Startup;
use App\Resource\ReportResource;
use Hyperf\Database\Model\Builder;
use App\MdModel\ProductionVerification;
use Hyperf\HttpServer\Contract\RequestInterface;

class ReportController
{
    public function data($id, RequestInterface $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $date = Carbon::parse($date)->timezone('Asia/Jakarta')->format('Y-m-d');

        $device = Device::findOrFail($id);
        $startup = Startup::with('device', 'verifications', 'verifications.operator', 'verifications.foreman')
                ->where('device_id', $device->id)
                ->whereDate('started_at', $date)
                ->latest()
                ->firstOrFail();

        $productions = ProductionVerification::with('operator', 'foreman', 'production', 'production.product')
            ->withCount(['good_records', 'ng_records'])
            ->whereIn('production_id', $startup->productions->pluck('id')->toArray())
            ->orderBy('type')
            ->orderBy('order')
            ->orderBy('started_at')
            ->get();
        
        return response([
            'startup' => $startup,
            'productions' => $productions
        ]);
    }
}
