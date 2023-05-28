<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use App\Model\Alarm;
use Hyperf\DbConnection\Db;
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Model\Events\Created;

/**
 */
class Leepack1 extends Model
{
    use DeviceTrait, ResourceTrait, ScoreTrait;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'leepack';
    protected string $primaryKey = 'id';
    protected string $keyType = 'string';
    public bool $incrementing = false;

    protected array $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'terminal_time' => 'datetime',
        'alarm_leepack1' => 'array',
        'inverter_fault' => 'boolean',
        'servo_fault' => 'boolean',
        'mc_run' => 'boolean'
    ];

    /**
     * export table headers
     */
    protected $headersExport = [
        'terminal_time' => 'Timestamp',
    ];

    /**
     * timestamp attribute from device iot gateway
     */
    public string $ts = 'ts';

    public array $desc_alarm_leepack1 = [
        'Air pressure drop',
        'Abnormal temperature',
        'Data limit over',
        'Gripper limit error',
        'Brake power fault',
        'Bag open check miss',
        'Target bag completion',
        'Grease end',
        'Filling time over',
        'Hopper low level',
        'Inverter alarm',
        'Servo drive alarm',
        'Bag loading check miss',
        'Printer alarm',
        'Fill nozzle open/close miss',
        'Please,enter password',
        'Drain mode',
        'Bag open width limit over',
        'Bag low level alarm',
        'PLC APM Module alarm',
        'Heat transfer oil low level',
        'Product temp. low alarm',
        'Recipe change confirmation',
        'Safety guard open'
    ];

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
                $table->text('alarm_leepack1')->nullable();
                $table->boolean('mc_run')->nullable();
                $table->boolean('mc_stop')->nullable();
                $table->integer('filling_speed')->nullable();
                $table->integer('sv_speed_bpm')->nullable();
                $table->integer('pv_speed_bpm')->nullable();
                $table->integer('sv_bag')->nullable();
                $table->integer('pv_bag')->nullable();
                $table->integer('sv_filling_speed_rpm')->nullable();
                $table->integer('pv_filling_speed_rpm')->nullable();
                $table->integer('sv_gripper_width')->nullable();
                $table->integer('sp_gripper_width')->nullable();
                $table->boolean('inverter_fault')->default(false);
                $table->boolean('servo_fault')->default(false);
                $table->timestamps();
            });
        }

        return $model->setTable($tableName);
    }

    public function format(array $data)
    {
        return [
            'alarm_leepack1' => $this->map($data['alarm_leepack1']),
            'mc_run' => $data['mc_run'],
            'mc_stop' => $data['mc_stop'],
            'filling_speed' => $data['filling_speed'],
            'sv_speed_bpm' => $data['sv_speed_bpm'],
            'pv_speed_bpm' => $data['pv_speed_bpm'],
            'sv_bag' => $data['sv_bag'],
            'pv_bag' => $data['pv_bag'],
            'sv_filling_speed_rpm' => $data['sv_filling_speed_rpm'],
            'pv_filling_speed_rpm' => $data['pv_filling_speed_rpm'],
            'sv_gripper_width' => $data['sv_gripper_width'],
            'sp_gripper_width' => $data['sp_gripper_width'],
            'inverter_fault' => $data['inverter_fault'],
            'servo_fault' => $data['servo_fault']
        ];
    }

    /**
     * created
     */
    public function created(Created $event)
    {
        $model = $event->getModel();
       
        $this->alarmDb($model, 'alarm_leepack1');
        $score = $this->createScoreShift($model);

        if($score && $model->mc_run) {
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

        if($score && ! $model->mc_run && ! $this->isAlarmOn()) {
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

        if($score && ! $model->mc_run && $this->isAlarmOn()) {
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
    }

    public function isAlarmOn(): bool
    {
        return (bool) $this->inverter_fault || $this->servo_fault;
    }

    public function export($device, $request)
    {
        $classModel = self::class;

        $from = $request->input('from', Carbon::now()->format('Y-m-d H:i:s'));
        $to = $request->input('to', Carbon::now()->format('Y-m-d H:i:s'));
        $interval = $request->input('interval', 60);

        $from = Carbon::parse($from)->timezone('Asia/Jakarta');
        $to = Carbon::parse($to)->timezone('Asia/Jakarta');

        $query = [];
        $fromClone = clone $from;
        $toClone = clone $to;

        $fromDiff = $from->format("Y-m-01 00:00:00");
        $toDiff = $to->format("Y-m-01 00:00:00");


        $count = Carbon::parse($fromDiff)->diffInMonths(Carbon::parse($toDiff));
        
        for($i=0; $i <= $count; $i++) {
            $tableName = (new $classModel)->table($device, $from)->getTable();
            $query[] = "
            (select 
                (UNIX_TIMESTAMP(ct.terminal_time) * 1000) as unix_time, ct.*
                from {$tableName} as `ct` 
                    inner join 
                    (
                        SELECT MIN(terminal_time) as times, FLOOR(UNIX_TIMESTAMP(terminal_time)/{$interval}) AS timekey 
                        FROM {$tableName} 
                        WHERE terminal_time BETWEEN '{$fromClone->format('Y-m-d H:i:s')}' AND '{$toClone->format('Y-m-d H:i:s')}' 
                        GROUP BY timekey
                    ) ctx 
                    on `ct`.`terminal_time` = `ctx`.`times`)
            ";
            $from = $from->addMonth();
        }
        $querySql = implode(' UNION ', $query);
        
        $rows = Db::table(Db::raw("($querySql order by terminal_time asc) as datalog"));

        if($request->has('sortBy')) {
            $column = $request->input('sortBy');
            $dir = $request->input('sortType');
            $rows = $rows->orderBy($column, $dir);
        }

        $rows = $rows->get()->toArray();

        $headers =[
            'terminal_time' => 'Timestamp',
            'mc_run' => 'MC Status',
            'filling_speed' => 'Filling Speed',
            'sv_speed_bpm' => 'SV Speed BPM',
            'pv_speed_bpm' => 'PV Speed BPM',
            'sv_bag' => 'SV Bag',
            'pv_bag' => 'PV Bag',
            'sv_filling_speed_rpm' => 'sv_filling_speed_rpm',
            'pv_filling_speed_rpm' => 'pv_filling_speed_rpm',
            'sv_gripper_width' => 'sv_gripper_width',
            'sp_gripper_width' => 'sp_gripper_width',
        ];
    
        return export($headers, $rows, 'report_leepack1_'.$from.'-'.$to);
    }
}
