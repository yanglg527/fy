<?php
/**
 * Created by PhpStorm.
 * User: jervis
 * Date: 16/5/12
 * Time: 下午8:00
 */

function howLongAgo($begin_time){
    $timediff = timediff($begin_time, time());
    if($timediff['day'] > 7){
        return date('Y-m-d', $begin_time);
    }else if($timediff['day'] > 0){
        return $timediff['day']."天前";
    }else if($timediff['hour'] > 0){
        return $timediff['hour']."小时前";
    }else if($timediff['min'] > 0){
        return $timediff['min']."分钟前";
    }else{
        return "刚刚";
    }
}


function timediff( $begin_time, $end_time )
{
    if ( $begin_time < $end_time ) {
        $starttime = $begin_time;
        $endtime = $end_time;
    } else {
        $starttime = $end_time;
        $endtime = $begin_time;
    }
    $timediff = $endtime - $starttime;
    $days = intval( $timediff / 86400 );
    $remain = $timediff % 86400;
    $hours = intval( $remain / 3600 );
    $remain = $remain % 3600;
    $mins = intval( $remain / 60 );
    $secs = $remain % 60;
    $res = array( "day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs );
    return $res;
}