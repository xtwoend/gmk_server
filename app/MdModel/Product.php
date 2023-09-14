<?php

declare(strict_types=1);

namespace App\MdModel;

/**
 */
class Product extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'products';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'code', 'product_name', 'unit', 'desc'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];

    protected array $appends = ["name"];

    public function getNameAttribute(){
        return $this->attributes['product_name'];
    }
}
