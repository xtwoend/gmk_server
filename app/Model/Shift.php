<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 */
class Shift extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'shifts';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'name', 'started_at', 'ended_at'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];
}
