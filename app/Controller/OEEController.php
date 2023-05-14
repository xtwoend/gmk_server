<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Alarm;
use App\Model\Device;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Contract\RequestInterface;

class OEEController
{
    public function index($deviceId, RequestInterface $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $date = Carbon::parse($date)->timezone('Asia/Jakarta');
        
        return [];
    }
    
    public function store($deviceId, RequestInterface $request)
    {
        return [];
    }
}
