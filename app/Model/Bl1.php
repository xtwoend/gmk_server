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
class Bl1 extends Model
{
    use DeviceTrait, ResourceTrait, ScoreTrait;
    
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'bl_one';

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
                
                $table->float('depositor_speed_backsucktion_pistonleft', 10, 3)->default(0);
                $table->float('depositor_speed_pressing_pistonleft', 10, 3)->default(0);
                $table->float('depositor_speed_sucktion_pistonleft', 10, 3)->default(0);
                $table->float('depositor_backsucktion_pistonleft', 10, 3)->default(0);
                $table->float('depositor_waiting_time_pistonleft', 10, 3)->default(0);
                $table->float('depositor_speed_backsucktion_pistonright', 10, 3)->default(0);
                $table->float('depositor_speed_pressing_pistonright', 10, 3)->default(0);
                $table->float('depositor_speed_sucktion_pistonright', 10, 3)->default(0);
                $table->float('depositor_backsucktion_pistonright', 10, 3)->default(0);
                $table->float('depositor_zeropoint_pressing_left', 10, 3)->default(0);
                $table->float('depositor_specific_grafity_left', 10, 3)->default(0);
                $table->float('depositor_time_untill_ready', 10, 3)->default(0);
                $table->float('depositor_switch_on_temp', 10, 3)->default(0);
                $table->float('depositor_zeropoint_pressing_right', 10, 3)->default(0);
                $table->float('depositor_specific_grafity_right', 10, 3)->default(0);
                $table->float('depositor_manually_position_left', 10, 3)->default(0);
                $table->float('depositor_manually_position_right', 10, 3)->default(0);
                $table->float('depositor_manually_temp_315r1', 10, 3)->default(0);
                $table->float('depositor_manually_temp_315r2', 10, 3)->default(0);
                $table->float('depositor_weight_pistonleft', 10, 3)->default(0);
                $table->float('depositor_weight_pistonright', 10, 3)->default(0);
                $table->float('depositor_heating_temp', 10, 3)->default(0);
                $table->float('depositor_waitingtime_after_mouldlifter_down', 10, 3)->default(0);
                $table->float('depositor_measurenment_mouldtemp_act', 10, 3)->default(0);
                $table->float('depositor_last_mould_temp_act', 10, 3)->default(0);
                $table->float('mould_temp_preheating_station_setpoint_temp', 10, 3)->default(0);
                $table->float('mould_temp_preheating_station_hysterese_plus', 10, 3)->default(0);
                $table->float('mould_temp_preheating_station_hysterese_minus', 10, 3)->default(0);
                $table->float('mould_temp_preheating_station_setpoint_controller', 10, 3)->default(0);
                $table->float('paternoster1_act_temp_section1', 10, 3)->default(0);
                $table->float('paternoster1_setpoint_temp_section1', 10, 3)->default(0);
                $table->float('paternoster1_act_temp_section2', 10, 3)->default(0);
                $table->float('paternoster1_setpoint_temp_section2', 10, 3)->default(0);
                $table->float('paternoster1_speed_lifting', 10, 3)->default(0);
                $table->float('paternoster1_acc_torque', 10, 3)->default(0);
                $table->float('paternoster1_automatic_torque', 10, 3)->default(0);
                $table->float('paternoster1_braking_torque', 10, 3)->default(0);
                $table->float('paternoster2_speed_lifting', 10, 3)->default(0);
                $table->float('paternoster2_acc_torque', 10, 3)->default(0);
                $table->float('paternoster2_automatic_torque', 10, 3)->default(0);
                $table->float('paternoster2_braking_torque', 10, 3)->default(0);
                $table->float('paternoster2_act_temp_section1', 10, 3)->default(0);
                $table->float('paternoster2_setpoint_temp_section1', 10, 3)->default(0);
                $table->float('paternoster2_act_temp_section2', 10, 3)->default(0);
                $table->float('paternoster2_setpoint_temp_section2', 10, 3)->default(0);
                $table->float('paternoster2_roatation_blast_freezer_sec2', 10, 3)->default(0);
                $table->float('demoulding_setup_mould_turner_position_top', 10, 3)->default(0);
                $table->float('demoulding_setup_lifting_table_position_top', 10, 3)->default(0);
                $table->float('demoulding_setup_mould_turner_position_left', 10, 3)->default(0);
                $table->float('demoulding_setup_mould_turner_speed_fwd', 10, 3)->default(0);
                $table->float('demoulding_setup_mould_turner_speed_fwd_acc', 10, 3)->default(0);
                $table->float('demoulding_setup_mould_turner_speed_fwd_dec', 10, 3)->default(0);
                $table->float('demoulding_setup_mould_turner_speed_bkwd', 10, 3)->default(0);
                $table->float('demoulding_setup_mould_turner_speed_bkwd_acc', 10, 3)->default(0);
                $table->float('demoulding_setup_mould_turner_speed_bkwd_dec', 10, 3)->default(0);
                $table->float('demoulding_setup_lifting_table_speed_lifting', 10, 3)->default(0);
                $table->float('demoulding_setup_lifting_table_speed_lifting_acc', 10, 3)->default(0);
                $table->float('demoulding_setup_lifting_table_speed_lifting_dec', 10, 3)->default(0);
                $table->float('demoulding_setup_lifting_table_speed_lowering', 10, 3)->default(0);
                $table->float('demoulding_setup_lifting_table_speed_lowering_acc', 10, 3)->default(0);
                $table->float('demoulding_setup_lifting_table_speed_lowering_dec', 10, 3)->default(0);
                $table->float('demoulding_lifting_table_manually_position', 10, 3)->default(0);
                $table->float('demoulding_mould_turner_manually_position', 10, 3)->default(0);
                $table->float('demoulding_transportchain_manually_position', 10, 3)->default(0);
                $table->float('demoulding_vibrator', 10, 3)->default(0);
                $table->float('demoulding_manually_belt1_speed', 10, 3)->default(0);
                $table->float('vibrator_station_123', 10, 3)->default(0);

