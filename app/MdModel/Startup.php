<?php

declare(strict_types=1);

namespace App\MdModel;

/**
 */
class Startup extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'startups';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'device_id', 'started_at', 'finished_at', 'operator_id', 'status'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];

    /**
     * relation productions
     */
    public function productions()
    {
        return $this->hasMany(Production::class, 'startup_id');
    }

    /**
     * metdect mechine device
     */
    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    /**
     * relation to operator
     */
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
