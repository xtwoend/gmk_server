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
use Hyperf\Database\Model\Events\Created;
use Hyperf\Database\Model\Events\Creating;

/**
 */
class SalsaThree extends Model
{
    use DeviceTrait, ResourceTrait, ScoreTrait;
    
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'salsa_three';

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
    public string $ppm_pv = 'rpm_masterrefiner_300_mill';
    public string $ppm_sv = 'SP_LME3_Mill_Speed'; // ambil dari setting
    public string $ppm2_pv = 'rpm_masterrefiner_300_feed_pump';
    public string $ppm2_sv = 'SP_Feed_Pump_Speed'; // ambil dari setting

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

                $table->float('mixing_tank_product_temp', 10, 3)->default(0);
                $table->float('cocoa_butter_melter_boiler_water_temperature', 10, 3)->default(0);
                $table->float('fat_storage_tank_temperature', 10, 3)->default(0);
                $table->float('lecithin_feed_tank_level', 10, 3)->default(0);
                $table->float('process_tank_level', 10, 3)->default(0);
                $table->float('process_tank_temperature', 10, 3)->default(0);
                $table->float('cleaning_fat_tank_pressure', 10, 3)->default(0);
                $table->float('master_refiner_inlet_pressure', 10, 3)->default(0);
                $table->float('master_refiner_outlet_pressure', 10, 3)->default(0);
                $table->float('lobe_pump_outlet_pressure', 10, 3)->default(0);
                $table->float('master_refiner_outlet_temperature', 10, 3)->default(0);
                $table->float('pump_inlet_pressure', 10, 3)->default(0);
                $table->float('pump_outlet_pressure', 10, 3)->default(0);
                $table->float('nt100_tank_temperature', 10, 3)->default(0);
                $table->float('external_samba_outlet_temperature', 10, 3)->default(0);
                $table->float('sugar_current_weight', 10, 3)->default(0);
                $table->float('cocoa_current_weight', 10, 3)->default(0);
                $table->float('milk_powder_current_weight', 10, 3)->default(0);
                $table->float('fat_storage_tank_current_weight', 10, 3)->default(0);
                $table->float('pig_system_pressure', 10, 3)->default(0);
                $table->float('nivel_lecitina_actual', 10, 3)->default(0);
                $table->float('rpm_cocoa_powder_dosage_rotary_valve', 10, 3)->default(0);
                $table->float('rpm_milk_powder_dosage_rotary_valve', 10, 3)->default(0);
                $table->float('rpm_product_transference_pump', 10, 3)->default(0);
                $table->float('rpm_mixing_tank_masterblend_3000_disperser', 10, 3)->default(0);
                $table->float('rpm_external_samba', 10, 3)->default(0);
                $table->float('rpm_fat_storage_tank_transference_pump', 10, 3)->default(0);
                $table->float('rpm_lecithin_add_tank_transference_pump', 10, 3)->default(0);
                $table->float('rpm_lecithin_feed_tank_transference_pump', 10, 3)->default(0);
                $table->float('rpm_process_tank_agitator', 10, 3)->default(0);
                $table->float('rpm_cleaning_fat_tank_disperser', 10, 3)->default(0);
                $table->float('rpm_masterrefiner_300_mill', 10, 3)->default(0);
                $table->float('rpm_masterrefiner_300_feed_pump', 10, 3)->default(0);
                $table->float('rpm_masterrefiner_300_discharge_pump', 10, 3)->default(0);
                $table->float('rpm_sugar_dosage_rotary_valve', 10, 3)->default(0);
                
                $table->boolean('is_run')->default(false);
                $table->float('performance_per_minutes', 10, 2)->nullable();
                $table->integer('sp_ppm_1')->nullable();
                $table->float('performance_per_minutes_2', 10, 2)->nullable();
                $table->integer('sp_ppm_2')->nullable();

