<?php

use Carbon\Carbon;
use Hyperf\Utils\Arr;
use Hyperf\Utils\Codec\Json;
use Hyperf\Utils\ApplicationContext;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Contract\ResponseInterface;


if( ! function_exists('response')) {

    function response($data, int $code = 0, array $meta = [])
    {
        $response = ApplicationContext::getContainer()->get(ResponseInterface::class);
        $payload = [
            'error' => $code
        ];
        if(is_string($data)) {
            $payload['message'] = $data;
            $data = null;
        }

        if ($data || is_array($data)) {
            $payload['data'] = $data;
        }

        if ($meta) {
            $payload['meta'] = $meta;
        }
        
        if($data instanceof \Hyperf\Resource\Json\AnonymousResourceCollection || $data instanceof \Hyperf\Resource\Json\ResourceCollection){
            if($data->resource instanceof \Hyperf\Paginator\LengthAwarePaginator) {
                $resource = $data->resource->toArray();
                $payload['meta'] = Arr::except($resource, [
                    'data',
                    'first_page_url',
                    'last_page_url',
                    'prev_page_url',
                    'next_page_url',
                ]);
            }
        }

        $payload = Json::encode($payload);
        
        return $response
                ->withStatus(200)
                ->withHeader('content-type', 'application/json')
                ->withBody(new SwooleStream($payload));
    }
}

if( ! function_exists('export')) {
    function export(array $head, array $body, string $file_name) {
        
        $head_keys   = array_keys($head);
        $head_values = array_values($head);
        $fileData    = implode(',', $head_values) . "\n";
 
        if (strpos($file_name, '.') === false) {
            $file_name .= '.csv';
        }
 
        foreach ($body as $value) {
            $temp_arr = [];
            $value = json_decode(json_encode($value), true);
            foreach ($head_keys as $key) {
                if(strpos($key, ".") == true) {
                    $k = explode('.', $key);
                    $val = $value[$k[0]][$k[1]];
                }else{
                    $val = $value[$key] ?? '';
                }
                $temp_arr[] = $val;
            }
            $fileData .= implode(',', $temp_arr) . "\n";
        }
 
        $response = ApplicationContext::getContainer()->get(ResponseInterface::class);
        $content_type = 'text/csv';

        return $response->withHeader('content-description', 'File Transfer')
            ->withHeader('content-type', $content_type)
            ->withHeader('content-disposition', "attachment; filename={$file_name}")
            ->withHeader('content-transfer-encoding', 'binary')
            ->withHeader('pragma', 'public')
            ->withBody(new SwooleStream($fileData));
    }
}

if(! function_exists('shift')) {
    function shift($date = null) {
        $date = $date ?: Carbon::now()->format('Y-m-d H:i:s');
        /**
         * (Shift 1 = 06.00 sd 14.00)
         * (Shift 2 = 14.00 sd 21.00)
         * (Shift 3 = 21.00 sd 06.00)
         */
        $hour = Carbon::now()->format('G');
        $shift = 1;
        if($hour >= 14 && $hour <= 21) {
            $shift = 2;
        }elseif($hour >= 21 && $hour <= 23 || $hour >= 0 && $hour <= 6) {
            $shift = 3;
        }
        return $shift;
    }
}