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
class Bl2 extends Model
{
    use DeviceTrait, ResourceTrait, ScoreTrait;
    
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'bl_two';

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
    public string $ppm_pv = 'sv_mould_perminute';
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

                $table->float('transportchain_speed', 10, 3)->default(0);
                $table->float('transportchain_acc', 10, 3)->default(0);
                $table->float('transportchain_dec', 10, 3)->default(0);
                $table->float('turner_left_speed', 10, 3)->default(0);
                $table->float('turner_left_acc', 10, 3)->default(0);
                $table->float('turner_left_dec', 10, 3)->default(0);
                $table->float('turner_right_speed', 10, 3)->default(0);
                $table->float('turner_right_acc', 10, 3)->default(0);
                $table->float('turner_right_dec', 10, 3)->default(0);
                $table->float('table_left_speed', 10, 3)->default(0);
                $table->float('depositor_zeropoint_pressing_left', 10, 3)->default(0);
                $table->float('depositor_maximal_torque_left', 10, 3)->default(0);
                $table->float('depositor_zeropoint_pressing_right', 10, 3)->default(0);
                $table->float('depositor_maximal_torque_right', 10, 3)->default(0);
                $table->float('depositor_weight_pistonleft', 10, 3)->default(0);
                $table->float('depositor_weight_pistonright', 10, 3)->default(0);
                $table->float('depositor_backsuction_pistonleft', 10, 3)->default(0);
                $table->float('depositor_backsuction_pistonright', 10, 3)->default(0);
                $table->float('depositor_heating_temp', 10, 3)->default(0);
                $table->float('depositor_engine _temp', 10, 3)->default(0);
                $table->float('depositor_speed_pressing_pistonleft', 10, 3)->default(0);
                $table->float('depositor_speed_pressing_pistonright', 10, 3)->default(0);
                $table->float('depositor_speed_backsuction_pistonleft', 10, 3)->default(0);
                $table->float('depositor_speed_backsuction_pistonright', 10, 3)->default(0);
                $table->float('depositor_speed_suction_pistonleft', 10, 3)->default(0);
                $table->float('depositor_speed_suction_pistonright', 10, 3)->default(0);
                $table->float('depositor_density_left', 10, 3)->default(0);
                $table->float('depositor_density_right', 10, 3)->default(0);
                $table->float('paternoster1_temp_cooling_sec1_soll', 10, 3)->default(0);
                $table->float('paternoster1_temp_cooling_sec1_Ist', 10, 3)->default(0);
                $table->float('paternoster1_setpoint_sec1', 10, 3)->default(0);
                $table->float('paternoster1_temp_cooling_sec2_soll', 10, 3)->default(0);
                $table->float('paternoster1_temp_cooling_sec2_Ist', 10, 3)->default(0);
                $table->float('paternoster1_setpoint_sec2', 10, 3)->default(0);
                $table->float('paternoster2_temp_cooling_sec1_soll', 10, 3)->default(0);
                $table->float('paternoster2_temp_cooling_sec1_Ist', 10, 3)->default(0);
                $table->float('paternoster2_setpoint_sec1', 10, 3)->default(0);
                $table->float('paternoster2_temp_cooling_sec2_soll', 10, 3)->default(0);
                $table->float('paternoster2_temp_cooling_sec2_Ist', 10, 3)->default(0);
                $table->float('paternoster2_setpoint_sec2', 10, 3)->default(0);
                $table->float('demoulding_knife_heating', 10, 3)->default(0);
                $table->float('demoulding_vibrator', 10, 3)->default(0);
                $table->float('separation_twister', 10, 3)->default(0);
                $table->float('separation_manually_setpoint', 10, 3)->default(0);
                $table->float('vibration_station1', 10, 3)->default(0);
                $table->float('vibration_station2', 10, 3)->default(0);
                $table->float('vibration_station3', 10, 3)->default(0);
                $table->float('vibration_station4', 10, 3)->default(0);
                $table->float('vibration_station5', 10, 3)->default(0);
                $table->float('heating_setpoint_temp_station1', 10, 3)->default(0);
                $table->float('heating_act_temp_station1', 10, 3)->default(0);
                $table->float('heating_setpoint_temp_station2', 10, 3)->default(0);
                $table->float('heating_act_temp_station2', 10, 3)->default(0);
                $table->float('transport_belt', 10, 3)->default(0);
                $table->float('demoulding_turner_position_left', 10, 3)->default(0);
                $table->float('demoulding_turner_position_right', 10, 3)->default(0);
                $table->float('demoulding_turner_limitswitch_left', 10, 3)->default(0);
                $table->float('demoulding_turner_limitswitch_right', 10, 3)->default(0);
                $table->float('demoulding_table_position_left', 10, 3)->default(0);
                $table->float('demoulding_table_limitswitch_left', 10, 3)->default(0);
                $table->float('demoulding_table_limitswitch_right', 10, 3)->default(0);

