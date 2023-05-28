<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 */
class Timesheet extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'timesheets';

    protected array $status = ['run', 'idle', 'breakdown'];
    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'score_id', 'started_at', 'ended_at', 'output', 'reject', 'ppm', 'status'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];
}
