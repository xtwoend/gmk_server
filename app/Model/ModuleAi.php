<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Database\Schema\Blueprint;

/**
 */
class ModuleAi extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'modul_ai';

    /**
     * The attributes that are mass assignable.
     */
    protected array $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];


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
                
                $table->float('level_tank1')->default(0);
                $table->float('temp_piping_tank1')->default(0);
                $table->float('temp_tank1')->default(0);

                $table->float('level_tank2')->default(0);
                $table->float('temp_piping_tank2')->default(0);
                $table->float('temp_tank2')->default(0);

                $table->float('level_tank3')->default(0);
                $table->float('temp_piping_tank3')->default(0);
                $table->float('temp_tank3')->default(0);

                $table->float('level_tank4')->default(0);
                $table->float('temp_piping_tank4')->default(0);
                $table->float('temp_tank4')->default(0);

                $table->float('level_tank5')->default(0);
                $table->float('temp_piping_tank5')->default(0);
                $table->float('temp_tank5')->default(0);

                $table->float('level_tank6')->default(0);
                $table->float('temp_piping_tank6')->default(0);
                $table->float('temp_tank6')->default(0);

                $table->float('level_tank7')->default(0);
                $table->float('temp_piping_tank7')->default(0);
                $table->float('temp_tank7')->default(0);

                $table->float('level_tank8')->default(0);
                $table->float('temp_piping_tank8')->default(0);
                $table->float('temp_tank8')->default(0);

                $table->float('level_tank9')->default(0);
                $table->float('temp_piping_tank9')->default(0);
                $table->float('temp_tank9')->default(0);

                $table->float('level_tank10')->default(0);
                $table->float('temp_piping_tank10')->default(0);
                $table->float('temp_tank10')->default(0);


                $table->float('level_tank11')->default(0);
                $table->float('temp_piping_tank11')->default(0);
                $table->float('temp_tank11')->default(0);

                $table->float('level_tank12')->default(0);
                $table->float('temp_piping_tank12')->default(0);
                $table->float('temp_tank12')->default(0);

                $table->float('level_tank13')->default(0);
                $table->float('temp_piping_tank13')->default(0);
                $table->float('temp_tank13')->default(0);

                $table->float('level_tank14')->default(0);
                $table->float('temp_piping_tank14')->default(0);
                $table->float('temp_tank14')->default(0);

                $table->float('level_tank15')->default(0);
                $table->float('temp_piping_tank15')->default(0);
                $table->float('temp_tank15')->default(0);

                $table->float('level_tank16')->default(0);
                $table->float('temp_piping_tank16')->default(0);
                $table->float('temp_tank16')->default(0);

                $table->float('level_tank17')->default(0);
                $table->float('temp_piping_tank17')->default(0);
                $table->float('temp_tank17')->default(0);

                $table->float('level_tank18')->default(0);
                $table->float('temp_piping_tank18')->default(0);
                $table->float('temp_tank18')->default(0);

                $table->float('level_tank19')->default(0);
                $table->float('temp_piping_tank19')->default(0);
                $table->float('temp_tank19')->default(0);

                $table->float('level_tank20')->default(0);
                $table->float('temp_piping_tank20')->default(0);
                $table->float('temp_tank20')->default(0);
                

                $table->float('level_tank21')->default(0);
                $table->float('temp_piping_tank21')->default(0);
                $table->float('temp_tank21')->default(0);

                $table->float('level_tank22')->default(0);
                $table->float('temp_piping_tank22')->default(0);
                $table->float('temp_tank22')->default(0);

                $table->float('level_tank23')->default(0);
                $table->float('temp_piping_tank23')->default(0);
                $table->float('temp_tank23')->default(0);

                $table->float('level_tank24')->default(0);
                $table->float('temp_piping_tank24')->default(0);
                $table->float('temp_tank24')->default(0);

                $table->float('level_tank25')->default(0);
                $table->float('temp_piping_tank25')->default(0);
                $table->float('temp_tank25')->default(0);

                $table->float('level_tank26')->default(0);
                $table->float('temp_piping_tank26')->default(0);
                $table->float('temp_tank26')->default(0);

                $table->float('level_tank27')->default(0);
                $table->float('temp_piping_tank27')->default(0);
                $table->float('temp_tank27')->default(0);

                $table->float('level_tank28')->default(0);
                $table->float('temp_piping_tank28')->default(0);
                $table->float('temp_tank28')->default(0);

                $table->float('level_tank29')->default(0);
                $table->float('temp_piping_tank29')->default(0);
                $table->float('temp_tank29')->default(0);

                $table->float('level_tank30')->default(0);
                $table->float('temp_piping_tank30')->default(0);
                $table->float('temp_tank30')->default(0);


                $table->float('level_tank31')->default(0);
                $table->float('temp_piping_tank31')->default(0);
                $table->float('temp_tank31')->default(0);

                $table->float('level_tank32')->default(0);
                $table->float('temp_piping_tank32')->default(0);
                $table->float('temp_tank32')->default(0);

                $table->float('level_tank33')->default(0);
                $table->float('temp_piping_tank33')->default(0);
                $table->float('temp_tank33')->default(0);

                $table->float('level_tank34')->default(0);
                $table->float('temp_piping_tank34')->default(0);
                $table->float('temp_tank34')->default(0);

                $table->float('level_tank35')->default(0);
                $table->float('temp_piping_tank35')->default(0);
                $table->float('temp_tank35')->default(0);

                $table->float('level_tank36')->default(0);
                $table->float('temp_piping_tank36')->default(0);
                $table->float('temp_tank36')->default(0);

                $table->float('level_tank37')->default(0);
                $table->float('temp_piping_tank37')->default(0);
                $table->float('temp_tank37')->default(0);

                $table->float('level_tank38')->default(0);
                $table->float('temp_piping_tank38')->default(0);
                $table->float('temp_tank38')->default(0);

                $table->float('level_tank39')->default(0);
                $table->float('temp_piping_tank39')->default(0);
                $table->float('temp_tank39')->default(0);

                $table->float('level_tank40')->default(0);
                $table->float('temp_piping_tank40')->default(0);
                $table->float('temp_tank40')->default(0);

                $table->timestamps();
            });
        }

        return $model->setTable($tableName);
    }
}
