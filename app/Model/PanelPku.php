<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\DbConnection\Db;
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Model\Events\Creating;

/**
 */
class PanelPku extends Model
{
    use DeviceTrait, ResourceTrait;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'panel_pku';
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
        $rows = $this->jsonResource($rows);

        $headers =[
            'terminal_time' => 'Timestamp',
            'ai_module_1.0' => 'Pressure Comp Screw 7',
            'ai_module_1.1' => 'Temp Chill WT1',
            'ai_module_1.2' => 'Level Chill WT1',
            'ai_module_1.3' => 'Temp Chill WT2',
            'ai_module_1.4' => 'Level Chill WT2',
            'ai_module_1.5' => 'Temp Hot WT 45',
            'ai_module_1.6' => 'Level Hot WT 45',
            'ai_module_1.7' => 'Temmp Hot WT 60',
            'ai_module_2.0' => 'Level Hot WT 60',
            'ai_module_2.1' => 'Temp Hot WT3',
            'ai_module_2.2' => 'Level Hot WT3',
            'ai_module_2.3' => 'Level Groung Tank',
            'ai_module_2.4' => 'Level Water Tank GK',
            'ai_module_2.5' => 'Temp Tank FAT1',
            'ai_module_2.6' => 'Level Tank FAT1',
            'ai_module_2.7' => 'Temp Tank FAT2',
            'ai_module_3.0' => 'Level Tank FAT2',
            'ai_module_3.1' => 'Temp Tank FAT3',
            'ai_module_3.2' => 'Level Tank FAT3',
            'ai_module_3.3' => 'Temp Tank FAT4',
            'ai_module_3.4' => 'Level Tank FAT4',
            'ai_module_3.5' => 'Temp Tank FAT5',
            'ai_module_3.6' => 'Level Tank FAT5',
            'ai_module_3.7' => 'Temp Tank FAT6',
            'ai_module_4.0' => 'Level Tank FAT6',
            'ai_module_4.1' => 'Temp Tank FAT7',
            'ai_module_4.2' => 'Level Tank FAT7',
            'ai_module_4.3' => 'Temp Melter Tank',
            'ai_module_4.4' => 'Level Melter Tank ',
            'di_module_1.0'  => 'Compressor 1 Run',
            'di_module_1.2' => 'Compressor 2 Run',
            'di_module_1.4' => 'Compressor 3 Run',
            'di_module_1.6' => 'Compressor 4 Run',
            'di_module_1.8' => 'Compressor Screw 5 Run',
            'di_module_1.9' => 'Compressor Screw 6 Run',
            'di_module_1.10' => 'Compressor Screw 7 Run',
            'di_module_1.11' => 'Chill KC1 Run',
            'di_module_1.12' => 'Pump Circ KC1 Run',
            'di_module_1.13' => 'Chill KC2 Run',
            'di_module_1.14' => 'Pump Circ KC2 Run',
            'di_module_1.15' => 'Chill KC3 Run',
            'di_module_2.0' => 'Pump Circ KC3 Run',
            'di_module_2.1' => 'Chill KC4 Run',
            'di_module_2.2' => 'Pump Circ KC4 Run ',
            'di_module_2.3' => 'Chill KC5 Run',
            'di_module_2.4' => 'Pump Circ KC5 Run ',
            'di_module_2.5' => 'Chill KC6 Run ',
            'di_module_2.6' => 'Pump Circ KC6 Run',
            'di_module_2.7' => 'Chill KC7 Run',
            'di_module_2.8' => 'Pump Circ KC7 Run',
            'di_module_2.9' => 'Chill KC8 Run',
            'di_module_2.10' => 'Pump Circ KC8 Run ',
            'di_module_2.11' => 'Chill KC9 Run',
            'di_module_2.12' => 'Pump Circ KC9 Run ',
            'di_module_2.13' => 'Chill KC10 Run',
            'di_module_2.14' => 'Pump Circ KC10 Run',
            'di_module_2.15' => 'Chill KC11 Run',
            'di_module_3.0' => 'Pump Circ KC11 Run',
            'di_module_3.1' => 'Chilled WT1 Run',
            'di_module_3.2' => 'Chilled WT2 Run',
            'di_module_3.3' => 'Chilled WT3 Run',
            'di_module_3.4' => ' Chilled WT4 Run',
            'di_module_3.5' => 'Hot Water Pump 1 Run',
            'di_module_3.6' => 'Hot Water Pump 2 Run',
            'di_module_3.7' => 'Hot Water Pump 3 Run',
            'di_module_3.8' => 'Hot Water Pump 4 Run',
            'di_module_3.9' => 'Hot Water Pump 5 Run',
            'di_module_3.10' => 'Hot Water Pump 6 Run',
            'di_module_3.11' => 'Hot Water Pump 7 Run',
            'di_module_3.12' => 'Hot Water Pump 8 Run',
            'di_module_3.13' => 'Hot Water Pump 9 Run',
            'di_module_3.14' => 'Ground Tank Pump Run',
            'di_module_3.15' => 'Water Pump GK Run',
        ];
    
        return export($headers, $rows->toArray(), 'report_pku1_'.$from.'-'.$to);
    }
}
