<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Codefication;
use Hyperf\DbConnection\Model\Model;

/**
 */
class VerificationWithProduct extends Model
{
    use TraitConnection;

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

    /**
     * products
     */
    public function products()
    {
        return $this->hasMany(ProductRecord::class, 'production_id');
    }

    /**
     * state
     */
    public function state()
    {
        return $this->belongsTo(Codefication::class, 'verification_id', 'code');
    }

    /**
     * production
     */
    public function production()
    {
        return $this->belongsTo(Production::class, 'production_id', 'id'); 
    }

    /**
     * 
     */
    public function operator()
    {
        return $this->belongsTo(Operator::class, 'operator', 'nik'); 
    }

    /**
     * 
     */
    public function foreman()
    {
        return $this->belongsTo(Operator::class, 'foreman', 'nik'); 
    }
}
