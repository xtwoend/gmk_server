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
class Mp1 extends Model
{
    use DeviceTrait, ResourceTrait, ScoreTrait;
    
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'mp_one';

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

                $table->float('sp_machine_weighing_minute_from_hmi', 10, 3)->default(0);
                $table->float('sp_right_dispenser_preaspiration_distance_from_hmi', 10, 3)->default(0);
                $table->float('sp_right_dosing_suction_distance_from_hmi', 10, 3)->default(0);
                $table->float('sp_right_dispenser_offset_distance', 10, 3)->default(0);
                $table->float('sp_left_dispenser_preaspiration_distance', 10, 3)->default(0);
                $table->float('sp_left_dispenser_dosing_distance', 10, 3)->default(0);
                $table->float('sp_left_dispenser_offset_distance', 10, 3)->default(0);
                $table->float('sp_no_dosages_followed', 10, 3)->default(0);
                $table->float('sp_left_dispenser_start_delay_time_from_hmi', 10, 3)->default(0);
                $table->float('sp_left_dispenser_preaspiration_distance_from_hmi', 10, 3)->default(0);
                $table->float('sp_left_dosing_suction_distance_from_hmi', 10, 3)->default(0);
                $table->float('pv_hopper_temperature', 10, 3)->default(0);
                $table->float('sp_hopper_temperature', 10, 3)->default(0);
                $table->float('pv_tunnel_temperature', 10, 3)->default(0);
                $table->float('weighing_machine_minute', 10, 3)->default(0);
                $table->float('partial_dosage_counter', 10, 3)->default(0);
                $table->float('total_dosage_counter', 10, 3)->default(0);
                $table->float('drag_belt_driver_alarm_code', 10, 3)->default(0);
                $table->float('right_dosing_driver_alarm_code', 10, 3)->default(0);
                $table->float('total_operating_hours_counter', 10, 3)->default(0);
                $table->float('total_operating_minute_counter', 10, 3)->default(0);
                $table->float('sp_lifting_table_height', 10, 3)->default(0);
                $table->float('sp_percentage_dosage_start_lowering_lifting_table', 10, 3)->default(0);
                $table->float('sp_percentage_dosage_start_conveyor_belt_run', 10, 3)->default(0);
                $table->float('sp_vibrator_speed', 10, 3)->default(0);
                $table->float('sp_work_temp_hopper', 10, 3)->default(0);
                $table->float('sp_maintain_temp_hopper', 10, 3)->default(0);
                $table->float('sp_tunnel_temperature', 10, 3)->default(0);

                $table->timestamps();
            });
        }

        return $model->setTable($tableName);
    }

    public function format(array $data)
    {
        return [
            'sp_machine_weighing_minute_from_hmi' => (float) $data['sp_machine_weighing_minute_from_hmi'] ?: 0,
            'sp_right_dispenser_preaspiration_distance_from_hmi' => (float) $data['sp_right_dispenser_preaspiration_distance_from_hmi'] ?: 0,
            'sp_right_dosing_suction_distance_from_hmi' => (float) $data['sp_right_dosing_suction_distance_from_hmi'] ?: 0,
            'sp_right_dispenser_offset_distance' => (float) $data['sp_right_dispenser_offset_distance'] ?: 0,
            'sp_left_dispenser_preaspiration_distance' => (float) $data['sp_left_dispenser_preaspiration_distance'] ?: 0,
            'sp_left_dispenser_dosing_distance' => (float) $data['sp_left_dispenser_dosing_distance'] ?: 0,
            'sp_left_dispenser_offset_distance' => (float) $data['sp_left_dispenser_offset_distance'] ?: 0,
            'sp_no_dosages_followed' => (float) $data['sp_no_dosages_followed'] ?: 0,
            'sp_left_dispenser_start_delay_time_from_hmi' => (float) $data['sp_left_dispenser_start_delay_time_from_hmi'] ?: 0,
            'sp_left_dispenser_preaspiration_distance_from_hmi' => (float) $data['sp_left_dispenser_preaspiration_distance_from_hmi'] ?: 0,
            'sp_left_dosing_suction_distance_from_hmi' => (float) $data['sp_left_dosing_suction_distance_from_hmi'] ?: 0,
            'pv_hopper_temperature' => (float) $data['pv_hopper_temperature'] ?: 0,
            'sp_hopper_temperature' => (float) $data['sp_hopper_temperature'] ?: 0,
            'pv_tunnel_temperature' => (float) $data['pv_tunnel_temperature'] ?: 0,
            'weighing_machine_minute' => (float) $data['weighing_machine_minute'] ?: 0,
            'partial_dosage_counter' => (float) $data['partial_dosage_counter'] ?: 0,
            'total_dosage_counter' => (float) $data['total_dosage_counter'] ?: 0,
            'drag_belt_driver_alarm_code' => (float) $data['drag_belt_driver_alarm_code'] ?: 0,
            'right_dosing_driver_alarm_code' => (float) $data['right_dosing_driver_alarm_code'] ?: 0,
            'total_operating_hours_counter' => (float) $data['total_operating_hours_counter'] ?: 0,
            'total_operating_minute_counter' => (float) $data['total_operating_minute_counter'] ?: 0,
            'sp_lifting_table_height' => (float) $data['sp_lifting_table_height'] ?: 0,
            'sp_percentage_dosage_start_lowering_lifting_table' => (float) $data['sp_percentage_dosage_start_lowering_lifting_table'] ?: 0,
            'sp_percentage_dosage_start_conveyor_belt_run' => (float) $data['sp_percentage_dosage_start_conveyor_belt_run'] ?: 0,
            'sp_vibrator_speed' => (float) $data['sp_vibrator_speed'] ?: 0,
            'sp_work_temp_hopper' => (float) $data['sp_work_temp_hopper'] ?: 0,
            'sp_maintain_temp_hopper' => (float) $data['sp_maintain_temp_hopper'] ?: 0,
            'sp_tunnel_temperature' => (float) $data['sp_tunnel_temperature'] ?: 0,
        ];
    }
}
