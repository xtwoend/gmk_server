<?php

declare(strict_types=1);

namespace App\MdModel;

/**
 */
class ProductionVerification extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'production_verifications';

    /**
     * Types
     */
    protected array $types = [
        'Hourly', 'Batch', 'Change Over', 'Noise', 'Maintenance'
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'production_id', 
        'started_at', 
        'finished_at',
        'type',
        'order',
        'status', 
        'fe_front', 
        'non_fe_front', 
        'ss_front', 
        'fe_mid', 
        'non_fe_mid', 
        'ss_mid', 
        'fe_end', 
        'non_fe_end', 
        'ss_end', 
        'operator_id', 
        'foreman_id', 
        'remark'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime'
    ];


    public function production()
    {
        return $this->belongsTo(Production::class, 'production_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function foreman()
    {
        return $this->belongsTo(User::class, 'foreman_id');
    }
}
