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
class Leepack3 extends Model
{
    use DeviceTrait, ResourceTrait, ScoreTrait;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'leepack';
    protected string $primaryKey = 'id';
    protected string $keyType = 'string';
    public bool $incrementing = false;

    protected array $guarded = ['id'];

    /**
     * export table headers
     */
    protected $headersExport = [
        'terminal_time' => 'Timestamp',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'terminal_time' => 'datetime',
        'alarm_leepack3' => 'array'
    ];

    /**
     * timestamp attribute from device iot gateway
     */
    public string $ts = 'ts';

    public array $desc_alarm_leepack3 = [
        'Hopper high,low level',
        'Data range limit over',
        'Target bag completed',
        'Air pressure drop',
        'Abnormal temperature',
        'Heat transfer oil low level',
        'Product temperature low',
        'Not use',
        'Not use',
        'Not use',
        'Not use',
        '3-Phase motor overload',
        'Drain mode off',
        'Drain mode',
        'Servo jog driving',
        'Please,enter password',
        'Not use',
        'Not use',
        'Not use',
        'Servo drive alarm',
        'Not use',
        'Not use',
        'Nozzle open error',
        'Nozzle close error',
        'Not use',
        'APM Module error',
        'Not use',
        'Not use',
        'Not use',
        'Not use',
        'Not use',
        'Not use'
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
                $table->text('alarm_leepack3')->nullable();
                $table->boolean('mc_run')->nullable();
                $table->boolean('mc_stop')->nullable();
                $table->integer('sv_bag')->nullable();
                $table->integer('pv_bag')->nullable();
                $table->integer('sv_filling_speed_rpm')->nullable();
                $table->integer('sv_filling_pulse')->nullable();
                $table->integer('level_hopper')->nullable();
                $table->timestamps();
            });
        }

        return $model->setTable($tableName);
    }

    public function format(array $data)
    {
        return [
            'alarm_leepack3' => $this->map($data['alarm_leepack3']),
            'mc_run' => $data['mc_run'],
            'mc_stop' => $data['mc_stop'],
            'sv_bag' => $data['sv_bag'],
            'pv_bag' => $data['pv_bag'],
            'sv_filling_speed_rpm' => $data['sv_filling_speed_rpm'],
            'sv_filling_pulse' => $data['sv_filling_pulse'],
            'level_hopper' => $data['level_hopper'],
        ];
    }

    /**
     * created
     */
    public function created(Created $event)
    {
        $model = $event->getModel();
       
        $this->alarmDb($model, 'alarm_leepack3');
        $this->createScoreDaily($model);
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
            'sv_bag' => 'sv_bag',
            'pv_bag' => 'pv_bag',
            'sv_filling_speed_rpm' => 'sv_filling_speed_rpm',
            'sv_filling_pulse' => 'sv_filling_pulse',
            'level_hopper' => 'level_hopper',
        ];
    
        return export($headers, $rows, 'report_leepack3_'.$from.'-'.$to);
    }
}
