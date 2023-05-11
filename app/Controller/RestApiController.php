<?php

declare(strict_types=1);

namespace App\Controller;

use App\MdModel\Startup;
use App\MdModel\Production;
use App\MdModel\ProductionRecord;
use App\MdModel\ProductionProblem;
use App\MdModel\StartupVerification;
use App\MdModel\ProductionVerification;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

class RestApiController
{
    public function sync($table, RequestInterface $request)
    {
        if($table == 'startup') {
            $model = new Startup;
        }elseif($table == 'startup_verifications') {
            $model = new StartupVerification;
        }elseif($table == 'productions') {
            $model = new Production;
        }elseif($table == 'production_verifications') {
            $model = new ProductionVerification;
        }elseif($table == 'production_records') {
            $model = new ProductionRecord;
        }elseif($table == 'production_problems') {
            $model = new ProductionProblem;
        }

        $data = $request->all();
            
        foreach($data as $row) {
            $model->updateOrCreate([
                'id' => $row['id']
            ], $row);
        }
    }
}
