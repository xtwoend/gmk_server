<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\DbConnection\Db;
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Model\Events\Created;
use Hyperf\Database\Model\Events\Creating;

/**
 */
class Lme3 extends Model
{
    use DeviceTrait, ResourceTrait, ScoreTrait;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'lme';
    protected string $primaryKey = 'id';
    protected string $keyType = 'string';
    public bool $incrementing = false;

    protected array $guarded = ['id'];

    public string $statusRun = 'IHM_ST_Moinho_status';
    public string $ppm_pv = 'data8';
    public string $ppm_sv = 'SP_LME3_Mill_Speed';
    public string $ppm2_pv = 'data6';
    public string $ppm2_sv = 'SP_Feed_Pump_Speed';

    /**
     * export table headers
     */
    protected $headersExport = [
        'terminal_time' => 'Timestamp',
    ];
    
    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'terminal_time' => 'datetime',
        'alarm_message_1' => 'array',
        'alarm_message_2' => 'array',
        'alarm_message_3' => 'array',
        'alarm_message_4' => 'array',
        'data1' => 'decimal:2',
        'data2' => 'decimal:2',
        'data3' => 'decimal:2',
        'data4' => 'decimal:2',
        'data5' => 'decimal:2',
        'data6' => 'decimal:2',
        'data7' => 'decimal:2',
        'data8' => 'decimal:2',
        'data9' => 'decimal:2',
        'data10' => 'decimal:2',
        'SP_LME3_Mill_Speed' => 'decimal:2',
        'performance_per_minutes' => 'decimal:2',
        'temp_chilled_water_in' => 'decimal:2',
        'temp_chilled_water_out' => 'decimal:2',
        'performance_per_minutes_2' => 'decimal:2',
        'chilled_water_in_run' => 'boolean',
        'chilled_water_out_run' => 'boolean',
        'chilled_water_in' => 'decimal:2',
        'chilled_water_out' => 'decimal:2',
        'cooling_tank' => 'decimal:2',
        'holding_tank' => 'decimal:2'
    ];

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
                $table->text('alarm_message_1')->nullable();
                $table->text('alarm_message_2')->nullable();
                $table->text('alarm_message_3')->nullable();
                $table->text('alarm_message_4')->nullable();
                $table->float('data1', 10, 2)->nullable();
                $table->float('data2', 10, 2)->nullable();
                $table->float('data3', 10, 2)->nullable();
                $table->float('data4', 10, 2)->nullable();
                $table->float('data5', 10, 2)->nullable();
                $table->float('data6', 10, 2)->nullable();
                $table->float('data7', 10, 2)->nullable();
                $table->float('data8', 10, 2)->nullable();
                $table->float('data9', 10, 2)->nullable();
                $table->float('data10', 10, 2)->nullable();
                $table->tinyInteger('IHM_ST_Moinho_status')->nullable();
                $table->float('SP_LME3_Mill_Speed', 10, 2)->nullable();
                $table->float('performance_per_minutes', 10, 2)->nullable();
                $table->tinyInteger('alarm1')->default(0);
                $table->tinyInteger('alarm2')->default(0);
                $table->tinyInteger('alarm3')->default(0);
                $table->tinyInteger('alarm4')->default(0);
                // addeded
                $table->float('temp_chilled_water_in', 10, 2)->nullable();
                $table->float('temp_chilled_water_out', 10, 2)->nullable();
                $table->tinyInteger('feedpump_status')->nullable();
                $table->tinyInteger('oil_transfer_pump_status')->nullable();
                $table->integer('SP_Feed_Pump_Speed')->nullable();
                $table->float('performance_per_minutes_2', 10, 2)->nullable();

                // added 2023-08-14 14.32
                $table->boolean('chilled_water_in_run')->default(false);
                $table->boolean('chilled_water_out_run')->default(false);
                $table->float('chilled_water_in', 10, 2)->default(0);
                $table->float('chilled_water_out', 10, 2)->default(0);

                // added 2023-09-13 21.56
                $table->float('cooling_tank', 10, 2)->default(0);
                $table->float('holding_tank', 10, 2)->default(0);

                $table->timestamps();
            });
        }

        return $model->setTable($tableName);
    }

    public function format(array $data)
    { 
        $perfoma = ($data['SP_LME3_Mill_Speed'] > 0) ? ($data['data8'] / $data['SP_LME3_Mill_Speed']) : 0;
        $perfoma2 = ($data['SP_Feed_Pump_Speed'] > 0) ? ($data['data6'] / $data['SP_Feed_Pump_Speed']) : 0;

        /**
         * PV speed = data6
         * SV speed =
         */
        return [
            'alarm_message_1' => $this->map($data['alarm_message_1']),
            'alarm_message_2' => $this->map($data['alarm_message_2']),
            'alarm_message_3' => $this->map($data['alarm_message_3']),
            'alarm_message_4' => $this->map($data['alarm_message_4']),
            'data1' => $data['data1'],
            'data2' => $data['data2'],
            'data3' => $data['data3'],
            'data4' => $data['data4'],
            'data5' => $data['data5'],
            'data6' => $data['data6'],
            'data7' => $data['data7'],
            'data8' => $data['data8'],
            'data9' => $data['data9'],
            'data10' => $data['data10'],
            'IHM_ST_Moinho_status' => $data['IHM_ST_Moinho_status'],
            'SP_LME3_Mill_Speed' => $data['SP_LME3_Mill_Speed'],
            'performance_per_minutes' => $perfoma,
            'alarm1' => $data['alarm1'],
            'alarm2' => $data['alarm2'],
            'alarm3' => $data['alarm3'],
            'alarm4' => $data['alarm4'],
            // 'temp_chilled_water_in' => $data['temp_chilled_water_in'],
            // 'temp_chilled_water_out' => $data['temp_chilled_water_out'],
            'feedpump_status' => $data['feedpump_status'],
            'oil_transfer_pump_status' => $data['oil_transfer_pump_status'],
            'SP_Feed_Pump_Speed' => $data['SP_Feed_Pump_Speed'],
            'performance_per_minutes_2' => $perfoma2,

            'chilled_water_in_run' => $data['di_pkp1.1'][5],
            'chilled_water_out_run' => $data['di_pkp1.1'][6],
            'chilled_water_in' => $data['ai_pkp1.1'][5],
            'chilled_water_out' => $data['ai_pkp1.1'][6],

            'cooling_tank' => $data['ai_pkp1.1'][7],
            'holding_tank' => $data['ai_pkp1.1'][8],
        ];
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
            'data1' => 'Mill Current (a)',
            'data3' => 'Mill Inlet Pressure (bar)',
            'data4' => 'Mill Outlet Pressure (bar)',
            'data6' => 'Feed Pump Speed (rpm)',
            'data8' => 'Mill Motor Speed (rpm)',
            'data9' => 'Product Output Temperature (°C)',
            'IHM_ST_Moinho_status' => 'Mill Motor Status',
            'chilled_water_in' => 'Chilled Water In',
            'chilled_water_out' => 'Chilled Water Out',
            'chilled_water_in_run' => 'Chilled Water In Status',
            'chilled_water_out_run' => 'Chilled Water Out Status',
        ];
    
        return export($headers, $rows, 'report_lme3_'.$from.'-'.$to);
    }

    /**
     * created
     */
    public function created(Created $event)
    {
        $model = $event->getModel();
        
        $this->alarmDb($model, 'alarm_message_1');
        $this->alarmDb($model, 'alarm_message_2');
        $this->alarmDb($model, 'alarm_message_3');
        $this->alarmDb($model, 'alarm_message_4');
        
        $score = $this->createScoreDaily($model);
        
        
        if($score && $model->IHM_ST_Moinho_status > 0) {
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

        if($score && $model->IHM_ST_Moinho_status == 0 && $model->isAlarmOn()) {
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

        if($score && $model->IHM_ST_Moinho_status == 0 && ! $model->isAlarmOn()) {
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

    public function isAlarmOn(): bool
    {
        return (bool) $this->alarm1 || $this->alarm2 || $this->alarm3 || $this->alarm4;
    }

    protected array $desc_alarm_message_1 = [
        'Emergency Button Triggered',
        'mill overload',
        'mill inverter defect',
        'missing feedback mill on',
        'maximum temperature in the liquid seal',
        'minimum pressure on the liquid seal',
        'minimum level in the liquid seal',
        'mill on without feed pump',
        'maximum mill inlet pressure',
        'maximum mill outlet pressure',
        'maximum mill outlet temperature',
        'current too high in the mill',
        'seal pump overload',
        'missing feedback from seal pump on',
        'minimum flow in the seal pump',
        'supply pump overload'
    ];

    protected array $desc_alarm_message_2 = [
        'feed pump powered inverter feedback is missing',
        'feedback from the power pump on is missing',
        'discharge pump overload',
        'discharge pump powered inverter feedback is missing',
        'feedback from flush pump on is missing',
        'CV100 valve failure',
        'CV101 valve failure',
        'CV103 valve failure',
        'CV104 valve failure',
        'CV105 valve failure',
        'CV106 valve failure',
        'fault in analog PT100',
        'fault in analog TT100',
        'fault in analog PT200',
        'fault in analog TT200',
        'failure in analog PT300'
    ];    

    protected array $desc_alarm_message_3 = [
        'clogging in the heat exchanger',
        'maximum temperature in the heat exchanger',
        'CV107 valve failure',
    ];

    protected array $desc_alarm_message_4 = [
        'Maximum pressure at the mill inlet',
        'Maximum pressure at the mill outlet',
        'Maximum temperature at the mill outlet',
        'Maximum temperature in the heat exchanger',
        'clogging in the heat exchanger',
        'high current in the mill',
    ];
}
