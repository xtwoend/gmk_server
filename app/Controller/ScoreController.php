<?php

declare(strict_types=1);

namespace App\Controller;

use Carbon\Carbon;
use App\Model\Score;
use App\Resource\ReportResource;
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

    public function history($deviceId, RequestInterface $request)
    {
        $from = $request->input('from', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to', Carbon::now()->format('Y-m-d'));

        $from = Carbon::parse($from)->timezone('Asia/Jakarta');
        $to = Carbon::parse($to)->timezone('Asia/Jakarta');

        $rows = Score::where('device_id', $deviceId)->whereBetween('date_score', [$from, $to])->get();

        return response(ReportResource::collection($rows));
    }

    public function setting($deviceId, RequestInterface $request)
    {
        $setting = ScoreSetting::updateOrCreate([
            'device_id' => $deviceId,
        ], $request->all());

        return response($setting);
    }

    public function getSetting($deviceId)
    {
        $setting = ScoreSetting::findOrFail($deviceId);
        return response($setting);
    }

}
