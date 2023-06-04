<?php

declare(strict_types=1);

namespace App\Controller;

use Carbon\Carbon;
use App\Model\Score;
use App\Model\ScoreSetting;
use App\Resource\ReportResource;
use Hyperf\HttpServer\Contract\RequestInterface;

class ScoreController
{
    public function index($deviceId, RequestInterface $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $date = Carbon::parse($date)->timezone('Asia/Jakarta');
        $score = Score::where('device_id', $deviceId)->where('production_date', $date->format('Y-m-d'))->first();

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
                'production_date' => $date
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

        $rows = Score::where('device_id', $deviceId)->whereBetween('production_date', [$from, $to])->get();
        $rows = $rows->map(function($row){
            foreach(['availability', 'performance', 'quality', 'oee'] as $val){
                $row->$val = $row->$val * 100;   
            }
            return $row;
        });

        return response(ReportResource::collection($rows));
    }

    public function setSetting($deviceId, RequestInterface $request)
    {
        $setting = ScoreSetting::updateOrCreate([
            'device_id' => $deviceId,
        ], $request->all());

        return response($setting);
    }

    public function getSetting($deviceId)
    {
        $setting = ScoreSetting::find($deviceId);
        
        return response($setting);
    }

    public function score($id, RequestInterface $request)
    {
        $score = Score::with('timesheets')->findOrFail($id);
        
        return response($score);
    }
    
}
