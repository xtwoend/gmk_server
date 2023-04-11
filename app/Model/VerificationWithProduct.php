<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 */
class VerificationWithProduct extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'tbl_verification_with_product';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];


    public static function device()
    {
        # code...
    }
}
