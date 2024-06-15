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
class PanelPkp extends Model
{
    use DeviceTrait, ResourceTrait;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'panel_pkp';
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
        'ai_module_4' => 'array',
        'ai_module_5' => 'array',
        'ai_module_6' => 'array',
        'ai_module_7' => 'array',
        'ai_module_8' => 'array',
        'ai_module_9' => 'array',
        'ai_module_10' => 'array',
        'ai_module_11' => 'array',
        'ai_module_12' => 'array',
        'ai_module_13' => 'array',
        'ai_module_14' => 'array',
        'ai_module_15' => 'array',
        'ai_refiner' => 'array',
        'di_refiner' => 'array'
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
            'ai_module_1' => $this->map($data['aii_module_1']),
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
            'ai_refiner' => isset($data['ai_refiner'])? $this->map($data['ai_refiner']) : [],
            'di_refiner' => isset($data['di_refiner'])? $this->map($data['di_refiner']) : [],
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

        if($request->input('type', null) == 'refiner') {
            $headers =[
                'terminal_time' => 'Timestamp',
                'ai_refiner.0' => 'Temp Cooling Tank 1',
                'ai_refiner.1' => 'Temp Holding Tank 1',
                'ai_refiner.2' => 'Temp Cooling Tank 5',
                'ai_refiner.3' => 'Temp Holding Tank 6',
                'ai_refiner.4' => 'Temp Cooling Tank LM3',
                'ai_refiner.5' => 'Temp Holding Tank LM3',

                'di_refiner.0' => 'Cooling Tank 1 Run',
                'di_refiner.1' => 'Hold Tank 1 Run',
                'di_refiner.2' => 'Cooling Tank 5 Run',
                'di_refiner.3' => 'Hold Tank 6 Run',
                'di_refiner.4' => 'Cooling Tank LM3 Run',
                'di_refiner.5' => 'Holding Tank LM3 Run',
            ];
        }else{
            $headers =[
                'terminal_time' => 'Timestamp',
                'ai_module_1.0' => 'Level Tank 1',
                'ai_module_1.1' => 'Temp Piping 1',
                'ai_module_1.2' => 'Temp Tank 1',
                'ai_module_1.3' => 'Level Tank 2',
                'ai_module_1.4' => 'Temp Piping 2',
                'ai_module_1.5' => 'Temp Tank 2',
                'ai_module_1.6' => 'Level Tank 3',
                'ai_module_1.7' => 'Temp Piping 3',

                'ai_module_2.0' => 'Temp Tank 3',
                'ai_module_2.1' => 'Level Tank 4  ',
                'ai_module_2.2' => 'Temp Piping 4',
                'ai_module_2.3' => 'Temp Tank 4',
                'ai_module_2.4' => 'Level Tank 5',
                'ai_module_2.5' => 'Temp Piping 5',
                'ai_module_2.6' => 'Temp Tank 5',
                'ai_module_2.7' => 'Level Tank 6',

                'ai_module_3.0' => 'Temp Piping 6',
                'ai_module_3.1' => 'Temp Tank 6',
                'ai_module_3.2' => 'Level Tank 7',
                'ai_module_3.3' => 'Temp Piping 7',
                'ai_module_3.4' => 'Temp Tank 7',
                'ai_module_3.5' => 'Level Tank 8',
                'ai_module_3.6' => 'Temp Piping 8',
                'ai_module_3.7' => 'Temp Tank 8',

                'ai_module_4.0' => 'Level Tank 9',
                'ai_module_4.1' => 'Temp Piping 9',
                'ai_module_4.2' => 'Temp Tank 9',
                'ai_module_4.3' => 'Level Tank 10',
                'ai_module_4.4' => 'Temp Piping 10',
                'ai_module_4.5' => 'Temp Tank 10',
                'ai_module_4.6' => 'Level Tank 11',
                'ai_module_4.7' => 'Temp Piping 11',

                'ai_module_5.0' => 'Temp Tank 11',
                'ai_module_5.1' => 'Level Tank 12',
                'ai_module_5.2' => 'Temp Piping 12',
                'ai_module_5.3' => 'Temp Tank 12',
                'ai_module_5.4' => 'Level Tank 13',
                'ai_module_5.5' => 'Temp Piping 13',
                'ai_module_5.6' => 'Temp Tank 13',
                'ai_module_5.7' => 'Level Tank 14',

                'ai_module_6.0' => 'Temp Piping 14',
                'ai_module_6.1' => 'Temp Tank 14',
                'ai_module_6.2' => 'Level Tank 15',
                'ai_module_6.3' => 'Temp Piping 15',
                'ai_module_6.4' => 'Temp Tank 15',
                'ai_module_6.5' => 'Level Tank 16',
                'ai_module_6.6' => 'Temp Piping 16',
                'ai_module_6.7' => 'Temp Tank 16',

                'ai_module_7.0' => 'Level Tank 17',
                'ai_module_7.1' => 'Temp Piping 17',
                'ai_module_7.2' => 'Temp Tank 17',
                'ai_module_7.3' => 'Level Tank 18',
                'ai_module_7.4' => 'Temp Piping 18',
                'ai_module_7.5' => 'Temp Tank 18',
                'ai_module_7.6' => 'Level Tank 19',
                'ai_module_7.7' => 'Temp Piping 19',

                'ai_module_8.0' => 'Temp Tank 19',
                'ai_module_8.1' => 'Level Tank 20',
                'ai_module_8.2' => 'Temp Piping 20',
                'ai_module_8.3' => 'Temp Tank 20',
                'ai_module_8.4' => 'Level Tank 21',
                'ai_module_8.5' => 'Temp Piping 21',
                'ai_module_8.6' => 'Temp Tank 21',
                'ai_module_8.7' => 'Level Tank 22',

                'ai_module_9.0' => 'Temp Piping 22',
                'ai_module_9.1' => 'Temp Tank 22',
                'ai_module_9.2' => 'Level Tank 23',
                'ai_module_9.3' => 'Temp Piping 23',
                'ai_module_9.4' => 'Temp Tank 23',
                'ai_module_9.5' => 'Level Tank 24',
                'ai_module_9.6' => 'Temp Piping 24',
                'ai_module_9.7' => 'Temp Tank 24',

                'ai_module_10.0' => 'Level Tank 25',
                'ai_module_10.1' => 'Temp Piping 25',
                'ai_module_10.2' => 'Temp Tank 25',
                'ai_module_10.3' => 'Level Tank 26',
                'ai_module_10.4' => 'Temp Piping 26',
                'ai_module_10.5' => 'Temp Tank 26',
                'ai_module_10.6' => 'Level Tank 27',
                'ai_module_10.7' => 'Temp Piping 27',

                'ai_module_11.0' => 'Temp Tank 27',
                'ai_module_11.1' => 'Level Tank 28',
                'ai_module_11.2' => 'Temp Piping 28',
                'ai_module_11.3' => 'Temp Tank 28',
                'ai_module_11.4' => 'Level Tank 29',
                'ai_module_11.5' => 'Temp Piping 29',
                'ai_module_11.6' => 'Temp Tank 29',
                'ai_module_11.7' => 'Level Tank 30',

                'ai_module_12.0' => 'Temp Piping 30',
                'ai_module_12.1' => 'Temp Tank 30',
                'ai_module_12.2' => 'Level Tank 31',
                'ai_module_12.3' => 'Temp Piping 31',
                'ai_module_12.4' => 'Temp Tank 31',
                'ai_module_12.5' => 'Level Tank 32',
                'ai_module_12.6' => 'Temp Piping 32',
                'ai_module_12.7' => 'Temp Tank 32',

                'ai_module_13.0' => 'Level Tank 33',
                'ai_module_13.1' => 'Temp Piping 33',
                'ai_module_13.2' => 'Temp Tank 33',
                'ai_module_13.3' => 'Level Tank 34',
                'ai_module_13.4' => 'Temp Piping 34',
                'ai_module_13.5' => 'Temp Tank 34',
                'ai_module_13.6' => 'Level Tank 35',
                'ai_module_13.7' => 'Temp Piping 35',

                'ai_module_14.0' => 'Temp Tank 35',
                'ai_module_14.1' => 'Level Tank 36',
                'ai_module_14.2' => 'Temp Piping 36',
                'ai_module_14.3' => 'Temp Tank 36',
                'ai_module_14.4' => 'Level Tank 37',
                'ai_module_14.5' => 'Temp Piping 37',
                'ai_module_14.6' => 'Temp Tank 37',
                'ai_module_14.7' => 'Level Tank 38',

                'ai_module_15.0' => 'Temp Piping 38',
                'ai_module_15.1' => 'Temp Tank 38',
                'ai_module_15.2' => 'Level Tank 39',
                'ai_module_15.3' => 'Temp Piping 39',
                'ai_module_15.4' => 'Temp Tank 39',
                'ai_module_15.5' => 'Level Tank 40',
                'ai_module_15.6' => 'Temp Piping 40',
                'ai_module_15.7' => 'Temp Tank 40',

                // 'di_module_1.0' => 'Pump 1 Run',
                // 'di_module_1.1' => 'Pump 2 Run',
                // 'di_module_1.2' => 'Pump 3 Run',
                // 'di_module_1.3' => 'Pump 4 Run',
                // 'di_module_1.4' => 'Pump 5 Run',
                // 'di_module_1.5' => 'Pump 6 Run',
                // 'di_module_1.6' => 'Pump 7 Run',
                // 'di_module_1.7' => 'Pump 8 Run',
                // 'di_module_1.8' => 'Pump 9 Run',
                // 'di_module_1.9' => 'Pump 10 Run',
                // 'di_module_1.10' => 'Pump 11 Run',
                // 'di_module_1.11' => 'Pump 12 Run',
                // 'di_module_1.12' => 'Pump 13 Run',
                // 'di_module_1.13' => 'Pump 14 Run',
                // 'di_module_1.14' => 'Pump 15 Run',
                // 'di_module_1.15' => 'Pump 16 Run',

                // 'di_module_2.0' => 'Pump 17 Run',
                // 'di_module_2.1' => 'Pump 18 Run',
                // 'di_module_2.2' => 'Pump 19 Run',
                // 'di_module_2.3' => 'Pump 20 Run',
                // 'di_module_2.4' => 'Pump 21 Run',
                // 'di_module_2.5' => 'Pump 22 Run',
                // 'di_module_2.6' => 'Pump 23 Run',
                // 'di_module_2.7' => 'Pump 24 Run',
                // 'di_module_2.8' => 'Pump 25 Run',
                // 'di_module_2.9' => 'Pump 26 Run',
                // 'di_module_2.10' => 'Pump 27 Run',
                // 'di_module_2.11' => 'Pump 28 Run',
                // 'di_module_2.12' => 'Pump 29 Run',
                // 'di_module_2.13' => 'Pump 30 Run',
                // 'di_module_2.14' => 'Pump 31 Run',
                // 'di_module_2.15' => 'Pump 32 Run',

                // 'di_module_3.0' => 'Pump 33 Run',
                // 'di_module_3.1' => 'Pump 34 Run',
                // 'di_module_3.2' => 'Pump 35 Run',
                // 'di_module_3.3' => 'Pump 36 Run',
                // 'di_module_3.4' => 'Pump 37 Run',
                // 'di_module_3.5' => 'Pump 38 Run',
                // 'di_module_3.6' => 'Pump 39 Run',
                // 'di_module_3.7' => 'Pump 40 Run',


                // 'di_module_4.0' => 'Agitator 1 Run',
                // 'di_module_4.1' => 'Agitator 2 Run',
                // 'di_module_4.2' => 'Agitator 3 Run',
                // 'di_module_4.3' => 'Agitator 4 Run',
                // 'di_module_4.4' => 'Agitator 5 Run',
                // 'di_module_4.5' => 'Agitator 6 Run',
                // 'di_module_4.6' => 'Agitator 7 Run',
                // 'di_module_4.7' => 'Agitator 8 Run',
                // 'di_module_4.8' => 'Agitator 9 Run',
                // 'di_module_4.9' => 'Agitator 10 Run',
                // 'di_module_4.10' => 'Agitator 11 Run',
                // 'di_module_4.11' => 'Agitator 12 Run',
                // 'di_module_4.12' => 'Agitator 13 Run',
                // 'di_module_4.13' => 'Agitator 14 Run',
                // 'di_module_4.14' => 'Agitator 15 Run',
                // 'di_module_4.15' => 'Agitator 16 Run',

                // 'di_module_5.0' => 'Agitator 17 Run',
                // 'di_module_5.1' => 'Agitator 18 Run',
                // 'di_module_5.2' => 'Agitator 19 Run',
                // 'di_module_5.3' => 'Agitator 20 Run',
                // 'di_module_5.4' => 'Agitator 21 Run',
                // 'di_module_5.5' => 'Agitator 22 Run',
                // 'di_module_5.6' => 'Agitator 23 Run',
                // 'di_module_5.7' => 'Agitator 24 Run',
                // 'di_module_5.8' => 'Agitator 25 Run',
                // 'di_module_5.9' => 'Agitator 26 Run',
                // 'di_module_5.10' => 'Agitator 27 Run',
                // 'di_module_5.11' => 'Agitator 28 Run',
                // 'di_module_5.12' => 'Agitator 29 Run',
                // 'di_module_5.13' => 'Agitator 30 Run',
                // 'di_module_5.14' => 'Agitator 31 Run',
                // 'di_module_5.15' => 'Agitator 32 Run',

                // 'di_module_6.0' => 'Agitator 33 Run',
                // 'di_module_6.1' => 'Agitator 34 Run',
                // 'di_module_6.2' => 'Agitator 35 Run',
                // 'di_module_6.3' => 'Agitator 36 Run',
                // 'di_module_6.4' => 'Agitator 37 Run',
                // 'di_module_6.5' => 'Agitator 38 Run',
                // 'di_module_6.6' => 'Agitator 39 Run',
                // 'di_module_6.7' => 'Agitator 40 Run',
            ];
        }
    
        return export($headers, $rows->toArray(), 'report_'.$from.'-'.$to);
    }
}
