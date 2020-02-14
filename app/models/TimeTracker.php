<?php

namespace Growave2\Models;
use Growave2\Plugins\Times;
use Phalcon\Mvc\Model;

class TimeTracker extends Model
{

    public $user_id;
    public $first_name;
    public $tr_date;
    public $start_time;
    public $stop_time;
    public $total;
    public $late;
    public $assigned;

    public static function getDataFromDb($id, $ym)
    {
        return self::find([
            'conditions'=> 'user_id = ?1 and ym = ?2',
            'order' => 'time_id DESC',
            'bind' => [
                1 => $id,
                2 => $ym
            ]
        ]);

    }
    public static function getTotalAssignedHours($user_id,$ym)
    {
        $currentUserTime = self::getDataFromDb($user_id, $ym);
        if ($currentUserTime != false)
        {
            $assign = 0;
            foreach ($currentUserTime->toArray() as $data)
            {
                $assign += $data['assigned'];
            }
            return $assign;
        }
        else{
            return;
        }
    }

     public static function getTotalLateHours($user_id,$ym)
    {
        $currentUserTime = self::getDataFromDb($user_id, $ym);
        if ($currentUserTime)
        {
            $times = new Times();
            $totalLateInSec = 0;
            foreach ($currentUserTime as $data)
            {
                $totalLateInSec += $times->timeToSec($data->late);
            }
            return $times->secondsToHour($totalLateInSec);
        }
        else{
            return;
        }
    }

    public static function workedHoursInPercentage($user_id,$ym)
    {
        $currentUserTime = self::getDataFromDb($user_id, $ym);
        $times = new Times();
        $hours = $times->timeToSec("00:00:00");
        $normal = 576000;
        if ($currentUserTime) {
            foreach ($currentUserTime as $data) {
                $hours += $times->timeToSec($data->total);
            }
            return  round($hours = $hours * 100 / $normal,3);
        }
        else{
            return;
        }

    }
    public static function lessHours($total)
    {
        $times = new \Growave2\Plugins\Times();
        $duty = date('H:i:s',mktime(8,0,0));
        return $times->interval($duty,$total);
    }
}