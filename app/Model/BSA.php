<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use App\Model\ScoreTrait;
use App\Model\DeviceTrait;
use App\Model\ScoreSetting;
use App\Model\ResourceTrait;
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Model\Events\Created;
use Hyperf\Database\Model\Events\Creating;

/**
 */
class BSA extends Model
{
    use DeviceTrait, ResourceTrait, ScoreTrait;
    
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'bsa';

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
    public string $ppm_pv = 'hopper_actual_load';
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

                $table->float('hopper_actual_load', 10, 3)->default(0);
                $table->float('hopper_total_volume', 10, 3)->default(0);
                $table->float('hopper_tank', 10, 3)->default(0);
                $table->float('hopper_dead_load', 10, 3)->default(0);
                $table->float('parameter_pressure_roll_bellow_left_press_set_act', 10, 3)->default(0);
                $table->float('parameter_pressure_roll_bellow_left_press_pv_act', 10, 3)->default(0);
                $table->float('parameter_pressure_roll_bellow_right_press_set_act', 10, 3)->default(0);
                $table->float('parameter_pressure_roll_bellow_right_press_pv_act', 10, 3)->default(0);
                $table->float('mixer1_rec_start_dos_kg', 10, 3)->default(0);
                $table->float('mixer1_mass_act_temp', 10, 3)->default(0);
                $table->float('mixer1_waterfw_act_temp', 10, 3)->default(0);
                $table->float('mixer1_waterrw_act_temp', 10, 3)->default(0);
                $table->float('mixer1_sp_temp_water_c', 10, 3)->default(0);
                $table->float('mixer2_rec_start_dos_kg', 10, 3)->default(0);
                $table->float('mixer2_mass_act_temp', 10, 3)->default(0);
                $table->float('mixer2_waterfw_act_temp', 10, 3)->default(0);
                $table->float('mixer2_waterrw_act_temp', 10, 3)->default(0);
                $table->float('mixer2_sp_temp_water_c', 10, 3)->default(0);
                $table->float('hrk1_actual_weight_kg', 10, 3)->default(0);
                $table->float('hrk1_mass_act_temp', 10, 3)->default(0);
                $table->float('hrk1_waterfw_act_temp', 10, 3)->default(0);
                $table->float('hrk1_waterrw_act_temp', 10, 3)->default(0);
                $table->float('hrk2_actual_weight_kg', 10, 3)->default(0);
                $table->float('hrk2_mass_act_temp', 10, 3)->default(0);
                $table->float('hrk2_waterfw_act_temp', 10, 3)->default(0);
                $table->float('hrk2_waterrw_act_temp', 10, 3)->default(0);
        
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
            'hopper_actual_load' => (float) $data['hopper_actual_load'] ?? 0,
            'hopper_total_volume' => (float) $data['hopper_total_volume'] ?? 0,
            'hopper_tank' => (float) $data['hopper_tank'] ?? 0,
            'hopper_dead_load' => (float) $data['hopper_dead_load'] ?? 0,
            'parameter_pressure_roll_bellow_left_press_set_act' => (float) $data['parameter_pressure_roll_bellow_left_press_set_act'] ?? 0,
            'parameter_pressure_roll_bellow_left_press_pv_act' => (float) $data['parameter_pressure_roll_bellow_left_press_pv_act'] ?? 0,
            'parameter_pressure_roll_bellow_right_press_set_act' => (float) $data['parameter_pressure_roll_bellow_right_press_set_act'] ?? 0,
            'parameter_pressure_roll_bellow_right_press_pv_act' => (float) $data['parameter_pressure_roll_bellow_right_press_pv_act'] ?? 0,
            'mixer1_rec_start_dos_kg' => (float) $data['mixer1_rec_start_dos_kg'] ?? 0,
            'mixer1_mass_act_temp' => (float) $data['mixer1_mass_act_temp'] ?? 0,
            'mixer1_waterfw_act_temp' => (float) $data['mixer1_waterfw_act_temp'] ?? 0,
            'mixer1_waterrw_act_temp' => (float) $data['mixer1_waterrw_act_temp'] ?? 0,
            'mixer1_sp_temp_water_c' => (float) $data['mixer1_sp_temp_water_c'] ?? 0,
            'mixer2_rec_start_dos_kg' => (float) $data['mixer2_rec_start_dos_kg'] ?? 0,
            'mixer2_mass_act_temp' => (float) $data['mixer2_mass_act_temp'] ?? 0,
            'mixer2_waterfw_act_temp' => (float) $data['mixer2_waterfw_act_temp'] ?? 0,
            'mixer2_waterrw_act_temp' => (float) $data['mixer2_waterrw_act_temp'] ?? 0,
            'mixer2_sp_temp_water_c' => (float) $data['mixer2_sp_temp_water_c'] ?? 0,
            'hrk1_actual_weight_kg' => (float) $data['hrk1_actual_weight_kg'] ?? 0,
            'hrk1_mass_act_temp' => (float) $data['hrk1_mass_act_temp'] ?? 0,
            'hrk1_waterfw_act_temp' => (float) $data['hrk1_waterfw_act_temp'] ?? 0,
            'hrk1_waterrw_act_temp' => (float) $data['hrk1_waterrw_act_temp'] ?? 0,
            'hrk2_actual_weight_kg' => (float) $data['hrk2_actual_weight_kg'] ?? 0,
            'hrk2_mass_act_temp' => (float) $data['hrk2_mass_act_temp'] ?? 0,
            'hrk2_waterfw_act_temp' => (float) $data['hrk2_waterfw_act_temp'] ?? 0,
            'hrk2_waterrw_act_temp' => (float) $data['hrk2_waterrw_act_temp'] ?? 0,
        ];
    }

    public function isAlarmOn(): bool
    {
        return false;
    }

    public function creating(Creating $event) {
        $this->id = Uuid::uuid4();
        $this->is_run = $this->{$this->ppm_pv} > 0;
    }

    public function created(Created $event)
    {
        $model = $event->getModel();

        $setting = ScoreSetting::where('device_id', $model->device_id)->first();
        $sp_ppm_1 = $setting?->sp_ppm_1;

        $perfoma = ($model->is_run > 0 && $sp_ppm_1 > 0) ? ($model->{$this->ppm_pv} / $sp_ppm_1) : 0;
        
        // update new data
        $model->fill([
            'sp_ppm_1' => $sp_ppm_1,
            'sp_ppm_2' => null,
            'performance_per_minutes' => $perfoma > 1 ? 1 : $perfoma,
            'performance_per_minutes_2' => null,
        ])->save();
            
        $score = $this->createScoreDaily($model);

        if($score && $model->is_run > 0) {
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

        if($score && $model->is_run <= 0 && $model->isAlarmOn()) {
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

        if($score && $model->is_run <= 0 && ! $model->isAlarmOn()) {
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
