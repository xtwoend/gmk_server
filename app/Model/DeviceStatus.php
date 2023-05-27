<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 */
class DeviceStatus extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'device_status';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'device_id', 'shift_id', 'started_at', 'ended_at', 'type'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];
}
