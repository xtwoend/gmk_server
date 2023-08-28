<?php

declare(strict_types=1);

namespace App\Controller;

use Carbon\Carbon;
use App\Model\Score;
use App\Model\Timesheet;
use App\Model\ScoreSetting;
use Hyperf\DbConnection\Db;
use App\Resource\ScoreResource;
use App\Resource\ReportResource;
use Hyperf\HttpServer\Contract\RequestInterface;

class ScoreController
{
    public function index($deviceId, RequestInterface $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $date = Carbon::parse($date);
        
        $score = Score::with('timesheets')
            ->where('device_id', $deviceId)
            ->where('production_date', $date->format('Y-m-d'));

      
        if($request->input('shift_id', null)) {
            $score = $score->where('shift_id', $request->input('shift_id'));
        }

        $score = $score->first();
        if($score) {
            return \response(new ScoreResource($score), 0, ['current_shift' => shift()]);
        }

        return \response([], 0, ['current_shift' => shift()]);
    }
    
    public function store($deviceId, RequestInterface $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $date = Carbon::parse($date);
        
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

        $from = Carbon::parse($from);
        $to = Carbon::parse($to);

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

        $scores = Score::where('device_id', $deviceId)->whereBetween('production_date', [$from, $to])->get()->pluck('id');

        $rows = Timesheet::select(Db::raw("scores.production_date, scores.shift_id, SUM(IF(timesheets.status = 'run', TIMESTAMPDIFF(SECOND, timesheets.started_at, timesheets.ended_at), 0)) as run, SUM(IF(timesheets.status = 'idle', TIMESTAMPDIFF(SECOND, timesheets.started_at, timesheets.ended_at), 0)) as idle, SUM(IF(timesheets.status = 'breakdown', TIMESTAMPDIFF(SECOND, timesheets.started_at, timesheets.ended_at), 0)) as breakdown, (SUM(IF(timesheets.status = 'run', TIMESTAMPDIFF(SECOND, timesheets.started_at, timesheets.ended_at), 0))) + (SUM(IF(timesheets.status = 'idle', TIMESTAMPDIFF(SECOND, timesheets.started_at, timesheets.ended_at), 0)) ) + (SUM(IF(timesheets.status = 'breakdown', TIMESTAMPDIFF(SECOND, timesheets.started_at, timesheets.ended_at), 0))) as total"))
            ->join('scores', 'scores.id', '=', 'timesheets.score_id')
            ->whereIn('score_id', $scores)
            ->groupBy('score_id')
            ->get();

        return response($rows);
    }   

    public function setSetting($deviceId, RequestInterface $request)
    {
        $setting = ScoreSetting::updateOrCreate([
            'device_id' => $deviceId,
            'product_id' => $request->input('product_id', NULL)
        ], $request->all());

        return response($setting);
    }

    public function getSetting($deviceId, RequestInterface $request)
    {
        $setting = ScoreSetting::where('device_id', $deviceId)->where('product_id', $request->input('product_id', null))->first();
    
        return response($setting);
    }

    public function score($id, RequestInterface $request)
    {
        $score = Score::with('timesheets')->findOrFail($id);
        
        return \response(new ScoreResource($score));
    }
    
    public function getCurrentShift()
    {
        return response(shift());
    }
}
