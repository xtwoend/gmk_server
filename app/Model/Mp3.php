<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use App\Model\ScoreTrait;
use App\Model\DeviceTrait;
use App\Model\ResourceTrait;
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Database\Schema\Blueprint;

/**
 */
class Mp3 extends Model
{
    use DeviceTrait, ResourceTrait, ScoreTrait;
    
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'mp_three';

    protected string $primaryKey = 'id';
    protected string $keyType = 'string';
    public bool $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected array $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];

    /**
     * timestamp attribute from device iot gateway
     */
    public string $ts = 'ts';

    /**
     * create or choice table
     */
    public static function table($device, $date = null)
    {
        $date = is_null($date) ? date('Ym'): Carbon::parse($date)->format('Ym');
        $model = new self;
        $tableName = $model->getTable() . "_{$device->id}_{$date}";
       
        if(! Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->unsignedBigInteger('device_id')->index();
                $table->datetime('terminal_time')->unique()->index();

                $table->float('water_heater_pv', 10, 3)->default(0);
                $table->float('water_heater_sv', 10, 3)->default(0);
                $table->float('pump_speed', 10, 3)->default(0);
                $table->float('hose_heater_pv', 10, 3)->default(0);
                $table->float('hose_hwater_sv', 10, 3)->default(0);
                $table->float('encoder_cutoff_value', 10, 3)->default(0);
                $table->float('cutter_cutter_speed', 10, 3)->default(0);
                $table->float('cutter_belt_speed', 10, 3)->default(0);
                $table->float('cutter_cutting_lenght', 10, 3)->default(0);
                $table->float('servo_pulse_travel', 10, 3)->default(0);
                $table->float('water_heater_alarm_upper_limit', 10, 3)->default(0);
                $table->float('water_heater_alarm_lower_limit', 10, 3)->default(0);
                $table->float('hose_heater_alarm_upper_limit', 10, 3)->default(0);
                $table->float('hose_heater_alarm_lower_limit', 10, 3)->default(0);
                $table->float('recipe_par_conveyor_belt_sv', 10, 3)->default(0);
                $table->float('recipe_par_cutter_speed', 10, 3)->default(0);
                $table->float('depositor_pump_speed', 10, 3)->default(0);
                $table->float('stripe_lenght', 10, 3)->default(0);
                $table->float('cutting_offset_increament_value', 10, 3)->default(0);
                $table->float('cutting_offset_decreament_value', 10, 3)->default(0);

                $table->timestamps();
            });
        }

        return $model->setTable($tableName);
    }

    public function format(array $data)
    {
        return [
            'water_heater_pv' => (float) $data['water_heater_pv'],
            'water_heater_sv' => (float) $data['water_heater_sv'],
            'pump_speed' => (float) $data['pump_speed'],
            'hose_heater_pv' => (float) $data['hose_heater_pv'],
            'hose_hwater_sv' => (float) $data['hose_hwater_sv'],
            'encoder_cutoff_value' => (float) $data['encoder_cutoff_value'],
            'cutter_cutter_speed' => (float) $data['cutter_cutter_speed'],
            'cutter_belt_speed' => (float) $data['cutter_belt_speed'],
            'cutter_cutting_lenght' => (float) $data['cutter_cutting_lenght'],
            'servo_pulse_travel' => (float) $data['servo_pulse_travel'],
            'water_heater_alarm_upper_limit' => (float) $data['water_heater_alarm_upper_limit'],
            'water_heater_alarm_lower_limit' => (float) $data['water_heater_alarm_lower_limit'],
            'hose_heater_alarm_upper_limit' => (float) $data['hose_heater_alarm_upper_limit'],
            'hose_heater_alarm_lower_limit' => (float) $data['hose_heater_alarm_lower_limit'],
            'recipe_par_conveyor_belt_sv' => (float) $data['recipe_par_conveyor_belt_sv'],
            'recipe_par_cutter_speed' => (float) $data['recipe_par_cutter_speed'],
            'depositor_pump_speed' => (float) $data['depositor_pump_speed'],
            'stripe_lenght' => (float) $data['stripe_lenght'],
            'cutting_offset_increament_value' => (float) $data['cutting_offset_increament_value'],
            'cutting_offset_decreament_value' => (float) $data['cutting_offset_decreament_value'],
        ];
    }
}
