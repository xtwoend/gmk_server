<?php

namespace App\Resource;

use Hyperf\Resource\Json\JsonResource;

class TableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        if(is_object($this->resource)) {
            $data = get_object_vars($this->resource);
        }else{
            $data = parent::toArray();
        }
        return $data;
    }
}
