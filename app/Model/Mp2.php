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
class Mp2 extends Model
{
    use DeviceTrait, ResourceTrait, ScoreTrait;
    
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'mp_two';

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
}
