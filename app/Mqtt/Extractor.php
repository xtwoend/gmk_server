<?php

namespace App\Mqtt;

use Hyperf\Utils\Codec\Json;


class Extractor
{
    protected array $attributes = [];
 
    public function __construct(string $message) {
        $this->init($message);
    }

    public function __set(string $attribute, $val)
    {
        $this->attributes[$attribute] = $val;
        return $this;
    }

    public function __get(string $attribute)
    {
        return $this->attributes[$attribute] ?: '';
    }

    public function init(string $message)
    {
        try {
            $data = Json::decode($message);
            $this->attributes = $data;
            
        } catch (\Throwable $th) {
            
        }
        return $this->attributes;
    }

    public function toArray()
    {
        return (array) $this->attributes;
    }
}