<?php

declare(strict_types=1);

namespace App\MdModel;

/**
 */
class ProductionRecord extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'production_records';

    
    /**
     * The attributes that are mass assignable.
     * status: 0 ok, 1 ng => bedarkan status dari alarm device
     */
    protected array $fillable = [
        'production_id', 'datetime', 'status', 'mark_verification_missed'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];
}
