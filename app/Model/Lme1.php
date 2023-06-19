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
use Hyperf\Database\Model\Events\Creating;

/**
 */
class Lme1 extends Model
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

    public string $statusRun = 'LME_ST_MillMotor_Status';
    public string $ppm_pv = 'HMI_CUM_ST_MillSpeed';
    public string $ppm_sv = 'HMI_LME_SP_MillPAutSpd';
    public string $ppm2_pv = 'HMI_LME_ST_FeedPSpeed';
    public string $ppm2_sv = 'HMI_LME_SP_FeedPManSpd';

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
        'tk_alarm_message_1' => 'array',
        'tk_alarm_message_2' => 'array',
        'tk_warning_message' => 'array',
        'cum_alarm_message_1' => 'array',
        'cum_alarm_message_2' => 'array',
        'lme_alarm_message_1' => 'array',
        'lme_alarm_message_2' => 'array',
        'ce_alarm_message_1' => 'array',
        'ce_alarm_message_2' => 'array'
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
                $table->text('tk_alarm_message_1')->nullable();
                $table->text('tk_alarm_message_2')->nullable();
                $table->text('tk_warning_message')->nullable();
                $table->text('cum_alarm_message_1')->nullable();
                $table->text('cum_alarm_message_2')->nullable();
                $table->text('lme_alarm_message_1')->nullable();
                $table->text('lme_alarm_message_2')->nullable();
                $table->text('ce_alarm_message_1')->nullable();
                $table->text('ce_alarm_message_2')->nullable();
                $table->tinyInteger('TK_ST_TksTransfPump_Status')->nullable(); // untuk acuan downtime loss jika nilai 0 mulai menghitung durasi
                $table->tinyInteger('LME_ST_MillMotor_Status')->nullable(); // untuk acuan downtime loss jika nilai 0 mulai menghitung durasi
                $table->tinyInteger('LME_ST_FeedingPump_Status')->nullable();
                $table->float('HMI_CE_ST_ConcProdTemp', 15, 10)->nullable();
                $table->float('HMI_TK_ST_CoolTkTemp', 15, 10)->nullable();
                $table->float('HMI_TK_ST_HoldTkTemp', 15, 10)->nullable();
                $table->float('HMI_LME_ST_FeedPSpeed', 15, 10)->nullable();
                $table->float('HMI_CUM_ST_MillSpeed', 15, 10)->nullable();
                $table->float('HMI_LME_ST_MillCurrent', 15, 10)->nullable();
                $table->float('HMI_LME_ST_InProdPres', 15, 10)->nullable();
                $table->float('HMI_LME_ST_OutProdTemp', 15, 10)->nullable();
                $table->tinyInteger('HMI_TK_ST_CoolTkAgit_status')->nullable();
                $table->tinyInteger('HMI_TK_ST_HoldTkAgit_status')->nullable();
                $table->tinyInteger('HMI_CE_ST_Conche_status')->nullable();
                $table->tinyInteger('HMI_LME_ST_RecirPump_status')->nullable();
                $table->float('HMI_LME_SP_MillPAutSpd', 15, 10)->nullable();
                $table->float('performance_per_minutes', 15, 10)->nullable();
                // alarm to be breakdown
                $table->tinyInteger('tank_alarm1')->default(0);
                $table->tinyInteger('tank_alarm2')->default(0);
                $table->tinyInteger('lme_alarm1')->default(0);
                $table->tinyInteger('lme_alarm2')->default(0);
                $table->tinyInteger('stk_alarm')->default(0);
                $table->tinyInteger('cum_alarm1')->default(0);
                $table->tinyInteger('cum_alarm2')->default(0);
                $table->tinyInteger('ce_alarm1')->default(0);
                $table->tinyInteger('ce_alarm2')->default(0);

                // addeded
                $table->float('HMI_LME_SP_FeedPManSpd', 15, 10)->nullable();
                $table->float('temp_chilled_water_in', 15, 10)->nullable();
                $table->float('temp_chilled_water_out', 15, 10)->nullable();

                $table->float('performance_per_minutes_2', 15, 10)->nullable();

                $table->timestamps();
            });
        }

        return $model->setTable($tableName);
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
            'HMI_CE_ST_ConcProdTemp' => 'Conche Product Temperatur(°C)',
            'HMI_TK_ST_CoolTkTemp' => 'Product CT Temperatur(°C)',
            'HMI_TK_ST_HoldTkTemp' => 'Product HT Temperatur(°C)',
            'HMI_LME_ST_FeedPSpeed' => 'LME500 FeedPump Speed (rpm)',
            'HMI_CUM_ST_MillSpeed' => 'LME 500 Mill Speed (rpm)',
            'HMI_LME_ST_MillCurrent' => 'LME500 Mill Current (A)',
            'HMI_LME_ST_InProdPres' => 'LME500 Product Pressure (bar)',
            'HMI_LME_ST_OutProdTemp' => 'LME500 Product Temperature (°C)',
            'LME_ST_MillMotor_Status' => 'Mill Motor Status',
            'TK_ST_TksTransfPump_Status' => 'Transfer Pump Status',
            'LME_ST_FeedingPump_Status' => 'Feed Pump Status',                
            'HMI_TK_ST_CoolTkAgit_status' => 'CT Agitator Status',
            'HMI_TK_ST_HoldTkAgit_status' => 'HT Agitator Status',
            'HMI_CE_ST_Conche_status' => 'CE Motor Status',
            'HMI_LME_ST_RecirPump_status' => 'LME Recirc Pump Status'
        ];
    
        return export($headers, $rows, 'report_lme1_'.$from.'-'.$to);
    }
    
    /**
     * created
     */
    public function created(Created $event)
    {
        $model = $event->getModel();
        
        $this->alarmDb($model, 'tk_alarm_message_1');
        $this->alarmDb($model, 'tk_alarm_message_2');
        $this->alarmDb($model, 'tk_warning_message');
        $this->alarmDb($model, 'cum_alarm_message_1');
        $this->alarmDb($model, 'cum_alarm_message_2');
        $this->alarmDb($model, 'lme_alarm_message_1');
        $this->alarmDb($model, 'lme_alarm_message_2');
        $this->alarmDb($model, 'ce_alarm_message_1');
        $this->alarmDb($model, 'ce_alarm_message_2');
        
        $score = $this->createScoreDaily($model);
        
        if($score && $model->LME_ST_MillMotor_Status > 0) {
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

        if($score && $model->LME_ST_MillMotor_Status == 0 && $model->isAlarmOn()) {
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
                'ended_at' => Carbon::now(),
            ]);
        }
        // LME_ST_MillMotor_Status
        if($score && $model->LME_ST_MillMotor_Status == 0 && ! $model->isAlarmOn()) {
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
        return (bool) $this->tank_alarm1 || $this->tank_alarm2 || $this->lme_alarm1 || $this->lme_alarm2 || $this->stk_alarm || $this->cum_alarm1 || $this->cum_alarm2 || $this->ce_alarm1 || $this->ce_alarm2;
    }

    public function format(array $data)
    {
        $perfoma = $data['HMI_LME_SP_MillPAutSpd'] > 0 ? ($data['HMI_CUM_ST_MillSpeed'] / $data['HMI_LME_SP_MillPAutSpd']) : 0;
        $perfoma2 = $data['HMI_LME_SP_FeedPManSpd'] > 0? ($data['HMI_LME_ST_FeedPSpeed'] / $data['HMI_LME_SP_FeedPManSpd']) : 0;

        return [
            'tk_alarm_message_1' => $this->map($data['tk_alarm_message_1']),
            'tk_alarm_message_2' => $this->map($data['tk_alarm_message_2']),
            'tk_warning_message' => $this->map($data['tk_warning_message']),
            'cum_alarm_message_1' => $this->map($data['cum_alarm_message_1']),
            'cum_alarm_message_2' => $this->map($data['cum_alarm_message_2']),
            'lme_alarm_message_1' => $this->map($data['lme_alarm_message_1']),
            'lme_alarm_message_2' => $this->map($data['lme_alarm_message_2']),
            'ce_alarm_message_1' => $this->map($data['ce_alarm_message_1']),
            'ce_alarm_message_2' => $this->map($data['ce_alarm_message_2']),
            'TK_ST_TksTransfPump_Status' => $data['TK_ST_TksTransfPump_Status'],
            'LME_ST_MillMotor_Status' => $data['LME_ST_MillMotor_Status'],
            'LME_ST_FeedingPump_Status' => $data['LME_ST_FeedingPump_Status'],
            'HMI_CE_ST_ConcProdTemp' => $data['HMI_CE_ST_ConcProdTemp'],
            'HMI_TK_ST_CoolTkTemp' => $data['HMI_TK_ST_CoolTkTemp'],
            'HMI_TK_ST_HoldTkTemp' => $data['HMI_TK_ST_HoldTkTemp'],
            'HMI_LME_ST_FeedPSpeed' => $data['HMI_LME_ST_FeedPSpeed'],
            'HMI_CUM_ST_MillSpeed' => $data['HMI_CUM_ST_MillSpeed'],
            'HMI_LME_ST_MillCurrent' => $data['HMI_LME_ST_MillCurrent'],
            'HMI_LME_ST_InProdPres' => $data['HMI_LME_ST_InProdPres'],
            'HMI_LME_ST_OutProdTemp' => $data['HMI_LME_ST_OutProdTemp'],

            'HMI_TK_ST_CoolTkAgit_status' => $data['HMI_TK_ST_CoolTkAgit_status'],
            'HMI_TK_ST_HoldTkAgit_status' => $data['HMI_TK_ST_HoldTkAgit_status'],
            'HMI_CE_ST_Conche_status' => $data['HMI_CE_ST_Conche_status'],
            'HMI_LME_ST_RecirPump_status' => $data['HMI_LME_ST_RecirPump_status'],

            'HMI_LME_SP_MillPAutSpd' => $data['HMI_LME_SP_MillPAutSpd'],
            'performance_per_minutes' => $perfoma > 1 ? 1: $perfoma,

            'tank_alarm1' => $data['tank_alarm1'],
            'tank_alarm2' => $data['tank_alarm2'],
            'lme_alarm1' => $data['lme_alarm1'],
            'lme_alarm2' => $data['lme_alarm2'],
            'stk_alarm' => $data['stk_alarm'],
            'cum_alarm1' => $data['cum_alarm1'],
            'cum_alarm2' => $data['cum_alarm2'],
            'ce_alarm1' => $data['ce_alarm1'],
            'ce_alarm2' => $data['ce_alarm2'],

            'HMI_LME_SP_FeedPManSpd' => $data['HMI_LME_SP_FeedPManSpd'],
            'temp_chilled_water_in' => $data['temp_chilled_water_in'],
            'temp_chilled_water_out' => $data['temp_chilled_water_out'],
            'performance_per_minutes_2' => ($perfoma2 > 1) ? 1: $perfoma2,
        ];
    }
    
    protected array $desc_tk_alarm_message_1 = [
        'Cooling Tank Agitator Overload',
        'Cooling Tank Agitator Without On Feedback Signal',
        'Cooling Tank Max Alarm Weight',
        'Hold Tank Agitator Overload',
        'Hold Tank Agitator Without On Feedback Signal',
        'Hold Tank Max Alarm Weight',
        'Tanks Transfer Pump Overload',
        'Tanks Transfer Pump On Feedback Signal',
        'Lecithin Transfer Pump Overload',
        'Lecithin Transfer Pump Without On Feedback Signal',
        'Circuit Breaker Main Supply Frequency Inverter Lecithin Dosage Pump OFF',
        'Lecithin Dosage Pump Inverter Without Main Supply Connection Feedback Signal',
        'Lecithin Dosage Pump Without On Feedback Signal',
        'Cooling Tank Valve CTK_PV1 Fault (Discharge Valve)',
        'Cooling Tank Valve CTK_WV1 Fault (Input Water Valve)',
        'Cooling Tank Valve CTK_WV2 Fault (Output Water Valve)' 
    ];

    protected array $desc_tk_alarm_message_2 = [
        'Cooling Tank Valve CTK_WV3 Fault (Hold Temperature Water Valve)',
        'Hold Tank Valve HTK_PV1 Fault (Discharge Valve)',
        'Hold Tank Valve CTK_WV1 Fault (Input Water Valve)',
        'Hold Tank Valve CTK_WV2 Fault (Output Water Valve)',
        'Hold Tank Valve CTK_WV3 Fault (Hold Temperature Water Valve)',
        'Lecithin Level Sensor Fault',
        'Lecithin Dosage Pump Running Without Product',
        'Lecithin Dosage Pump Running Without Product',
    ];

    protected array $desc_tk_warning_message = [
        'Cooling Tank Max Warning Weight',
        'Hold Tank Max Warning Weight',
    ];

    protected array $desc_cum_alarm_message_1 = [
        'Mill CUM450 Emergency Button Pushed',
        'Mill CUM450 Safety Sensors Screew Feeder Cover',
        'Mill CUM450 Bag Filter Exhaust Fan Overload',
        'Mill CUM450 Bag Filter Exhaust Fan Without Main Supply Connection Feedback Signal',
        'Mill CUM450 Bag Filter Exhaust Fan Without On Feedback Signal',
        'Bag Filter Explosion Security Sensor Fault',
        'Mill CUM450 Low Bearing Pressure',
        'Circuit Breaker Main Supply Frequency Inverter Mill CUM450 OFF',
        'Mill CUM450 Inverter Without Main Supply Connection Feedback Signal',
        'Mill CUM450 Motor Without On Feedback Signal',
        'Mill CUM450 Motor Alarm Current',
        'Rotary Feeder Valve Overload',
        'Rotary Feeder Valve Without On Feedback Signal',
        'Rotary Feeder Valve Without On Feedback Signal',
        'Sugar Exhaust Fan Feeder Funnel Overload',
        'Sugar Exhaust Fan Feeder Funnel Without On Feedback Signal'
    ];

    protected array $desc_cum_alarm_message_2 = [
        'Circuit Breaker Main Supply Frequency Inverter Screew Feeder OFF',
        'Screew Feeder Inverter Without Main Supply Connection Feedback Signal',
        'Screew Feeder Without On Feedback Signal'
    ];

    protected array $desc_lme_alarm_message_1 = [
        'Recirculation Seal Liquid Pump Overload',
        'Recirculation Seal Liquid Pump Without On Feedback Signal',
        'Seal Liquid Flow Control Fault',
        'Mill LME500 Softstarter w ith Alarm',
        'Mill LME500 Softsarter Without Main Supply Connection Feedback Signal',
        'Mill LME 500 Without On Feedback Signal',
        'Mill LME 500 Low Seal Pressure',
        'Mill LME 500 Low Seal Pressure',
        'Mill LME500 Motor Alarm Current',
        'Max Temperature Alarm Mill LME500 Product Output',
        'Max Pressure Alarm Mill LME500 Product Input',
        'Circuit Breaker Main Supply Frequency Inverter LME500 Feeding Pump OFF',
        'LME500 Feeding Pump Inverter Without Main Supply Connection Feedback Signal',
        'LME500 Feeding Pump Without On Feedback Signal',
        'Feeding Pump On Without Starting the Mill LME500',
        'Mill LME500 Water Input Valve LME_WV1 Fault',
    ];

    protected array $desc_lme_alarm_message_2 = [
        'Mill LME500 Water Output Valve LME_WV2 Fault',
        'Mill LME500 Recirculation/Discharge Valve LME_PV1 Fault',
        'Mill LME500 Output Temperature Sensor Fault',
        'Mill LME500 Input Product Pressure Sensor Fault',
    ];

    protected array $desc_ce_alarm_message_1 = [
        'Sugar Tank Safety Cover Open',
        'Cocoa Exhaust Fan Feeder Funnel Overload',
        'Sugar Exhaust Fan Feeder Funnel Without On Feedback Signal',
        'Conche Motor Overload',
        'Conche Softstarter Without Main Supply Connection Feedback Signal',
        'Conche Motor Without On Feedback Signal',
        'Forced Conche Ventilation Overload',
        'Forced Conche Ventilation Without On Feedback Signal',
        'Air Heating Fan Overload',
        'Air Heating Fan Without On Feedback Signal',
        'Air Heating Resistance Fault',
        'Air Heating Resistance Without On Feedback Signal',
        'Conche Air Heating Max Alarm Temperature',
        'CE6000 Pump Discharge Overload',
        'CE6000 Pump Discharge Without On Feedback Signal',
        'Conche Discharge Valve Fault CE_PV1',
        'Conche Fat Feeding Valve Fault CE_PV2',
    ];

    protected array $desc_ce_alarm_message_2 = [
        'Conche Cocoa Feeder Funnel Valve Fault CE_PV3',
        'Conche Input Water Valve Fault CE_WV1',
        'Conche Output Water Valve Fault CE_WV2',
        'Conche Hold Temperature Valve Fault CE_WV3',
        'Conche Air Heating Temperature Sensor Fault',
        'Conche Product Temperature Sensor Fault',
    ];
}
