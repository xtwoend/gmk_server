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
class Leepack3 extends Model
{
    use DeviceTrait;

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
        'alarm_leepack3' => 'array'
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
                $table->text('alarm_leepack3')->nullable();
                $table->boolean('mc_run')->nullable();
                $table->boolean('mc_stop')->nullable();
                $table->integer('sv_bag')->nullable();
                $table->integer('pv_bag')->nullable();
                $table->integer('sv_filling_speed_rpm')->nullable();
                $table->integer('sv_filling_pulse')->nullable();
                $table->integer('level_hopper')->nullable();
                $table->timestamps();
            });
        }

        return $model->setTable($tableName);
    }

    public function format(array $data)
    {
        $alarm = array_map(function($value){
            return ($value)? 1: 0;
        }, $data['alarm_leepack3']?:[]);

        return [
            'alarm_leepack3' => $alarm,
            'mc_run' => $data['mc_run'],
            'mc_stop' => $data['mc_stop'],
            'sv_bag' => $data['sv_bag'],
            'pv_bag' => $data['pv_bag'],
            'sv_filling_speed_rpm' => $data['sv_filling_speed_rpm'],
            'sv_filling_pulse' => $data['sv_filling_pulse'],
            'level_hopper' => $data['level_hopper'],
        ];
    }
}
