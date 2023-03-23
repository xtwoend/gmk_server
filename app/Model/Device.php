<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 */
class Device extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'devices';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'name', 'mqtt_server', 'topic', 'extractor', 'model', 'description', 'connected', 'last_connection', 'active'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'last_connection' => 'datetime',
        'active' => 'boolean',
        'connected' => 'boolean'
    ];

    /**
     * active
     */
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}
