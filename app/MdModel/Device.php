<?php

declare(strict_types=1);

namespace App\MdModel;

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
     * note: untuk type sesuaikan dengan jenis mesin misal type 1: mesin pengecekan perjam, type 2: mesin pengecekan perbatch
     */
    protected array $fillable = [
        'name', 'ip', 'verification_type', 'fe', 'non_fe', 'ss'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];
}
