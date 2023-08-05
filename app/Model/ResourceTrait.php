<?php

namespace App\Model;


trait ResourceTrait
{
    public function jsonResource(array $data)
    {
        $data = collect($data)->map(function($val, $key){
            return collect($val)->map(function($val, $key){
                $castType = $this->hasCast($key) ? $this->getCastType($key) : false;
                if($castType) {
                    if($this->hasCast($key, ['array', 'json']) && in_array(trim(strtolower($this->getCasts()[$key])),['array', 'json'])) {
                        return $this->fromJson($val);
                    } else if($this->hasCast($key) && $castType == 'decimal'){
                        return (float) $this->asDecimal($val, explode(':', $this->getCasts()[$key], 2)[1]);
                    }else{
                        return $val;
                    }
                }else{
                    return $val; 
                }
            });
        });

        return $data;
    }
}