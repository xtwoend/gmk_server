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
class PanelPkp extends Model
{
    use DeviceTrait;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'panel_pkp';
    protected string $primaryKey = 'id';
    protected string $keyType = 'string';
    public bool $incrementing = false;

    protected array $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'terminal_time' => 'datetime',
        'di_module_1' => 'array',
        'di_module_2' => 'array',
        'di_module_3' => 'array',
        'ai_module_1' => 'array',
        'ai_module_2' => 'array',
        'ai_module_3' => 'array',
        'ai_module_4' => 'array',
        'ai_module_5' => 'array',
        'ai_module_6' => 'array',
        'ai_module_7' => 'array',
        'ai_module_8' => 'array',
        'ai_module_9' => 'array',
        'ai_module_10' => 'array',
        'ai_module_11' => 'array',
        'ai_module_12' => 'array'
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
                $table->text('di_module_1')->nullable();
                $table->text('di_module_2')->nullable();
                $table->text('di_module_3')->nullable();
                $table->text('ai_module_1')->nullable();
                $table->text('ai_module_2')->nullable();
                $table->text('ai_module_3')->nullable();
                $table->text('ai_module_4')->nullable();
                $table->text('ai_module_5')->nullable();
                $table->text('ai_module_6')->nullable();
                $table->text('ai_module_7')->nullable();
                $table->text('ai_module_8')->nullable();
                $table->text('ai_module_9')->nullable();
                $table->text('ai_module_10')->nullable();
                $table->text('ai_module_11')->nullable();
                $table->text('ai_module_12')->nullable();
                $table->text('ai_module_13')->nullable();
                $table->text('ai_module_14')->nullable();
                $table->text('ai_module_15')->nullable();
                $table->text('ai_refiner')->nullable();
                $table->text('di_refiner')->nullable();
                $table->timestamps();
            });
        }

        return $model->setTable($tableName);
    }

    public function format(array $data)
    {
        return [
            'di_module_1' => $this->map($data['di_module_1']),
            'di_module_2' => $this->map($data['di_module_2']),
            'di_module_3' => $this->map($data['di_module_3']),
            'ai_module_1' => $this->map($data['ai_module_1']),
            'ai_module_2' => $this->map($data['ai_module_2']),
            'ai_module_3' => $this->map($data['ai_module_3']),
            'ai_module_4' => $this->map($data['ai_module_4']),
            'ai_module_5' => $this->map($data['ai_module_5']),
            'ai_module_6' => $this->map($data['ai_module_6']),
            'ai_module_7' => $this->map($data['ai_module_7']),
            'ai_module_8' => $this->map($data['ai_module_8']),
            'ai_module_9' => $this->map($data['ai_module_9']),
            'ai_module_10' => $this->map($data['ai_module_10']),
            'ai_module_11' => $this->map($data['ai_module_11']),
            'ai_module_12' => $this->map($data['ai_module_12']),
            'ai_module_13' => $this->map($data['ai_module_13']),
            'ai_module_14' => $this->map($data['ai_module_14']),
            'ai_module_15' => $this->map($data['ai_module_15']),
            'ai_refiner' => $this->map($data['ai_refiner']),
            'di_refiner' => $this->map($data['di_refiner']),
        ];
    }
}
