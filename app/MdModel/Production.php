<?php

declare(strict_types=1);

namespace App\MdModel;

/**
 */
class Production extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'productions';
    
    /**
     * The attributes that are mass assignable.
     * type: 1 => normal, 2 => recheck, 
     */
    protected array $fillable = [
        'startup_id', 'type', 'por_number', 'product_id', 'batch_no', 'status'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];

    /**
     * startup 
     */
    public function startup()
    {
        return $this->belongsTo(Startup::class, 'startup_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
