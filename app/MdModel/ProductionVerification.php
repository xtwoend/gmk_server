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
        'Start Up', 'MD Noise','MD Mainetenace', 'Istirahat Shift 1', 'Istirahat Shift 2', 'Istirahat Shift 3', 'Change Over', 'Verifikasi Perjam', 'Verifikasi Perbatch'
    ];

    protected array $orders = [
        'Verifikasi perjam',
        'Verifikasi perbatch awal',
        'Verifikasi perbatch tengah',
        'Verifikasi perbatch akhir',
        'Verifikasi perjam terlewat',
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

    protected array $appends = ['order_text'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime'
    ];

    public function getOrderTextAttribute()
    {
        return $this->orders[$this->order] ?? '';
    }

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

    public function good_records()
    {
        return $this->hasMany(ProductionRecord::class, 'production_id', 'production_id')->where('status', 0);
    }

    public function ng_records()
    {
        return $this->hasMany(ProductionRecord::class, 'production_id', 'production_id')->where('status', 1);
    }
}
