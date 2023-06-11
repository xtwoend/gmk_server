<?php

namespace App\Resource;

use Carbon\Carbon;
use Hyperf\Resource\Json\JsonResource;

class ScoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        $avail_duration = (is_null($this->shift_id))? "24:00:00": "08:00:00";
       
        $now = Carbon::now();
        if($this->ended_at > $now) {
            $running_time = $this->started_at->diffInSeconds($now);
        }else{
            $running_time = $this->started_at->diffInSeconds($this->ended_at);
        }

        return array_merge($data, [
            'running' => (string) gmdate('H:i:s', $running_time),
            'run_duration' => (string) gmdate("H:i:s", $this->run_time),
            'down_duration' => (string) gmdate("H:i:s", $this->down_time),
            'stop_duration' => (string) gmdate("H:i:s", $this->stop_time),
            'avail_duration' => $avail_duration,
        ]);
    }
}
