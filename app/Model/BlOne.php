<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use App\Model\ScoreTrait;
use App\Model\DeviceTrait;
use App\Model\ResourceTrait;
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Database\Schema\Blueprint;

/**
 */
class BlOne extends Model
{
    use DeviceTrait, ResourceTrait, ScoreTrait;
    
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'bl_one';

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

                $table->float('cooling_temperature1', 10, 3)->default(0);
                $table->float('cooling_temperature2', 10, 3)->default(0);
                $table->float('formen_temperature', 10, 3)->default(0);
                $table->float('temperature_heating1_up', 10, 3)->default(0);
                $table->float('tempeature_heating2_up', 10, 3)->default(0);
                $table->float('cooling_tempeature3', 10, 3)->default(0);
                $table->float('cooling_tempeature4', 10, 3)->default(0);
                $table->float('water_temp_forward_run', 10, 3)->default(0);
                $table->float('water_temp_runback', 10, 3)->default(0);

                $table->timestamps();
            });
        }

        return $model->setTable($tableName);
    }

    public function format(array $data)
    {
        return [
            'cooling_temperature1' => (float) $data['cooling_temperature1'] ?: 0,
            'cooling_temperature2' => (float) $data['cooling_temperature2'] ?: 0,
            'formen_temperature' => (float) $data['formen_temperature'] ?: 0,
            'temperature_heating1_up' => (float) $data['temperature_heating1_up'] ?: 0,
            'tempeature_heating2_up' => (float) $data['tempeature_heating2_up'] ?: 0,
            'cooling_tempeature3' => (float) $data['cooling_tempeature3'] ?: 0,
            'cooling_tempeature4' => (float) $data['cooling_tempeature4'] ?: 0,
            'water_temp_forward_run' => (float) $data['water_temp_forward_run'] ?: 0,
            'water_temp_runback' => (float) $data['water_temp_runback'] ?: 0,
        ];
    }
}
