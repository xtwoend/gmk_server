<?php

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Model\Events\Creating;

class Alarm extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'alarm';
    protected string $primaryKey = 'id';
    protected string $keyType = 'string';
    public bool $incrementing = false;
    protected array $appends = ['duration'];

    protected array $guarded = ['id'];

    protected array $casts = [
        'started_at' => 'datetime',
        'finised_at' => 'datetime'
    ];

    /**
     * creating
     */
    public function creating(Creating $event)
    {
        $this->id = \Ramsey\Uuid\Uuid::uuid4()->toString();
    }

    /**
     * get duration attribute
     */
    public function getDurationAttribute()
    {
        return (isset($this->finished_at) && isset($this->started_at)) ? Carbon::parse($this->finished_at)->diffInSeconds(Carbon::parse($this->started_at)): 0;
    }
    
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
                $table->string('property')->nullable();
                $table->tinyInteger('property_index')->default(0);
                $table->string('message')->nullable();
                $table->tinyInteger('status')->default(1); // 0: Close, 1: Open
                $table->timestamp('started_at')->nullable();
                $table->timestamp('finished_at')->nullable();
                $table->timestamps();
            });
        }

        return $model->setTable($tableName);
    }
}