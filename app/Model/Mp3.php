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

    // trigger run status
    public string $statusRun = 'is_run';
    public string $ppm_pv = 'pv_pump_speed';
    public string $ppm_sv = ''; // ambil dari setting
    public string $ppm2_pv = '';
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

                $table->float('pv_pump_speed', 10, 3)->default(0);
                $table->float('sp_pump_speed', 10, 3)->default(0);

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
            'water_heater_pv' => (float) $data['water_heater_pv'] ?? 0,
            'water_heater_sv' => (float) $data['water_heater_sv'] ?? 0,
            'pump_speed' => (float) $data['pump_speed'] ?? 0,
            'hose_heater_pv' => (float) $data['hose_heater_pv'] ?? 0,
            'hose_hwater_sv' => (float) $data['hose_hwater_sv'] ?? 0,
            'encoder_cutoff_value' => (float) $data['encoder_cutoff_value'] ?? 0,
            'cutter_cutter_speed' => (float) $data['cutter_cutter_speed'] ?? 0,
            'cutter_belt_speed' => (float) $data['cutter_belt_speed'] ?? 0,
            'cutter_cutting_lenght' => (float) $data['cutter_cutting_lenght'] ?? 0,
            'servo_pulse_travel' => (float) $data['servo_pulse_travel'] ?? 0,
            'water_heater_alarm_upper_limit' => (float) $data['water_heater_alarm_upper_limit'] ?? 0,
            'water_heater_alarm_lower_limit' => (float) $data['water_heater_alarm_lower_limit'] ?? 0,
            'hose_heater_alarm_upper_limit' => (float) $data['hose_heater_alarm_upper_limit'] ?? 0,
            'hose_heater_alarm_lower_limit' => (float) $data['hose_heater_alarm_lower_limit'] ?? 0,
            'recipe_par_conveyor_belt_sv' => (float) $data['recipe_par_conveyor_belt_sv'] ?? 0,
            'recipe_par_cutter_speed' => (float) $data['recipe_par_cutter_speed'] ?? 0,
            'depositor_pump_speed' => (float) $data['depositor_pump_speed'] ?? 0,
            'stripe_lenght' => (float) $data['stripe_lenght'] ?? 0,
            'cutting_offset_increament_value' => (float) $data['cutting_offset_increament_value'] ?? 0,
            'cutting_offset_decreament_value' => (float) $data['cutting_offset_decreament_value'] ?? 0,
            'pv_pump_speed' => (float) $data['pv_pump_speed'] ?? 0,
            'sp_pump_speed' => (float) $data['sp_pump_speed'] ?? 0,
        ];
    }

    public function isAlarmOn(): bool
    {
        return false;
    }

    public function creating(Creating $event) {
        $this->id = Uuid::uuid4();
        $this->is_run = $this->pv_pump_speed > ($this->sp_pump_speed / 2);
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
