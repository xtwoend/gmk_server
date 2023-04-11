<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 */
class VerificationWithoutProduct extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'tbl_verification_without_product';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];
}