                $table->float('sp_mould_perminute', 10, 3)->default(0);
                $table->float('sv_mould_perminute', 10, 3)->default(0);
                
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
            'depositor_speed_backsucktion_pistonleft' => (float) $data['depositor_speed_backsucktion_pistonleft'] ?? 0,
            'depositor_speed_pressing_pistonleft' => (float) $data['depositor_speed_pressing_pistonleft'] ?? 0,
            'depositor_speed_sucktion_pistonleft' => (float) $data['depositor_speed_sucktion_pistonleft'] ?? 0,
            'depositor_backsucktion_pistonleft' => (float) $data['depositor_backsucktion_pistonleft'] ?? 0,
            'depositor_waiting_time_pistonleft' => (float) $data['depositor_waiting_time_pistonleft'] ?? 0,
            'depositor_speed_backsucktion_pistonright' => (float) $data['depositor_speed_backsucktion_pistonright'] ?? 0,
            'depositor_speed_pressing_pistonright' => (float) $data['depositor_speed_pressing_pistonright'] ?? 0,
            'depositor_speed_sucktion_pistonright' => (float) $data['depositor_speed_sucktion_pistonright'] ?? 0,
            'depositor_backsucktion_pistonright' => (float) $data['depositor_backsucktion_pistonright'] ?? 0,
            'depositor_zeropoint_pressing_left' => (float) $data['depositor_zeropoint_pressing_left'] ?? 0,
            'depositor_specific_grafity_left' => (float) $data['depositor_specific_grafity_left'] ?? 0,
            'depositor_time_untill_ready' => (float) $data['depositor_time_untill_ready'] ?? 0,
            'depositor_switch_on_temp' => (float) $data['depositor_switch_on_temp'] ?? 0,
            'depositor_zeropoint_pressing_right' => (float) $data['depositor_zeropoint_pressing_right'] ?? 0,
            'depositor_specific_grafity_right' => (float) $data['depositor_specific_grafity_right'] ?? 0,
            'depositor_manually_position_left' => (float) $data['depositor_manually_position_left'] ?? 0,
            'depositor_manually_position_right' => (float) $data['depositor_manually_position_right'] ?? 0,
            'depositor_manually_temp_315r1' => (float) $data['depositor_manually_temp_315r1'] ?? 0,
            'depositor_manually_temp_315r2' => (float) $data['depositor_manually_temp_315r2'] ?? 0,
            'depositor_weight_pistonleft' => (float) $data['depositor_weight_pistonleft'] ?? 0,
            'depositor_weight_pistonright' => (float) $data['depositor_weight_pistonright'] ?? 0,
            'depositor_heating_temp' => (float) $data['depositor_heating_temp'] ?? 0,
            'depositor_waitingtime_after_mouldlifter_down' => (float) $data['depositor_waitingtime_after_mouldlifter_down'] ?? 0,
            'depositor_measurenment_mouldtemp_act' => (float) $data['depositor_measurenment_mouldtemp_act'] ?? 0,
            'depositor_last_mould_temp_act' => (float) $data['depositor_last_mould_temp_act'] ?? 0,
            'mould_temp_preheating_station_setpoint_temp' => (float) $data['mould_temp_preheating_station_setpoint_temp'] ?? 0,
            'mould_temp_preheating_station_hysterese_plus' => (float) $data['mould_temp_preheating_station_hysterese+'] ?? 0,
            'mould_temp_preheating_station_hysterese_minus' => (float) $data['mould_temp_preheating_station_hysterese-'] ?? 0,
            'mould_temp_preheating_station_setpoint_controller' => (float) $data['mould_temp_preheating_station_setpoint_controller'] ?? 0,
            'paternoster1_act_temp_section1' => (float) $data['paternoster1_act_temp_section1'] ?? 0,
            'paternoster1_setpoint_temp_section1' => (float) $data['paternoster1_setpoint_temp_section1'] ?? 0,
            'paternoster1_act_temp_section2' => (float) $data['paternoster1_act_temp_section2'] ?? 0,
            'paternoster1_setpoint_temp_section2' => (float) $data['paternoster1_setpoint_temp_section2'] ?? 0,
            'paternoster1_speed_lifting' => (float) $data['paternoster1_speed_lifting'] ?? 0,
            'paternoster1_acc_torque' => (float) $data['paternoster1_acc_torque'] ?? 0,
            'paternoster1_automatic_torque' => (float) $data['paternoster1_automatic_torque'] ?? 0,
            'paternoster1_braking_torque' => (float) $data['paternoster1_braking_torque'] ?? 0,
            'paternoster2_speed_lifting' => (float) $data['paternoster2_speed_lifting'] ?? 0,
            'paternoster2_acc_torque' => (float) $data['paternoster2_acc_torque'] ?? 0,
            'paternoster2_automatic_torque' => (float) $data['paternoster2_automatic_torque'] ?? 0,
            'paternoster2_braking_torque' => (float) $data['paternoster2_braking_torque'] ?? 0,
            'paternoster2_act_temp_section1' => (float) $data['paternoster2_act_temp_section1'] ?? 0,
            'paternoster2_setpoint_temp_section1' => (float) $data['paternoster2_setpoint_temp_section1'] ?? 0,
            'paternoster2_act_temp_section2' => (float) $data['paternoster2_act_temp_section2'] ?? 0,
            'paternoster2_setpoint_temp_section2' => (float) $data['paternoster2_setpoint_temp_section2'] ?? 0,
            'paternoster2_roatation_blast_freezer_sec2' => (float) $data['paternoster2_roatation_blast_freezer_sec2'] ?? 0,
            'demoulding_setup_mould_turner_position_top' => (float) $data['demoulding_setup_mould_turner_position_top'] ?? 0,
            'demoulding_setup_lifting_table_position_top' => (float) $data['demoulding_setup_lifting_table_position_top'] ?? 0,
            'demoulding_setup_mould_turner_position_left' => (float) $data['demoulding_setup_mould_turner_position_left'] ?? 0,
            'demoulding_setup_mould_turner_speed_fwd' => (float) $data['demoulding_setup_mould_turner_speed_fwd'] ?? 0,
            'demoulding_setup_mould_turner_speed_fwd_acc' => (float) $data['demoulding_setup_mould_turner_speed_fwd_acc'] ?? 0,
            'demoulding_setup_mould_turner_speed_fwd_dec' => (float) $data['demoulding_setup_mould_turner_speed_fwd_dec'] ?? 0,
            'demoulding_setup_mould_turner_speed_bkwd' => (float) $data['demoulding_setup_mould_turner_speed_bkwd'] ?? 0,
            'demoulding_setup_mould_turner_speed_bkwd_acc' => (float) $data['demoulding_setup_mould_turner_speed_bkwd_acc'] ?? 0,
            'demoulding_setup_mould_turner_speed_bkwd_dec' => (float) $data['demoulding_setup_mould_turner_speed_bkwd_dec'] ?? 0,
            'demoulding_setup_lifting_table_speed_lifting' => (float) $data['demoulding_setup_lifting_table_speed_lifting'] ?? 0,
            'demoulding_setup_lifting_table_speed_lifting_acc' => (float) $data['demoulding_setup_lifting_table_speed_lifting_acc'] ?? 0,
            'demoulding_setup_lifting_table_speed_lifting_dec' => (float) $data['demoulding_setup_lifting_table_speed_lifting_dec'] ?? 0,
            'demoulding_setup_lifting_table_speed_lowering' => (float) $data['demoulding_setup_lifting_table_speed_lowering'] ?? 0,
            'demoulding_setup_lifting_table_speed_lowering_acc' => (float) $data['demoulding_setup_lifting_table_speed_lowering_acc'] ?? 0,
            'demoulding_setup_lifting_table_speed_lowering_dec' => (float) $data['demoulding_setup_lifting_table_speed_lowering_dec'] ?? 0,
            'demoulding_lifting_table_manually_position' => (float) $data['demoulding_lifting_table_manually_position'] ?? 0,
            'demoulding_mould_turner_manually_position' => (float) $data['demoulding_mould_turner_manually_position'] ?? 0,
            'demoulding_transportchain_manually_position' => (float) $data['demoulding_transportchain_manually_position'] ?? 0,
            'demoulding_vibrator' => (float) $data['demoulding_vibrator'] ?? 0,
            'demoulding_manually_belt1_speed' => (float) $data['demoulding_manually_belt1_speed'] ?? 0,
            'vibrator_station_123' => (float) $data['vibrator_station_123'] ?? 0,
            'sp_mould_perminute' => (float) $data['sp_mould_perminute'] ?? 0,
            'sv_mould_perminute' => (float) $data['sv_mould_perminute'] ?? 0,
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
