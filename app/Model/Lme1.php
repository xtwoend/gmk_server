<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Model\Events\Creating;

/**
 */
class Lme1 extends Model
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
                $table->tinyInteger('TK_ST_TksTransfPump_Status')->nullable();
                $table->tinyInteger('LME_ST_MillMotor_Status')->nullable();
                $table->tinyInteger('LME_ST_FeedingPump_Status')->nullable();
                $table->float('HMI_CE_ST_ConcProdTemp', 15, 10)->nullable();
                $table->float('HMI_TK_ST_CoolTkTemp', 15, 10)->nullable();
                $table->float('HMI_TK_ST_HoldTkTemp', 15, 10)->nullable();
                $table->float('HMI_LME_ST_FeedPSpeed', 15, 10)->nullable();
                $table->float('HMI_CUM_ST_MillSpeed', 15, 10)->nullable();
                $table->float('HMI_LME_ST_MillCurrent', 15, 10)->nullable();
                $table->float('HMI_LME_ST_InProdPres', 15, 10)->nullable();
                $table->float('HMI_LME_ST_OutProdTemp', 15, 10)->nullable();
                $table->timestamps();
            });
        }

        return $model->setTable($tableName);
    }

    public function format(array $data)
    {
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
            'HMI_LME_ST_OutProdTemp' => $data['HMI_LME_ST_OutProdTemp']
        ];
    }
}
