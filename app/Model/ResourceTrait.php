<?php

namespace App\Model;


trait ResourceTrait
{
    public function jsonResource(array $data)
    {
        $data = collect($data)->map(function($val, $key){
            return collect($val)->map(function($val, $key){
                if($this->hasCast($key, ['array', 'json']) && in_array(trim(strtolower($this->getCasts()[$key])),['array', 'json'])) {
                    return $this->fromJson($val);
                }else{
                    return $val;
                }
            });
        });

        return $data;
    }
}