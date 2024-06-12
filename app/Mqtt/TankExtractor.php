<?php

namespace App\Mqtt;

use Hyperf\Codec\Json;


class TankExtractor
{
    protected array $attributes = [];
 
    public function __construct(string $message) {
        $this->init($message);
    }

    public function __set(string $attribute, $val)
    {
        $this->attributes[$attribute] = $val;
        return $this;
    }

    public function __get(string $attribute)
    {
        return $this->attributes[$attribute] ?: '';
    }

    public function init(string $message)
    {
        try {
            $data = Json::decode($message);

            $this->attributes['level_tank1'] = $data['ai_module_1'][0];
            $this->attributes['temp_piping_tank1'] =$data['ai_module_1'][1];
            $this->attributes['temp_tank1'] = $data['ai_module_1'][2];

            $this->attributes['level_tank2'] = $data['ai_module_1'][3];
            $this->attributes['temp_piping_tank2'] =$data['ai_module_1'][4];
            $this->attributes['temp_tank2'] = $data['ai_module_1'][5];

            $this->attributes['level_tank3'] = $data['ai_module_1'][6];
            $this->attributes['temp_piping_tank3'] =$data['ai_module_1'][7];
            $this->attributes['temp_tank3'] = $data['ai_module_2'][0];

            $this->attributes['level_tank4'] = $data['ai_module_2'][1];
            $this->attributes['temp_piping_tank4'] =$data['ai_module_2'][2];
            $this->attributes['temp_tank4'] = $data['ai_module_2'][3];

            $this->attributes['level_tank5'] = $data['ai_module_2'][4];
            $this->attributes['temp_piping_tank5'] =$data['ai_module_2'][5];
            $this->attributes['temp_tank5'] = $data['ai_module_2'][6];

            $this->attributes['level_tank6'] = $data['ai_module_2'][7];
            $this->attributes['temp_piping_tank6'] =$data['ai_module_3'][0];
            $this->attributes['temp_tank6'] = $data['ai_module_3'][1];

            $this->attributes['level_tank7'] = $data['ai_module_3'][2];
            $this->attributes['temp_piping_tank7'] =$data['ai_module_3'][3];
            $this->attributes['temp_tank7'] = $data['ai_module_3'][4];

            $this->attributes['level_tank8'] = $data['ai_module_3'][5];
            $this->attributes['temp_piping_tank8'] =$data['ai_module_3'][6];
            $this->attributes['temp_tank8'] = $data['ai_module_3'][7];

            $this->attributes['level_tank9'] = $data['ai_module_4'][0];
            $this->attributes['temp_piping_tank9'] =$data['ai_module_4'][1];
            $this->attributes['temp_tank9'] = $data['ai_module_4'][2];
            
            $this->attributes['level_tank10'] = $data['ai_module_4'][3];
            $this->attributes['temp_piping_tank10'] =$data['ai_module_4'][4];
            $this->attributes['temp_tank10'] = $data['ai_module_4'][5];

            // lg sini
            $this->attributes['level_tank11'] = $data['ai_module_5'][6];
            $this->attributes['temp_piping_tank11'] =$data['ai_module_5'][7];
            $this->attributes['temp_tank11'] = $data['ai_module_5'][0];

            $this->attributes['level_tank12'] = $data['ai_module_5'][1];
            $this->attributes['temp_piping_tank12'] =$data['ai_module_5'][2];
            $this->attributes['temp_tank12'] = $data['ai_module_5'][3];

            $this->attributes['level_tank13'] = $data['ai_module_5'][4];
            $this->attributes['temp_piping_tank13'] =$data['ai_module_5'][5];
            $this->attributes['temp_tank13'] = $data['ai_module_6'][6];

            $this->attributes['level_tank14'] = $data['ai_module_6'][7];
            $this->attributes['temp_piping_tank14'] =$data['ai_module_6'][0];
            $this->attributes['temp_tank14'] = $data['ai_module_6'][1];

            $this->attributes['level_tank15'] = $data['ai_module_6'][2];
            $this->attributes['temp_piping_tank15'] =$data['ai_module_6'][3];
            $this->attributes['temp_tank15'] = $data['ai_module_6'][4];

            $this->attributes['level_tank16'] = $data['ai_module_6'][5];
            $this->attributes['temp_piping_tank16'] =$data['ai_module_7'][6];
            $this->attributes['temp_tank16'] = $data['ai_module_7'][7];

            $this->attributes['level_tank17'] = $data['ai_module_7'][0];
            $this->attributes['temp_piping_tank17'] =$data['ai_module_7'][1];
            $this->attributes['temp_tank17'] = $data['ai_module_7'][2];

            $this->attributes['level_tank18'] = $data['ai_module_7'][3];
            $this->attributes['temp_piping_tank18'] =$data['ai_module_7'][4];
            $this->attributes['temp_tank18'] = $data['ai_module_7'][5];

            $this->attributes['level_tank19'] = $data['ai_module_8'][6];
            $this->attributes['temp_piping_tank19'] =$data['ai_module_8'][7];
            $this->attributes['temp_tank19'] = $data['ai_module_8'][0];
            
            $this->attributes['level_tank20'] = $data['ai_module_8'][1];
            $this->attributes['temp_piping_tank20'] =$data['ai_module_8'][2];
            $this->attributes['temp_tank20'] = $data['ai_module_8'][3];

            // 11
            $this->attributes['level_tank21'] = $data['ai_module_8'][4];
            $this->attributes['temp_piping_tank21'] =$data['ai_module_8'][5];
            $this->attributes['temp_tank21'] = $data['ai_module_9'][6];

            $this->attributes['level_tank22'] = $data['ai_module_9'][7];
            $this->attributes['temp_piping_tank22'] =$data['ai_module_9'][0];
            $this->attributes['temp_tank22'] = $data['ai_module_9'][1];

            $this->attributes['level_tank23'] = $data['ai_module_9'][2];
            $this->attributes['temp_piping_tank23'] =$data['ai_module_9'][3];
            $this->attributes['temp_tank23'] = $data['ai_module_9'][4];

            $this->attributes['level_tank24'] = $data['ai_module_9'][5];
            $this->attributes['temp_piping_tank24'] =$data['ai_module_10'][6];
            $this->attributes['temp_tank24'] = $data['ai_module_10'][7];

            $this->attributes['level_tank25'] = $data['ai_module_10'][0];
            $this->attributes['temp_piping_tank25'] =$data['ai_module_10'][1];
            $this->attributes['temp_tank25'] = $data['ai_module_10'][2];

            $this->attributes['level_tank26'] = $data['ai_module_10'][3];
            $this->attributes['temp_piping_tank26'] =$data['ai_module_10'][4];
            $this->attributes['temp_tank26'] = $data['ai_module_10'][5];

            $this->attributes['level_tank27'] = $data['ai_module_11'][6];
            $this->attributes['temp_piping_tank27'] =$data['ai_module_11'][7];
            $this->attributes['temp_tank27'] = $data['ai_module_11'][0];

            $this->attributes['level_tank28'] = $data['ai_module_11'][1];
            $this->attributes['temp_piping_tank28'] =$data['ai_module_11'][2];
            $this->attributes['temp_tank28'] = $data['ai_module_11'][3];

            $this->attributes['level_tank29'] = $data['ai_module_11'][4];
            $this->attributes['temp_piping_tank29'] =$data['ai_module_11'][5];
            $this->attributes['temp_tank29'] = $data['ai_module_12'][6];
            
            $this->attributes['level_tank30'] = $data['ai_module_12'][7];
            $this->attributes['temp_piping_tank30'] =$data['ai_module_12'][0];
            $this->attributes['temp_tank30'] = $data['ai_module_12'][1];

            $this->attributes['level_tank31'] = $data['ai_module_12'][2];
            $this->attributes['temp_piping_tank31'] =$data['ai_module_12'][3];
            $this->attributes['temp_tank31'] = $data['ai_module_12'][4];

            $this->attributes['level_tank32'] = $data['ai_module_12'][5];
            $this->attributes['temp_piping_tank32'] =$data['ai_module_13'][6];
            $this->attributes['temp_tank32'] = $data['ai_module_13'][7];

            $this->attributes['level_tank33'] = $data['ai_module_13'][0];
            $this->attributes['temp_piping_tank33'] =$data['ai_module_13'][1];
            $this->attributes['temp_tank33'] = $data['ai_module_13'][2];

            $this->attributes['level_tank34'] = $data['ai_module_13'][3];
            $this->attributes['temp_piping_tank34'] =$data['ai_module_13'][4];
            $this->attributes['temp_tank34'] = $data['ai_module_13'][5];

            $this->attributes['level_tank35'] = $data['ai_module_14'][6];
            $this->attributes['temp_piping_tank35'] =$data['ai_module_14'][7];
            $this->attributes['temp_tank35'] = $data['ai_module_14'][0];

            $this->attributes['level_tank36'] = $data['ai_module_14'][1];
            $this->attributes['temp_piping_tank36'] =$data['ai_module_14'][2];
            $this->attributes['temp_tank36'] = $data['ai_module_14'][3];

            $this->attributes['level_tank37'] = $data['ai_module_14'][4];
            $this->attributes['temp_piping_tank37'] =$data['ai_module_14'][5];
            $this->attributes['temp_tank37'] = $data['ai_module_15'][6];

            $this->attributes['level_tank38'] = $data['ai_module_15'][7];
            $this->attributes['temp_piping_tank38'] =$data['ai_module_15'][0];
            $this->attributes['temp_tank38'] = $data['ai_module_15'][1];

            $this->attributes['level_tank39'] = $data['ai_module_15'][2];
            $this->attributes['temp_piping_tank39'] =$data['ai_module_15'][3];
            $this->attributes['temp_tank39'] = $data['ai_module_15'][4];
            
            $this->attributes['level_tank40'] = $data['ai_module_15'][5];
            $this->attributes['temp_piping_tank40'] =$data['ai_module_15'][6];
            $this->attributes['temp_tank40'] = $data['ai_module_15'][7];
            
        } catch (\Throwable $th) {
            
        }
        return $this->attributes;
    }

    public function toArray()
    {
        return (array) $this->attributes;
    }
}