<?php

namespace App\Model;


trait ResourceTrait
{
    public function jsonResource(array $data)
    {
        $data = collect($data)->map(function($val, $key){
            return collect($val)->map(function($val, $key){
                $castType = $this->getCastType($key);
                if($this->hasCast($key, ['array', 'json']) && in_array(trim(strtolower($this->getCasts()[$key])),['array', 'json'])) {
                    return $this->fromJson($val);
                } elseif($castType == 'decimal'){
                    return $this->asDecimal($value, explode(':', $this->getCasts()[$key], 2)[1]);
                }else{
                    return $val;
                }
            });
        });

        return $data;
    }
}