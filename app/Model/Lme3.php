<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Model\Events\Created;
use Hyperf\Database\Model\Events\Creating;

/**
 */
class Lme3 extends Model
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
        'alarm_message_1' => 'array',
        'alarm_message_2' => 'array',
        'alarm_message_3' => 'array',
        'alarm_message_4' => 'array'
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
                $table->float('data1', 15, 10)->nullable();
                $table->float('data2', 15, 10)->nullable();
                $table->float('data3', 15, 10)->nullable();
                $table->float('data4', 15, 10)->nullable();
                $table->float('data5', 15, 10)->nullable();
                $table->float('data6', 15, 10)->nullable();
                $table->float('data7', 15, 10)->nullable();
                $table->float('data8', 15, 10)->nullable();
                $table->float('data9', 15, 10)->nullable();
                $table->float('data10', 15, 10)->nullable();
                $table->tinyInteger('IHM_ST_Moinho_status')->nullable();
                $table->timestamps();
            });
        }

        return $model->setTable($tableName);
    }

    public function format(array $data)
    {
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
            'IHM_ST_Moinho_status' => $data['IHM_ST_Moinho_status']
        ];
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
