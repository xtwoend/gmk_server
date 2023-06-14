<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Timesheet;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Database\Model\Events\Updating;

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
        'shift_id',
        'user_id',
        'production_date',
        'started_at',
        'ended_at',
        'ppm',
        'ppm_sv',
        'ppm_pv',
        'ppm2',
        'ppm2_sv',
        'ppm2_pv',
        'run_time',
        'stop_time',
        'down_time',
        'output_qty',
        'reject_qty',
        'availability',
        'performance',
        'quality',
        'oee'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'production_date' => 'date:Y-m-d',
        'started_at' => 'datetime:Y-m-d H:i:s',
        'end_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected array $appends = [
        // 'total_available_time', 
        // 'planned_shutdown', 
        // 'scheduled_operating_time', 
        // 'ideal_cycle_time', 
        // 'operating_time',
        // 'effective_operating_time',
        // 'speed_loss',
        // 'production_reject',
    ];

    public function timesheets()
    {
        return $this->hasMany(Timesheet::class, 'score_id');
    }

    // public function getTotalAvailableTimeAttribute()
    // {
    //     return ($this->number_of_shift * $this->hours_per_shift);
    // }

    // public function getPlannedShutdownAttribute()
    // {
    //     return ($this->number_of_shift * $this->planned_shutdown_shift);
    // }

    // public function getScheduledOperatingTimeAttribute()
    // {
    //     return $this->getTotalAvailableTimeAttribute() - $this->getPlannedShutdownAttribute();
    // }

    // public function getIdealCycleTimeAttribute()
    // {
    //     return ($this->ideal_cycle_time_seconds / (60 * 60));
    // }

    // public function getOperatingTimeAttribute()
    // {
    //     return $this->getScheduledOperatingTimeAttribute() - $this->downtime_loss;
    // }
    
    // public function getEffectiveOperatingTimeAttribute()
    // {
    //     return $this->getIdealCycleTimeAttribute() * $this->total_production;
    // }

    // public function getSpeedLossAttribute()
    // {
    //     return $this->getOperatingTimeAttribute() - $this->getEffectiveOperatingTimeAttribute();
    // }

    // public function getProductionRejectAttribute()
    // {
    //     return ($this->total_production - $this->good_production);
    // }

    // public function calcAvailability()
    // {
    //     return $this->getOperatingTimeAttribute() / $this->getTotalAvailableTimeAttribute();
    // }

    // public function calcPerformance()
    // {
    //     return ($this->getIdealCycleTimeAttribute() * $this->total_production) / $this->getOperatingTimeAttribute();
    // }

    // public function calcQuality()
    // {
    //     return $this->total_production > 0 ? ($this->good_production / $this->total_production): 0;
    // }

    // public function calcOee()
    // {
    //     $a = $this->calcAvailability();
    //     $p = $this->calcPerformance();
    //     $q = $this->calcQuality();
    //     return $a * $p * $q;
    // }
}
