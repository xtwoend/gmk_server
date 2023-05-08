<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Metdec;
use App\Model\VerificationWithProduct;
use App\Resource\MetdecReportResource;
use Hyperf\HttpServer\Contract\RequestInterface;

class MetdecReportController
{
    public function product($id, RequestInterface $request)
    {
        $rpp = (int) $request->input('rowsPerPage', 25);
        $production_id = $request->input('production_id', null);
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $date = Carbon::parse($date)->format('Y-m-d');

        $report = Metdec::findOrFail($id);
        $data   = VerificationWithProduct::on($report->connection)
                ->with('state', 'production', 'operator', 'foreman')
                ->withCount([
                    'products as product_good' => function($query) {
                        return $query->where('quality_id', 0);
                    },
                    'products as product_ng' => function($query) {
                        return $query->where('quality_id', 1);
                    }
                ]);

        if($production_id) {
            $data = $data->where('production_id', $production_id);
        }
        
        $data = $data->paginate($rpp);

        return response(MetdecReportResource::collection($data));
    }

    public function nonProduct($id, RequestInterface $request)
    {
        # code...
    }
}
