<?php
namespace Growave2\Plugins;
use Phalcon\Di\Injectable;

class Times extends Injectable
{
    public function getDefaultTimeZone()
    {
        $timeZone = date_default_timezone_get();
        return $timeZone;
    }

    public function timeToSec($hours) {
        $t = explode(':', $hours);
        $hour = $t[0] * 60 * 60;
        $minute = $t[1] * 60;
        $second = $t[2];
        return $hour + $minute + $second;
    }

    public function secondsToHour($seconds)
    {
        $minutes = round($seconds / 60);
        $rest = $seconds%60;
        $hours = round($minutes / 60);
        $minutes = abs($minutes - ($hours * 60));
        if ($hours < 10) {
            return "0".$hours.':'.$minutes.':'.round($rest);
        }elseif ($minutes < 10) {
            return $hours.':'."0".$minutes.':'.round($rest);
        }elseif (round($rest)< 10) {
            return $hours.':'.$minutes.':'."0".round($rest);
        }
        else{
            return $hours.':'.$minutes.':'.round($rest);
        }
    }
    /*it is used in TimeTracker modul*/
    public function getOnlyYearFromDate($date)
    {
        $arr = explode('-', $date);
        $year = $arr[0];
        return $year;
    }

    public function getHourWithoutMinutes($assignedHours)
    {
        $t = explode(':', $assignedHours);
        $h = $t[0];
        return $h;
    }
    /*01 */
    public function getMonthFromDate($date)
    {
        $arr = explode('-', $date);
        $month = $arr[1];
        return $month;
    }
    /*This function for setHolidays*/
    public function getYearFromDate($date)
    {
        $arr = explode('-', $date);
        $year = str_split($arr[0]);
        $yea = $year[0].$year[1].$year[2];
        return $yea;
    }

    /*This function for setHolidays*/
    public function getRemainingNumberFromYear($date)
    {
        $arr = explode('-', $date);
        $year = str_split($arr[0]);
        $remainingNumber = $year[3];
        return $remainingNumber;
    }

    /*This function for setHolidays*/
    public function getDayFromDate($date)
    {
        $arr = explode('-', $date);
        $day = $arr[2];
        return $day;
    }

    public function createTime()
    {
        return date('H:i:s');
    }

    public function getDaysInMonth()
    {
        return date("t");
    }

    /*1  Января*/
    public function getDateOfToday()
    {
        return date("m");

    }

    /*Другой формат*/
    public function createDateOFToday()
    {
        return $date = new \DateTime();
    }

    /*Формат 1*/
    public function todayIs()
    {
        return date("d");
    }


    public function getLateHours($start_time)
    {
        $duty = date('H:i:s', mktime(9,30,59));
        if ($start_time <= $duty)
        {
            return  date('H:i:s', mktime(0,0,0));
        }
        else
        {
             $lateInSeconds = $this->timeToSec($start_time) - $this->timeToSec($duty);
             return $this->secondsToHour($lateInSeconds);
        }

    }
    public function interval($start_time, $stop_time)
    {
        $intervalInSeconds = $this->timeToSec($stop_time) - $this->timeToSec($start_time);
        $intervalInSeconds = abs($intervalInSeconds);
        return $this->secondsToHour($intervalInSeconds);
    }

    public function assignedHoursPerDay($total)
    {
        return $this->getHourWithoutMinutes($total);

    }

    public function compareTimeZonez()
    {
        date_default_timezone_set('Asia/Bishkek');
        $script_tz = date_default_timezone_get();

        if (strcmp($script_tz, ini_get('date.timezone'))){
            return 'Временная зона скрипта отличается от заданной в INI-файле.';
        } else {
            return 'Временные зоны скрипта и настройки INI-файла совпадают.';
        }
        exit;

    }

    public function DateMonth($year, $month)
    {
        if (isset($year) && isset($month))
        {
                $number = cal_days_in_month(CAL_GREGORIAN, $month,$year );
                 return $number;

        }
    }
    public function convertNumberToMonthName($month)
    {
        $monthName = date('F', mktime(0, 0, 0, $month, 10)); // March
        return $monthName;
    }

}