                $table->timestamps();
            });
        }

        return $model->setTable($tableName);
    }

    public function format(array $data)
    {
        return [
            'mixing_tank_product_temp' => (float) $data['mixing_tank_product_temp'],
            'cocoa_butter_melter_boiler_water_temperature' => (float) $data['cocoa_butter_melter_boiler_water_temperature'],
            'fat_storage_tank_temperature' => (float) $data['fat_storage_tank_temperature'],
            'lecithin_feed_tank_level' => (float) $data['lecithin_feed_tank_level'],
            'process_tank_level' => (float) $data['process_tank_level'],
            'process_tank_temperature' => (float) $data['process_tank_temperature'],
            'cleaning_fat_tank_pressure' => (float) $data['cleaning_fat_tank_pressure'],
            'master_refiner_inlet_pressure' => (float) $data['master_refiner_inlet_pressure'],
            'master_refiner_outlet_pressure' => (float) $data['master_refiner_outlet_pressure'],
            'lobe_pump_outlet_pressure' => (float) $data['lobe_pump_outlet_pressure'],
            'master_refiner_outlet_temperature' => (float) $data['master_refiner_outlet_temperature'],
            'pump_inlet_pressure' => (float) $data['pump_inlet_pressure'],
            'pump_outlet_pressure' => (float) $data['pump_outlet_pressure'],
            'nt100_tank_temperature' => (float) $data['nt100_tank_temperature'],
            'external_samba_outlet_temperature' => (float) $data['external_samba_outlet_temperature'],
            'sugar_current_weight' => (float) $data['sugar_current_weight'],
            'cocoa_current_weight' => (float) $data['cocoa_current_weight'],
            'milk_powder_current_weight' => (float) $data['milk_powder_current_weight'],
            'fat_storage_tank_current_weight' => (float) $data['fat_storage_tank_current_weight'],
            'pig_system_pressure' => (float) $data['pig_system_pressure'],
            'nivel_lecitina_actual' => (float) $data['nivel_lecitina_actual'],
            'rpm_cocoa_powder_dosage_rotary_valve' => (float) $data['rpm_cocoa_powder_dosage_rotary_valve'],
            'rpm_milk_powder_dosage_rotary_valve' => (float) $data['rpm_milk_powder_dosage_rotary_valve'],
            'rpm_product_transference_pump' => (float) $data['rpm_product_transference_pump'],
            'rpm_mixing_tank_masterblend_3000_disperser' => (float) $data['rpm_mixing_tank_masterblend_3000_disperser'],
            'rpm_external_samba' => (float) $data['rpm_external_samba'],
            'rpm_fat_storage_tank_transference_pump' => (float) $data['rpm_fat_storage_tank_transference_pump'],
            'rpm_lecithin_add_tank_transference_pump' => (float) $data['rpm_lecithin_add_tank_transference_pump'],
            'rpm_lecithin_feed_tank_transference_pump' => (float) $data['rpm_lecithin_feed_tank_transference_pump'],
            'rpm_process_tank_agitator' => (float) $data['rpm_process_tank_agitator'],
            'rpm_cleaning_fat_tank_disperser' => (float) $data['rpm_cleaning_fat_tank_disperser'],
            'rpm_masterrefiner_300_mill' => (float) $data['rpm_masterrefiner_300_mill'],
            'rpm_masterrefiner_300_feed_pump' => (float) $data['rpm_masterrefiner_300_feed_pump'],
            'rpm_masterrefiner_300_discharge_pump' => (float) $data['rpm_masterrefiner_300_discharge_pump'],
            'rpm_sugar_dosage_rotary_valve' => (float) $data['rpm_sugar_dosage_rotary_valve']
        ];
    }

    public function isAlarmOn(): bool
    {
        return false;
    }

    public function creating(Creating $event) {
        
        $this->is_run = $this->rpm_masterrefiner_300_mill >= 0;
    }

    public function created(Created $event)
    {
        $model = $event->getModel();

        $setting = ScoreSetting::where('device_id', $model->device_id)->first();
        $sp_ppm_1 = $setting?->sp_ppm_1;
        $sp_ppm_2 = $setting?->sp_ppm_2;

        $perfoma = ($model->{$this->statusRun} > 0) ? ($model->rpm_masterrefiner_300_mill / $sp_ppm_1) : 0;
        $perfoma2 = ($model->rpm_masterrefiner_300_feed_pump > 0) ? ($model->rpm_masterrefiner_300_feed_pump / $sp_ppm_2) : 0;
        
        // update new data
        $model->fill([
            'sp_ppm_1' => $sp_ppm_1,
            'sp_ppm_2' => $sp_ppm_2,
            'performance_per_minutes' => $perfoma,
            'performance_per_minutes_2' => $perfoma2
        ])->save();

        $score = $this->createScoreDaily($model);

        if($score && $model->{$this->statusRun} > 0) {
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

        if($score && $model->{$this->statusRun} <= 0 && $model->isAlarmOn()) {
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

        if($score && $model->{$this->statusRun} <= 0 && ! $model->isAlarmOn()) {
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
