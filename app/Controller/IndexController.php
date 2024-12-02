<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Controller;

use Hyperf\Codec\Json;

class IndexController extends AbstractController
{
    public function index()
    {
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();

        return [
            'method' => $method,
            'message' => "Hello {$user}.",
        ];
    }

    
    public function extract()
    {
        $json = file_get_contents(BASE_PATH . '/mqtt_data/bsa.json');

        $data = [];
        foreach(Json::decode($json) as $key => $json) {
            $data[] = [
                'value' => $key,
                'text' => $this->keyToTitle($key)
            ];
        } 

        return $data;
    }

    protected function keyToTitle($val)
    {
        return implode(' ', array_map('ucfirst', explode('_', $val)));
    }
}
