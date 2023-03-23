<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Model\Events\Creating;

/**
 */
class Received extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'received';
    protected string $primaryKey = 'id';
    protected string $keyType = 'string';
    public bool $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'device_id', 'terminal_time', 'topic', 'data', 'sync_at'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'terminal_time' => 'datetime',
    ];

    /**
     * not sync scope
     */
    public function scopeSync($query)
    {
        return $query->whereNull('sync_at');
    }

    public function creating(Creating $event)
    {
        $this->id = \Ramsey\Uuid\Uuid::uuid4()->toString();
    }

    public static function table($device)
    {
        $model = new self;
        $tableName = $model->getTable() . "_{$device}";
        
        if(! Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->timestamp('terminal_time');
                $table->unsignedBigInteger('device_id')->nullable();
                $table->string('topic')->nullable();
                $table->text('data')->nullable();
                $table->timestamp('sync_at')->nullable();
                
                $table->unique(['terminal_time', 'device_id']);

                $table->timestamps();
            });
        }
        
        return $model->setTable($tableName);
    }
}
