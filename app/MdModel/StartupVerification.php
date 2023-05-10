<?php

declare(strict_types=1);

namespace App\MdModel;

/**
 */
class StartupVerification extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'startup_verifications';

    /**
     * type verification
     */
    protected array $types = [
        'Start Up', 'After Mainetenace', 'Break', 'Noise', 'Change Over'
    ];

    /**
     * status verification
     */
    protected array $status = [
        'on progress', 'finish', 'failed'
    ];
     
    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'startup_id', 'started_at', 'finished_at', 'type', 'status', 'fe', 'non_fe', 'ss', 'operator_id', 'foreman_id',  'wor_number', 'remark'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];

    /**
     * date relation startup
     */
    public function startup()
    {
        return $this->belongsTo(Startup::class, 'startup_id');
    }

    /**
     * operator
     */
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    /**
     * foreman
     */
    public function foreman()
    {
        return $this->belongsTo(User::class, 'foreman_id');
    }
}
