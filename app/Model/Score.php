<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 */
class Score extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'scores';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'device_id',
        'date_score',
        'number_of_shift',
        'hours_per_shift',
        'planned_shutdown_shift',
        'downtime_loss', // from summary duration alarm
        'ideal_cycle_time_seconds',
        'total_production',
        'good_production',
        'availability',
        'performance',
        'quality',
        'oee',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'date_score' => 'date'
    ];

    public function getScheduledOperatingTimeAttribute()
    {
        return ($this->number_of_shift * $this->hours_per_shift) - ($this->number_of_shift * $this->planned_shutdown_shift);
    }

    public function getTotalAvailableTimeAttribute()
    {
        return ($this->number_of_shift * $this->hours_per_shift);
    }

    public function getIdealCycleTimeAttribute()
    {
        return ($this->ideal_cycle_time_seconds / (60 * 60));
    }

    public function getOperatingTimeAttribute()
    {
        return (($this->number_of_shift * $this->hours_per_shift) - ($this->number_of_shift * $this->planned_shutdown_shift)) - $this->downtime_loss;
    }
    
    public function getEffectiveOperatingTimeAttribute()
    {
        return ($this->ideal_cycle_time_seconds / (60 * 60)) * $this->total_production;
    }

    public function getProductionRejectAttribute()
    {
        return ($this->total_production - $this->good_production);
    }

    public function getAvailabilityAttribute()
    {
        return ($this->number_of_shift * $this->hours_per_shift) / ((($this->number_of_shift * $this->hours_per_shift) - ($this->number_of_shift * $this->planned_shutdown_shift)) - $this->downtime_loss);
    }

    public function getPerformanceAttribute()
    {
        return (($this->ideal_cycle_time_seconds / (60 * 60)) * $this->total_production) / ((($this->number_of_shift * $this->hours_per_shift) - ($this->number_of_shift * $this->planned_shutdown_shift)) - $this->downtime_loss);
    }

    public function getQualityAttribute()
    {
        return ($this->good_production / $this->total_total_production);
    }

    public function getOeeAttribute()
    {
        $a = ($this->number_of_shift * $this->hours_per_shift) / ((($this->number_of_shift * $this->hours_per_shift) - ($this->number_of_shift * $this->planned_shutdown_shift)) - $this->downtime_loss);
        $p = (($this->ideal_cycle_time_seconds / (60 * 60)) * $this->total_production) / ((($this->number_of_shift * $this->hours_per_shift) - ($this->number_of_shift * $this->planned_shutdown_shift)) - $this->downtime_loss);
        $q = ($this->total_total_production / $this->good_production);
        return $a * $p * $q;
    }
    
}
