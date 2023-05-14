<?php

declare(strict_types=1);

namespace App\Controller;

use Carbon\Carbon;
use App\Model\Score;
use Hyperf\HttpServer\Contract\RequestInterface;

class ScoreController
{
    public function index($deviceId, RequestInterface $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $date = Carbon::parse($date)->timezone('Asia/Jakarta');
        $score = Score::where('device_id', $deviceId)->where('date_score', $date->format('Y-m-d'))->first();

        return \response($score);
    }
    
    public function store($deviceId, RequestInterface $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $date = Carbon::parse($date)->timezone('Asia/Jakarta');
        
        $id = $request->input('id', null);
        if($id) {
            $score = Score::find($id);
            $score->update($request->all());
        }else{
            $score = Score::create(array_merge([
                'device_id' => $deviceId,
                'date_score' => $date
            ],$request->all()));
        }
        return \response($score);
    }
}
