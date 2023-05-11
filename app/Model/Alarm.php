<?php

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Model\Events\Created;
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

    // protected $fillable = [
    //     'device_id', ''
    // ];


    /**
     * creating
     */
    public function creating(Creating $event)
    {
        $this->id = \Ramsey\Uuid\Uuid::uuid4()->toString();
    }

    public function created(Created $event)
    {
        $model = $event->getModel();

        $device = $model->device;
        $topics = [
            1 => 'data/gmk/k/leepack1/alarm',
            2 => 'data/gmk/k/leepack2/alarm',
            3 => 'data/gmk/k/leepack3/alarm',
            7 => 'data/gmk/k/lme1/alarm',
            8 => 'data/gmk/k/lme2/alarm',
            9 => 'data/gmk/k/lme3/alarm'
        ];

        if($device) {
            $topic = $topics[$device->id] ?? 'data/gmk/k/general';
            $listen = 'mqtt_1';
            $config = config('mqtt.servers')[$listen];
            $clientId = \Hyperf\Utils\Str::random(10);
            $event = $this->event;
            $mqtt = new \PhpMqtt\Client\MqttClient($config['host'], $config['port'], $clientId);
            
            $config = (new \PhpMqtt\Client\ConnectionSettings)
                ->setUsername($config['username'])
                ->setPassword($config['password']);

            $mqtt->connect($config, true);
            $mqtt->publish($topic, json_encode($model->toArray()), 0);
            $mqtt->disconnect();
        }
    }

    /**
     * get duration attribute
     */
    public function getDurationAttribute()
    {
        $duration = (isset($this->finished_at) && isset($this->started_at)) ? Carbon::parse($this->finished_at)->diffInSeconds(Carbon::parse($this->started_at)): 0;
        if($duration > 0) {
            return gmdate("H:i:s", $duration);
        }
        return 0;
    }

    /**
     * relation to device
     */
    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
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