                $table->float('sv_mould_perminute', 10, 3)->default(0);
                $table->float('pv_mould_perminute', 10, 3)->default(0);
                
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
            'transportchain_speed' => (float) $data['transportchain_speed'] ?? 0,
            'transportchain_acc' => (float) $data['transportchain_acc'] ?? 0,
            'transportchain_dec' => (float) $data['transportchain_dec'] ?? 0,
            'turner_left_speed' => (float) $data['turner_left_speed'] ?? 0,
            'turner_left_acc' => (float) $data['turner_left_acc'] ?? 0,
            'turner_left_dec' => (float) $data['turner_left_dec'] ?? 0,
            'turner_right_speed' => (float) $data['turner_right_speed'] ?? 0,
            'turner_right_acc' => (float) $data['turner_right_acc'] ?? 0,
            'turner_right_dec' => (float) $data['turner_right_dec'] ?? 0,
            'table_left_speed' => (float) $data['table_left_speed'] ?? 0,
            'depositor_zeropoint_pressing_left' => (float) $data['depositor_zeropoint_pressing_left'] ?? 0,
            'depositor_maximal_torque_left' => (float) $data['depositor_maximal_torque_left'] ?? 0,
            'depositor_zeropoint_pressing_right' => (float) $data['depositor_zeropoint_pressing_right'] ?? 0,
            'depositor_maximal_torque_right' => (float) $data['depositor_maximal_torque_right'] ?? 0,
            'depositor_weight_pistonleft' => (float) $data['depositor_weight_pistonleft'] ?? 0,
            'depositor_weight_pistonright' => (float) $data['depositor_weight_pistonright'] ?? 0,
            'depositor_backsuction_pistonleft' => (float) $data['depositor_backsuction_pistonleft'] ?? 0,
            'depositor_backsuction_pistonright' => (float) $data['depositor_backsuction_pistonright'] ?? 0,
            'depositor_heating_temp' => (float) $data['depositor_heating_temp'] ?? 0,
            'depositor_engine _temp' => (float) $data['depositor_engine _temp'] ?? 0,
            'depositor_speed_pressing_pistonleft' => (float) $data['depositor_speed_pressing_pistonleft'] ?? 0,
            'depositor_speed_pressing_pistonright' => (float) $data['depositor_speed_pressing_pistonright'] ?? 0,
            'depositor_speed_backsuction_pistonleft' => (float) $data['depositor_speed_backsuction_pistonleft'] ?? 0,
            'depositor_speed_backsuction_pistonright' => (float) $data['depositor_speed_backsuction_pistonright'] ?? 0,
            'depositor_speed_suction_pistonleft' => (float) $data['depositor_speed_suction_pistonleft'] ?? 0,
            'depositor_speed_suction_pistonright' => (float) $data['depositor_speed_suction_pistonright'] ?? 0,
            'depositor_density_left' => (float) $data['depositor_density_left'] ?? 0,
            'depositor_density_right' => (float) $data['depositor_density_right'] ?? 0,
            'paternoster1_temp_cooling_sec1_soll' => (float) $data['paternoster1_temp_cooling_sec1_soll'] ?? 0,
            'paternoster1_temp_cooling_sec1_Ist' => (float) $data['paternoster1_temp_cooling_sec1_Ist'] ?? 0,
            'paternoster1_setpoint_sec1' => (float) $data['paternoster1_setpoint_sec1'] ?? 0,
            'paternoster1_temp_cooling_sec2_soll' => (float) $data['paternoster1_temp_cooling_sec2_soll'] ?? 0,
            'paternoster1_temp_cooling_sec2_Ist' => (float) $data['paternoster1_temp_cooling_sec2_Ist'] ?? 0,
            'paternoster1_setpoint_sec2' => (float) $data['paternoster1_setpoint_sec2'] ?? 0,
            'paternoster2_temp_cooling_sec1_soll' => (float) $data['paternoster2_temp_cooling_sec1_soll'] ?? 0,
            'paternoster2_temp_cooling_sec1_Ist' => (float) $data['paternoster2_temp_cooling_sec1_Ist'] ?? 0,
            'paternoster2_setpoint_sec1' => (float) $data['paternoster2_setpoint_sec1'] ?? 0,
            'paternoster2_temp_cooling_sec2_soll' => (float) $data['paternoster2_temp_cooling_sec2_soll'] ?? 0,
            'paternoster2_temp_cooling_sec2_Ist' => (float) $data['paternoster2_temp_cooling_sec2_Ist'] ?? 0,
            'paternoster2_setpoint_sec2' => (float) $data['paternoster2_setpoint_sec2'] ?? 0,
            'demoulding_knife_heating' => (float) $data['demoulding_knife_heating'] ?? 0,
            'demoulding_vibrator' => (float) $data['demoulding_vibrator'] ?? 0,
            'separation_twister' => (float) $data['separation_twister'] ?? 0,
            'separation_manually_setpoint' => (float) $data['separation_manually_setpoint'] ?? 0,
            'vibration_station1' => (float) $data['vibration_station1'] ?? 0,
            'vibration_station2' => (float) $data['vibration_station2'] ?? 0,
            'vibration_station3' => (float) $data['vibration_station3'] ?? 0,
            'vibration_station4' => (float) $data['vibration_station4'] ?? 0,
            'vibration_station5' => (float) $data['vibration_station5'] ?? 0,
            'heating_setpoint_temp_station1' => (float) $data['heating_setpoint_temp_station1'] ?? 0,
            'heating_act_temp_station1' => (float) $data['heating_act_temp_station1'] ?? 0,
            'heating_setpoint_temp_station2' => (float) $data['heating_setpoint_temp_station2'] ?? 0,
            'heating_act_temp_station2' => (float) $data['heating_act_temp_station2'] ?? 0,
            'transport_belt' => (float) $data['transport_belt'] ?? 0,
            'demoulding_turner_position_left' => (float) $data['demoulding_turner_position_left'] ?? 0,
            'demoulding_turner_position_right' => (float) $data['demoulding_turner_position_right'] ?? 0,
            'demoulding_turner_limitswitch_left' => (float) $data['demoulding_turner_limitswitch_left'] ?? 0,
            'demoulding_turner_limitswitch_right' => (float) $data['demoulding_turner_limitswitch_right'] ?? 0,
            'demoulding_table_position_left' => (float) $data['demoulding_table_position_left'] ?? 0,
            'demoulding_table_limitswitch_left' => (float) $data['demoulding_table_limitswitch_left'] ?? 0,
            'demoulding_table_limitswitch_right' => (float) $data['demoulding_table_limitswitch_right'] ?? 0,

            'sv_mould_perminute' => (float) $data['sv_mould_perminute'] ?? 0,
            'pv_mould_perminute' => (float) $data['pv_mould_perminute'] ?? 0,
        ];
    }

    public function isAlarmOn(): bool
    {
        return false;
    }

    public function creating(Creating $event) {
        $this->id = Uuid::uuid4();
        $this->is_run = $this->sv_mould_perminute > ($this->sp_mould_perminute / 2);
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
