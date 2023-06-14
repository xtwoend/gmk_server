<?php

declare(strict_types=1);

namespace App\Controller;

use Carbon\Carbon;
use App\Model\Score;
use App\Model\ScoreSetting;
use App\Resource\ScoreResource;
use App\Resource\ReportResource;
use Hyperf\HttpServer\Contract\RequestInterface;

class ScoreController
{
    public function index($deviceId, RequestInterface $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $date = Carbon::parse($date)->timezone('Asia/Jakarta');
        
        $score = Score::with('timesheets')
            ->where('device_id', $deviceId)
            ->where('production_date', $date->format('Y-m-d'));

        if($request->input('shift_id', null)) {
            $score = $score->where('shift_id', $request->input('shift_id'));
        }

        $score = $score->firstOrFail();
           

        return \response(new ScoreResource($score));
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
        return \response(new ScoreResource($score));
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

        return response(ScoreResource::collection($rows));
    }

    public function timesheetHistory($deviceId, RequestInterface $request) 
    {
        $from = $request->input('from', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to', Carbon::now()->format('Y-m-d'));

        $from = Carbon::parse($from)->timezone('Asia/Jakarta');
        $to = Carbon::parse($to)->timezone('Asia/Jakarta');

        $scores = Score::where('device_id', $deviceId)->whereBetween('production_date', [$from, $to])->get();
        $rows = [];
        
        foreach($scores as $score) {
            $rows[] = [
                'timestamps' => 0,
                'idle' => 0,
                'run' => 0,
                'breakdown' => 0,
            ];
        }

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
        $setting = ScoreSetting::where('device_id', $deviceId)->first();
        
        return response($setting);
    }

    public function score($id, RequestInterface $request)
    {
        $score = Score::with('timesheets')->findOrFail($id);
        
        return \response(new ScoreResource($score));
    }
    
}
