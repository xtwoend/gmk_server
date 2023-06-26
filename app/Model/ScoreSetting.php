<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 */
class ScoreSetting extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'score_settings';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'device_id', 'product_id', 'number_of_shift', 'hours_per_shift', 'planned_shutdown_shift', 'ideal_cycle_time_seconds', 'production_plan',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];
}
