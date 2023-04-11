<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 */
class Metdec extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'metdec';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'name', 'connection', 'ip', 'status'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];
}
