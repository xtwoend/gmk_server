<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use App\Model\Alarm;
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Model\Events\Created;
use Hyperf\Database\Model\Events\Creating;

/**
 */
class Lme2 extends Model
{
    use DeviceTrait, ResourceTrait;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'lme';
    protected string $primaryKey = 'id';
    protected string $keyType = 'string';
    public bool $incrementing = false;

    protected array $guarded = ['id'];

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
        'in_alarm_message_1' => 'array',
        'in_alarm_message_2' => 'array',
        'tk_alarm_message_1' => 'array',
        'tk_alarm_message_2' => 'array',
        'lme_alarm_message_1' => 'array',
        'lme_alarm_message_2' => 'array'
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
                $table->text('in_alarm_message_1')->nullable();
                $table->text('in_alarm_message_2')->nullable();
                $table->text('tk_alarm_message_1')->nullable();
                $table->text('tk_alarm_message_2')->nullable();
                $table->text('lme_alarm_message_1')->nullable();
                $table->text('lme_alarm_message_2')->nullable();
                $table->tinyInteger('HMI_TK_ST_TksTransfPump_Status')->nullable();
                $table->tinyInteger('HMI_LME_ST_MillMotor_Status')->nullable();
                $table->tinyInteger('HMI_LME_ST_FeedingPump_Status')->nullable();
                $table->float('HMI_TK_ST_DispTKTemp', 15, 10)->nullable();
                $table->float('HMI_TK_ST_HoldTkTemp', 15, 10)->nullable();
                $table->float('HMI_LME_ST_FeedPSpeed', 15, 10)->nullable();
                $table->float('HMI_TK_ST_DispSpeed', 15, 10)->nullable();
                $table->float('HMI_LME_ST_MillCurrent', 15, 10)->nullable();
                $table->float('HMI_LME_ST_InProdPres', 15, 10)->nullable();
                $table->float('HMI_LME_ST_OutProdTemp', 15, 10)->nullable();
                $table->float('HMI_LME_ST_MillSpeed', 15, 10)->nullable();
                $table->tinyInteger('HMI_TK_ST_DispTkAgit_status')->nullable();
                $table->tinyInteger('HMI_TK_ST_HoldTkAgit_status')->nullable();
                $table->tinyInteger('HMI_LME_ST_RecirPump_status')->nullable();
                $table->timestamps();
            });
        }

        return $model->setTable($tableName);
    }

    public function format(array $data)
    {
        return [
            'in_alarm_message_1' => $this->map($data['in_alarm_message_1']),
            'in_alarm_message_2' => $this->map($data['in_alarm_message_2']),
            'tk_alarm_message_1' => $this->map($data['tk_alarm_message_1']),
            'tk_alarm_message_2' => $this->map($data['tk_alarm_message_2']),
            'lme_alarm_message_1' => $this->map($data['lme_alarm_message_1']),
            'lme_alarm_message_2' => $this->map($data['lme_alarm_message_2']),
            'HMI_TK_ST_TksTransfPump_Status' => $data['HMI_TK_ST_TksTransfPump_Status'],
            'HMI_LME_ST_MillMotor_Status' => $data['HMI_LME_ST_MillMotor_Status'],
            'HMI_LME_ST_FeedingPump_Status' => $data['HMI_LME_ST_FeedingPump_Status'],
            'HMI_TK_ST_DispTKTemp' => $data['HMI_TK_ST_DispTKTemp'],
            'HMI_TK_ST_HoldTkTemp' => $data['HMI_TK_ST_HoldTkTemp'],
            'HMI_LME_ST_FeedPSpeed' => $data['HMI_LME_ST_FeedPSpeed'],
            'HMI_TK_ST_DispSpeed' => $data['HMI_TK_ST_DispSpeed'],
            'HMI_LME_ST_MillCurrent' => $data['HMI_LME_ST_MillCurrent'],
            'HMI_LME_ST_InProdPres' => $data['HMI_LME_ST_InProdPres'],
            'HMI_LME_ST_OutProdTemp' => $data['HMI_LME_ST_OutProdTemp'],
            'HMI_LME_ST_MillSpeed' => $data['HMI_LME_ST_MillSpeed'],

            'HMI_TK_ST_DispTkAgit_status' => $data['HMI_TK_ST_DispTkAgit_status'],
            'HMI_TK_ST_HoldTkAgit_status' => $data['HMI_TK_ST_HoldTkAgit_status'],
            'HMI_LME_ST_RecirPump_status' => $data['HMI_LME_ST_RecirPump_status'],
        ];
    }

    /**
     * created
     */
    public function created(Created $event)
    {
        $model = $event->getModel();
        
        $this->alarmDb($model, 'in_alarm_message_1');
        $this->alarmDb($model, 'in_alarm_message_2');
        $this->alarmDb($model, 'tk_alarm_message_1');
        $this->alarmDb($model, 'tk_alarm_message_2');
        $this->alarmDb($model, 'lme_alarm_message_1');
        $this->alarmDb($model, 'lme_alarm_message_2');
    }

    protected array $in_alarm_message_1 = [
        'Cocoa Exhaust Fan Feeder Funnel Overload',
        'Sugar Exhaust Fan Feeder Funnel Without On Feedback Signal',
        'Conche Discharge Valve Fault CE_PV1',
        'Conche Fat Feeding Valve Fault CE_PV2',
        'Conche Cocoa Feeder Funnel Valve Fault CE_PV3',
        'Rotary Discharge Valve Overload',
        'Rotary Discharge Valve Without On Feedback Signal',
        'Circuit Breaker Main Supply Frequency Inverter Discharge Screew OFF',
        'Discharge Screew Inverter Without Main Supply Connection Feedback Signal',
        'Discharge Screew Without On Feedback Signal'
    ];

    protected array $tk_alarm_message_1 = [
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
        'Cooling Tank Valve CTK_WV2 Fault (Output Water Valve)',
    ];

    protected array $tk_alarm_message_2 = [
        'Cooling Tank Valve CTK_WV3 Fault (Hold Temperature Water Valve)',
        'Hold Tank Valve HTK_PV1 Fault (Discharge Valve)',
        'Hold Tank Valve CTK_WV1 Fault (Input Water Valve)',
        'Hold Tank Valve CTK_WV2 Fault (Output Water Valve)',
        'Hold Tank Valve CTK_WV3 Fault (Hold Temperature Water Valve)',
        'Lecithin Level Sensor Fault',
        'Lecithin Dosage Pump Running Without Product',
        'Lecithin Dosage Pump Running Without Product',
    ];

    protected array $lme_alarm_message_1 = [
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
        'Mill LME500 Water Input Valve LME_FV4_OP Fault',
    ];

    protected array $lme_alarm_message_2 = [
        'Mill LME500 Water Output Valve LME_FV5_OP Fault',
        'Mill LME500 Water Output Valve LME_FV6_OP Fault',
        'Mill LME500 Recirculation/Discharge Valve LME_PV1 Fault',
        'Mill LME500 Output Temperature Sensor Fault',
        'Mill LME500 Input Product Pressure Sensor Fault',
    ];
}
