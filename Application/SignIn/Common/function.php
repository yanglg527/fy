<?php
/**
 * Created by PhpStorm.
 * User: jervis
 * Date: 16/5/12
 * Time: 下午8:00
 */

function fill_zero_before($obj){
    if($obj < 10){
        return '0'.$obj;
    }else{
        return $obj;
    }
}

function getSignedClass($signStateList, $day){
    return $signStateList[$day . 'class'];
}

function getSignState($signStateList, $day){
    return $signStateList[$day];
}

function  isToday($date){
    $today = date('Y-m-d');
    return isSameDay($today,$date);
}

/**
 *
 * @param $day1 ('Y-m-d')
 * @param $day2 ('Y-m-d')
 * @return bool
 */
function isSameDay($day1,$day2){
    if($day1 == $day2){
        return true;
    }else{
        return false;
    }
}