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
class Buhler extends Model
{
    use DeviceTrait, ResourceTrait, ScoreTrait;
    
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'buhlers';

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
    public string $ppm_pv = 'temperature_heating_house';
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

                $table->float('water_temperature', 10, 3)->default(0);
                $table->float('temperature_heating_house', 10, 3)->default(0);
                $table->float('water_temperature_in', 10, 3)->default(0);
                $table->float('water_temperature_out', 10, 3)->default(0);
                $table->float('level_control_left', 10, 3)->default(0);
                $table->float('cooling_temperature1_right', 10, 3)->default(0);
                $table->float('cooling_temperature2_left', 10, 3)->default(0);
                $table->float('cooling_temperature3_right', 10, 3)->default(0);
                $table->float('cooling_temperature4_left', 10, 3)->default(0);
                $table->boolean('run_status')->default(false);

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
            'water_temperature' => (float) $data['water_temperature'],
            'temperature_heating_house' => (float) $data['temperature_heating_house'],
            'water_temperature_in' => (float) $data['water_temperature_in'],
            'water_temperature_out' => (float) $data['water_temperature_out'],
            'level_control_left' => (float) $data['level_control_left'],
            'cooling_temperature1_right' => (float) $data['cooling_temperature1_right'],
            'cooling_temperature2_left' => (float) $data['cooling_temperature2_left'],
            'cooling_temperature3_right' => (float) $data['cooling_temperature3_right'],
            'cooling_temperature4_left' => (float) $data['cooling_temperature4_left'],
            'run_status' => (bool) $data['run_status'],
        ];
    }

    public function isAlarmOn(): bool
    {
        return false;
    }

    public function creating(Creating $event) {
        $this->id = Uuid::uuid4();
        $this->is_run = $this->run_status;
    }

    public function created(Created $event)
    {
        $model = $event->getModel();

        $setting = ScoreSetting::where('device_id', $model->device_id)->first();
        $sp_ppm_1 = $setting?->sp_ppm_1;

        $perfoma = ($model->run_status > 0) ? ($model->temperature_heating_house / $sp_ppm_1) : 0;
        
        // update new data
        $model->fill([
            'sp_ppm_1' => $sp_ppm_1,
            'sp_ppm_2' => null,
            'performance_per_minutes' => $perfoma > 1 ? 1 : $perfoma,
            'performance_per_minutes_2' => null,
        ])->save();

        $score = $this->createScoreDaily($model);

        if($score && $model->run_status > 0) {
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

        if($score && $model->run_status <= 0 && $model->isAlarmOn()) {
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

        if($score && $model->run_status <= 0 && ! $model->isAlarmOn()) {
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
