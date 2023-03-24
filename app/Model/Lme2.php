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
class Lme2 extends Model
{
    use DeviceTrait;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'lme';
    protected string $primaryKey = 'id';
    protected string $keyType = 'string';
    public bool $incrementing = false;

    protected array $guarded = ['id'];

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
            'HMI_LME_ST_MillSpeed' => $data['HMI_LME_ST_MillSpeed']
        ];
    }
}
