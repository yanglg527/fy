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

function  isToday($date){
    $today = date('Y-m-d');
    return isSameDay($today,$date);
}

function isSameDay($day1,$day2){
    if($day1 == $day2){
        return true;
    }else{
        return false;
    }
}

function getSignedClass($signStateList, $day){
    return $signStateList[$day . 'class'];
}

function get_content($content, $count) {
    if($content == undefined || $content == null) {
        return "";
    }
    if($count == undefined || $count == 0){
        $count = 20;
    }
    $c = strip_tags($content);
//    return $c;
    $lenght = strlen($c);
    if($lenght >= $count) {
        return substr($c,0,$count)."...";
    } else {
        return $c;
    }
}

function show_center_rate($user){
    $center_f = array('headimgurl','realname','gender','birthday','official_date','mobile');
    $tcount = count($center_f);
    $count = 0;
    foreach ($center_f as $c){
        if($user[$c]){
            $count++;
        }
    }
    return sprintf('%.2f', $count/$tcount * 100);
}

function show_rate($p,$total){
    return sprintf('%.1f', $p/$total * 100);
}


function show_tz_img($str, $size)
{
    if ($str) {
        if (strpos($str, 'http:')>-1 || strpos($str, 'https:')>-1) {
            return $str;
        } elseif (strpos($str, 'paint/')>-1) {
            return __ROOT__ . '/Uploads' . $str;
        } elseif (strpos($str, 'Uploads/')>-1) {
            if ($size == 'middle') {
                return __ROOT__ . '/' . $str . '-m.png';
            } elseif ($size == 'small') {
                return __ROOT__ . '/' . $str . '-s.png';
            } else {
                return __ROOT__ . '/' . $str;
            }
        } else {
            if ($size == 'middle') {
                return __ROOT__ . '/Uploads/' . $str . '-m.png';
            } elseif ($size == 'small') {
                return __ROOT__ . '/Uploads/' . $str . '-s.png';
            } else {
                return __ROOT__ . '/Uploads/' . $str;
            }
        }
    } else {
        return __ROOT__ . '/Public/Mob/images/common/tz.png';
    }
}


function show_count($num){
	if($num){
		return intval($num);
	}else{
		return 0;
	}
}
