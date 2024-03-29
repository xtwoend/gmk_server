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
        'Perjam',
        'Batch awal',
        'Batch tengah',
        'Batch akhir',
        'Perjam terlewat',
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

    protected array $appends = ['order_text', 'good_records_count', 'ng_records_count'];

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

    // public function good_records()
    // {
    //     return $this->hasMany(ProductionRecord::class, 'production_id', 'production_id')->where('status', 0);
    // }

    // public function ng_records()
    // {
    //     return $this->hasMany(ProductionRecord::class, 'production_id', 'production_id')->where('status', 1);
    // }

    public function records()
    {
        return $this->hasMany(ProductionRecord::class,  'production_id', 'production_id');
    }

    public function getNgRecordsCountAttribute()
    { 
        $startup = $this->production->startup;
        
        $productionIds = $startup->productions->pluck('id')->toArray();

        $lastCheck = self::where('finished_at', '<', $this->attributes['started_at'])->whereIn('production_id', $productionIds)->latest()->first();
        $started =  $lastCheck ? $lastCheck->finished_at : $startup->started_at;
        $finished = $this->attributes['finished_at'];

        $count = ProductionRecord::whereIn('production_id', $productionIds)->where('status', 1)->whereBetween('datetime', [$started, $finished])->count();

        return $count;
    }

    public function getGoodRecordsCountAttribute()
    { 
        $startup = $this->production->startup;

        $productionIds = $startup->productions->pluck('id')->toArray();
        $lastCheck = self::where('finished_at', '<', $this->attributes['started_at'])->whereIn('production_id', $productionIds)->latest()->first();
        
        $started =  $lastCheck ? $lastCheck->finished_at : $startup->started_at;
        $finished = $this->attributes['finished_at'];

        $count = ProductionRecord::whereIn('production_id', $productionIds)->where('status', 0)->whereBetween('datetime', [$started, $finished])->count();

        return $count;
    }
}
