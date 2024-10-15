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
class SalsaSix extends Model
{
    use DeviceTrait, ResourceTrait, ScoreTrait;
    
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'salsa_six';

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
    public string $ppm_pv = 'mill_speed';
    public string $ppm_sv = ''; // ambil dari setting
    public string $ppm2_pv = 'feedpump_speed';
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

                $table->tinyInteger('mill_status')->nullable();
                $table->float('mill_max_speed', 10, 4)->nullable();
                $table->float('mill_speed', 10, 4)->nullable();
                $table->float('mill_curent', 10, 4)->nullable();
                $table->float('mill_power', 10, 4)->nullable();

                $table->tinyInteger('sealpump_status')->nullable();
                $table->tinyInteger('feedpump_status')->nullable();
                $table->float('feedpump_maxspeed', 10, 4)->nullable();
                $table->float('feedpump_minspeed', 10, 4)->nullable();
                $table->float('feedpump_speed', 10, 4)->nullable();
                $table->float('feedpump_current', 10, 4)->nullable();
                $table->float('feedpump_power', 10, 4)->nullable();

                $table->tinyInteger('agitator_status')->nullable();
                $table->float('agitator_maxspeed', 10, 4)->nullable();
                $table->float('agitator_minspeed', 10, 4)->nullable();
                $table->float('agitator_speed', 10, 4)->nullable();
                $table->float('agitator_current', 10, 4)->nullable();
                $table->float('agitator_power', 10, 4)->nullable();

                $table->float('dischpump_status', 10, 4)->nullable();
                $table->float('dischpump_maxspeed', 10, 4)->nullable();
                $table->float('dischpump_mispeed', 10, 4)->nullable();
                $table->float('dischpump_speed', 10, 4)->nullable();
                $table->float('dischpump_current', 10, 4)->nullable();
                $table->float('dischpump_power', 10, 4)->nullable();

                $table->tinyInteger('general_machine_status')->nullable();
                $table->float('general_energy', 10, 4)->nullable();
                $table->float('product_pressure_inlet', 10, 4)->nullable();
                $table->float('product_temperature_outlet', 10, 4)->nullable();
                $table->float('cooling_water_flow', 10, 4)->nullable();

                $table->timestamps();
            });
            
        }

        if(Schema::hasTable($tableName)) {
            if (! Schema::hasColumn($tableName, 'is_run')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->boolean('is_run')->default(false);
                    $table->float('performance_per_minutes', 3, 2)->nullable();
                    $table->integer('sp_ppm_1')->nullable();
                    $table->float('performance_per_minutes_2', 3, 2)->nullable();
                    $table->integer('sp_ppm_2')->nullable();
                });
            }
        }


        return $model->setTable($tableName);
    }

    public function format(array $data)
    {
        return [
            'mill_status' => (float) $data['mill_status'] ?: 0,
            'mill_max_speed' => (float) $data['mill_max_speed'] ?: 0,
            'mill_speed' => (float) $data['mill_speed'] ?: 0,
            'mill_curent' => (float) $data['mill_curent'] ?: 0,
            'mill_power' => (float) $data['mill_power'] ?: 0,
            'sealpump_status' => (float) $data['sealpump_status'] ?: 0,
            'feedpump_status' => (float) $data['feedpump_status'] ?: 0,
            'feedpump_maxspeed' => (float) $data['feedpump_maxspeed'] ?: 0,
            'feedpump_minspeed' => (float) $data['feedpump_minspeed'] ?: 0,
            'feedpump_speed' => (float) $data['feedpump_speed'] ?: 0,
            'feedpump_current' => (float) $data['feedpump_current'] ?: 0,
            'feedpump_power' => (float) $data['feedpump_power'] ?: 0,
            'agitator_status' => (float) $data['agitator_status'] ?: 0,
            'agitator_maxspeed' => (float) $data['agitator_maxspeed'] ?: 0,
            'agitator_minspeed' => (float) $data['agitator_minspeed'] ?: 0,
            'agitator_speed' => (float) $data['agitator_speed'] ?: 0,
            'agitator_current' => (float) $data['agitator_current'] ?: 0,
            'agitator_power' => (float) $data['agitator_power'] ?: 0,
            'dischpump_status' => (float) $data['dischpump_status'] ?: 0,
            'dischpump_maxspeed' => (float) $data['dischpump_maxspeed'] ?: 0,
            'dischpump_mispeed' => (float) $data['dischpump_mispeed'] ?: 0,
            'dischpump_speed' => (float) $data['dischpump_speed'] ?: 0,
            'dischpump_current' => (float) $data['dischpump_current'] ?: 0,
            'dischpump_power' => (float) $data['dischpump_power'] ?: 0,
            'general_machine_status' => (float) $data['general_machine_status'] ?: 0,
            'general_energy' => (float) $data['general_energy'] ?: 0,
            'product_pressure_inlet' => (float) $data['product_pressure_inlet'] ?: 0,
            'product_temperature_outlet' => (float) $data['product_temperature_outlet'] ?: 0,
            'cooling_water_flow' => (float) $data['cooling_water_flow'] ?: 0,
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

        $perfoma = ($model->mill_speed > 0) ? ($model->mill_speed / $sp_ppm_1) : 0;
        $perfoma2 = ($model->mill_speed > 0) ? ($model->feedpump_speed / $sp_ppm_2) : 0;
        
        // update new data
        $model->fill([
            'sp_ppm_1' => $sp_ppm_1,
            'sp_ppm_2' => $sp_ppm_2,
            'performance_per_minutes' => $perfoma > 1 ? 1 : $perfoma,
            'performance_per_minutes_2' => $perfoma2 > 1 ? 1 : $perfoma2
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
