<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use App\Model\ScoreTrait;
use App\Model\DeviceTrait;
use App\Model\ResourceTrait;
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Model\Events\Created;
use Hyperf\Database\Model\Events\Creating;

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

    // trigger run status
    public string $statusRun = 'is_run';
    public string $ppm_pv = 'pv_belt_speed';
    public string $ppm_sv = ''; // ambil dari setting
    public string $ppm2_pv = 'temperature_heating_house';
    public string $ppm2_sv = ''; // ambil dari setting
 
 

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

                $table->float('pv_belt_speed', 10, 3)->default(0);

                $table->boolean('is_run')->default(false);
                $table->float('performance_per_minutes', 3, 2)->nullable();
                $table->integer('sp_ppm_1')->nullable();
                $table->float('performance_per_minutes_2', 3, 2)->nullable();
                $table->integer('sp_ppm_2')->nullable();

                $table->timestamps();
            });
        }

        return $model->setTable($tableName);
    }

    public function format(array $data)
    {
        return [
            'sp_machine_weighing_minute_from_hmi' => $data['sp_machine_weighing_minute_from_hmi'] ?? 0,
            'sp_right_dispenser_preaspiration_distance_from_hmi' => $data['sp_right_dispenser_preaspiration_distance_from_hmi'] ?? 0,
            'sp_right_dosing_suction_distance_from_hmi' => $data['sp_right_dosing_suction_distance_from_hmi'] ?? 0,
            'sp_right_dispenser_offset_distance' => $data['sp_right_dispenser_offset_distance'] ?? 0,
            'sp_left_dispenser_preaspiration_distance' => $data['sp_left_dispenser_preaspiration_distance'] ?? 0,
            'sp_left_dispenser_dosing_distance' => $data['sp_left_dispenser_dosing_distance'] ?? 0,
            'sp_left_dispenser_offset_distance' => $data['sp_left_dispenser_offset_distance'] ?? 0,
            'sp_no_dosages_followed' => $data['sp_no_dosages_followed'] ?? 0,
            'sp_left_dispenser_start_delay_time_from_hmi' => $data['sp_left_dispenser_start_delay_time_from_hmi'] ?? 0,
            'sp_left_dispenser_preaspiration_distance_from_hmi' => $data['sp_left_dispenser_preaspiration_distance_from_hmi'] ?? 0,
            'sp_left_dosing_suction_distance_from_hmi' => $data['sp_left_dosing_suction_distance_from_hmi'] ?? 0,
            'pv_hopper_temperature' => $data['pv_hopper_temperature'] ?? 0,
            'sp_hopper_temperature' => $data['sp_hopper_temperature'] ?? 0,
            'pv_tunnel_temperature' => $data['pv_tunnel_temperature'] ?? 0,
            'weighing_machine_minute' => $data['weighing_machine_minute'] ?? 0,
            'partial_dosage_counter' => $data['partial_dosage_counter'] ?? 0,
            'total_dosage_counter' => $data['total_dosage_counter'] ?? 0,
            'drag_belt_driver_alarm_code' => $data['drag_belt_driver_alarm_code'] ?? 0,
            'right_dosing_driver_alarm_code' => $data['right_dosing_driver_alarm_code'] ?? 0,
            'total_operating_hours_counter' => $data['total_operating_hours_counter'] ?? 0,
            'total_operating_minute_counter' => $data['total_operating_minute_counter'] ?? 0,
            'sp_lifting_table_height' => $data['sp_lifting_table_height'] ?? 0,
            'sp_percentage_dosage_start_lowering_lifting_table' => $data['sp_percentage_dosage_start_lowering_lifting_table'] ?? 0,
            'sp_percentage_dosage_start_conveyor_belt_run' => $data['sp_percentage_dosage_start_conveyor_belt_run'] ?? 0,
            'sp_vibrator_speed' => $data['sp_vibrator_speed'] ?? 0,
            'sp_work_temp_hopper' => $data['sp_work_temp_hopper'] ?? 0,
            'sp_maintain_temp_hopper' => $data['sp_maintain_temp_hopper'] ?? 0,
            'sp_tunnel_temperature' => $data['sp_tunnel_temperature'] ?? 0,
            'pv_belt_speed' => $data['pv_belt_speed'] ?? 0
        ];
    }

    public function isAlarmOn(): bool
    {
        return false;
    }

    public function creating(Creating $event) {
        $this->id = Uuid::uuid4();
        $this->is_run = ($this->feedpump_speed > 0);
    }

    public function created(Created $event)
    {
        $model = $event->getModel();

        $setting = ScoreSetting::where('device_id', $model->device_id)->first();
        $sp_ppm_1 = $setting?->sp_ppm_1;
        $sp_ppm_2 = $setting?->sp_ppm_2;
        
        $perfoma = ($model->is_run > 0 && $sp_ppm_1 > 0) ? ($model->{$this->ppm_pv} / $sp_ppm_1) : 0;
        // $perfoma2 = ($model->is_run > 0 && $sp_ppm_2 > 0) ? ($model->{$this->ppm2_pv} / $sp_ppm_2) : 0;
        
        // update new data
        $model->fill([
            'sp_ppm_1' => $sp_ppm_1,
            // 'sp_ppm_2' => $sp_ppm_2,
            'performance_per_minutes' => $perfoma > 1 ? 1 : $perfoma,
            // 'performance_per_minutes_2' => $perfoma2 > 1 ? 1 : $perfoma2
        ])->save();

        $score = $this->createScoreDaily($model);

        if($score && $model->is_run) {
            $timesheet = $score->timesheets()
                ->where('score_id', $score->id)
                ->where('in_progress', 1)
                ->where('status', 'run')
                ->latest()
                ->first();
            
            if(is_null($timesheet)) {
                $time = Carbon::now();
                $score->timesheets()
                    ->where('in_progress', 1)
                    ->update([
                        'in_progress' => 0,
                        'ended_at' => Carbon::now()
                    ]);
                $timesheet = $score->timesheets()
                    ->create([
                        'started_at' => $time,
                        'in_progress' => 1,
                        'status' => 'run'
                    ]);
            }

            $timesheet->update([
                'ended_at' => Carbon::now()
            ]);
        }

        if($score && ! $model->is_run && $model->isAlarmOn()) {
            $timesheet = $score->timesheets()
                ->where('score_id', $score->id)
                ->where('in_progress', 1)
                ->where('status', 'breakdown')
                ->latest()
                ->first();
            
            if(is_null($timesheet)) {
                $time = Carbon::now();
                $score->timesheets()
                    ->where('in_progress', 1)
                    ->update([
                        'in_progress' => 0,
                        'ended_at' => Carbon::now()
                    ]);
                $timesheet = $score->timesheets()
                    ->create([
                        'started_at' => $time,
                        'in_progress' => 1,
                        'status' => 'breakdown'
                    ]);
            }

            $timesheet->update([
                'ended_at' => Carbon::now()
            ]);
        }

        if($score && $model->is_run && ! $model->isAlarmOn()) {
            $timesheet = $score->timesheets()
                ->where('score_id', $score->id)
                ->where('in_progress', 1)
                ->where('status', 'idle')
                ->latest()
                ->first();
            
            if(is_null($timesheet)) {
                $time = Carbon::now();
                $score->timesheets()
                    ->where('in_progress', 1)
                    ->update([
                        'in_progress' => 0,
                        'ended_at' => Carbon::now()
                    ]);
                $timesheet = $score->timesheets()
                    ->create([
                        'started_at' => $time,
                        'in_progress' => 1,
                        'status' => 'idle'
                    ]);
            }

            $timesheet->update([
                'ended_at' => Carbon::now()
            ]);
        }
    }
}
