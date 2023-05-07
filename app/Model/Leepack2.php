<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use App\Model\Alarm;
use Hyperf\DbConnection\Db;
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Model\Events\Created;
use Hyperf\Database\Model\Events\Creating;

/**
 */
class Leepack2 extends Model
{
    use DeviceTrait, ResourceTrait;

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
        'alarm_leepack2' => 'array'
    ];

    /**
     * timestamp attribute from device iot gateway
     */
    public string $ts = 'ts';

    public array $desc_alarm_leepack2 = [
        'Air pressure drop',
        'Abnormal temperature',
        'Data limit over',
        'Gripper limit error',
        'Brake power fault',
        'Bag open check miss',
        'Target bag completion',
        'Grease end',
        'Filling time over',
        'Hopper low level',
        'Inverter alarm',
        'Servo drive alarm',
        'Bag loading check miss',
        'Safety guard open',
        'Fill nozzle open/close miss',
        'Please,enter password',
        'Drain mode',
        'Bag open width limit over',
        'Bag low level alarm',
        'PLC APM Module alarm',
        'Heat transfer oil low level',
        'Product temp. low alarm',
        'Recipe change confirmation',
        'Printer alarm',
        '3-phase motor overload',
        'Feeding motor IVT alarm',
        'Drain mode reset'
    ];

    /**
     * export table headers
     */
    protected $headersExport = [
        'terminal_time' => 'Timestamp',
    ];

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
                $table->text('alarm_leepack2')->nullable();
                $table->boolean('mc_run')->nullable();
                $table->boolean('mc_stop')->nullable();
                $table->integer('sv_speed_bpm')->nullable();
                $table->integer('pv_speed_bpm')->nullable();
                $table->integer('sv_bag')->nullable();
                $table->integer('pv_bag')->nullable();
                $table->integer('sv_filling_speed_rpm')->nullable();
                $table->integer('pv_filling_speed_rpm')->nullable();
                $table->integer('level_hopper')->nullable();
                $table->timestamps();
            });
        }

        return $model->setTable($tableName);
    }

    public function format(array $data)
    {
        return [
            'alarm_leepack2' => $this->map($data['alarm_leepack2']),
            'mc_run' => $data['mc_run'],
            'mc_stop' => $data['mc_stop'],
            'sv_speed_bpm' => $data['sv_speed_bpm'],
            'pv_speed_bpm' => $data['pv_speed_bpm'],
            'sv_bag' => $data['sv_bag'],
            'pv_bag' => $data['pv_bag'],
            'sv_filling_speed_rpm' => $data['sv_filling_speed_rpm'],
            'pv_filling_speed_rpm' => $data['pv_filling_speed_rpm'],
            'level_hopper' => $data['level_hopper'],
        ];
    }

    /**
     * created
     */
    public function created(Created $event)
    {
        $model = $event->getModel();
       
        $this->alarmDb($model, 'alarm_leepack2');
    }
    

    public function export($device, $request)
    {
        $classModel = self::class;

        $from = $request->input('from', Carbon::now()->format('Y-m-d H:i:s'));
        $to = $request->input('to', Carbon::now()->format('Y-m-d H:i:s'));
        $interval = $request->input('interval', 60);

        $from = Carbon::parse($from)->timezone('Asia/Jakarta');
        $to = Carbon::parse($to)->timezone('Asia/Jakarta');

        $query = [];
        $fromClone = clone $from;
        $toClone = clone $to;

        $fromDiff = $from->format("Y-m-01 00:00:00");
        $toDiff = $to->format("Y-m-01 00:00:00");


        $count = Carbon::parse($fromDiff)->diffInMonths(Carbon::parse($toDiff));
        
        for($i=0; $i <= $count; $i++) {
            $tableName = (new $classModel)->table($device, $from)->getTable();
            $query[] = "
            (select 
                (UNIX_TIMESTAMP(ct.terminal_time) * 1000) as unix_time, ct.*
                from {$tableName} as `ct` 
                    inner join 
                    (
                        SELECT MIN(terminal_time) as times, FLOOR(UNIX_TIMESTAMP(terminal_time)/{$interval}) AS timekey 
                        FROM {$tableName} 
                        WHERE terminal_time BETWEEN '{$fromClone->format('Y-m-d H:i:s')}' AND '{$toClone->format('Y-m-d H:i:s')}' 
                        GROUP BY timekey
                    ) ctx 
                    on `ct`.`terminal_time` = `ctx`.`times`)
            ";
            $from = $from->addMonth();
        }
        $querySql = implode(' UNION ', $query);
        
        $rows = Db::table(Db::raw("($querySql order by terminal_time asc) as datalog"));

        if($request->has('sortBy')) {
            $column = $request->input('sortBy');
            $dir = $request->input('sortType');
            $rows = $rows->orderBy($column, $dir);
        }

        $rows = $rows->get()->toArray();

        $headers =[
            'terminal_time' => 'Timestamp',
            'mc_run' => 'mc_run',
            'mc_stop' => 'mc_stop',
            'sv_speed_bpm' => 'sv_speed_bpm',
            'pv_speed_bpm' => 'pv_speed_bpm',
            'sv_bag' => 'sv_bag',
            'pv_bag' => 'pv_bag',
            'sv_filling_speed_rpm' => 'sv_filling_speed_rpm',
            'pv_filling_speed_rpm' => 'pv_filling_speed_rpm',
            'level_hopper' => 'level_hopper',
        ];
    
        return export($headers, $rows, 'report_leepack2_'.$from.'-'.$to);
    }
}
