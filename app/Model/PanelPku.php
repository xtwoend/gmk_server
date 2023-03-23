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
class PanelPku extends Model
{
    use DeviceTrait;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'panel_pku';
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
        'ai_module_4' => 'array'
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
        ];
    }
}
