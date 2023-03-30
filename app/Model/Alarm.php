<?php

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

class Alarm extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'alarm';
    protected string $primaryKey = 'id';
    protected string $keyType = 'string';
    public bool $incrementing = false;

    protected array $guarded = ['id'];

    /**
     * create or choice table
     */
    public static function table($deviceId)
    {
        $model = new self;
        $tableName = $model->getTable() . "_{$deviceId}";
       
        if(! Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->unsignedBigInteger('device_id')->index();
                $table->string('message')->nullable();
                $table->tinyInteger('status')->default(1); // 0: Close, 1: Open
                $table->timestamp('started_at')->nullable();
                $table->tiemstamp('finished_at')->nullable();
                $table->timestamps();
            });
        }

        return $model->setTable($tableName);
    }

    
}