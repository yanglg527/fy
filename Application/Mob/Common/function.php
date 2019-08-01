<?php
/**
 * Created by PhpStorm.
 * User: jervis
 * Date: 16/5/12
 * Time: 下午8:00
 */
function twoSum($nums, $target) {
    $result = array();
   for($i=0;$i<count($nums);$i++){
       for($j=$i+1;$j<count($nums)-1;$j++){

           if($nums[$i]+$nums[$j] == $target){
            array_push($result,$i,$j);
           }
       }
   }
    return $result;
}
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

/**
 * 获得文件后缀
 */
if (!function_exists('getFileExtension')) {
    function getFileExtension($file) {
        return array_pop(explode('.', $file));
    }
}

/**
 * 更新记录操作记录
 * @param  int $tasksId 任务ID
 * @param  string $content 内容
 */
if (!function_exists('updateExeLog')) {
    function updateExeLog($tasksId, $content, $createAt=0){
        $uid = uid();
        if (empty($tasksId) || empty($content) || empty($uid)) return false;
        $createAt = $createAt?$createAt:time();
        $data['exe_uid'] = $uid;
        $data['tasks_id'] = $tasksId;
        $data['content'] = $content;
        $data['create_at'] = $createAt;
        $_ExeLog = D('TasksExecutorLog');
        if ($_ExeLog->add($data)) {
            return $data;
        }
        return false;
    }
}

/**
 * 任务结办
 */
if (!function_exists('endTasks')) {
    function endTasks($id){
        $_TasksExecutor = M('TasksExecutor');
        $bool = $_TasksExecutor->where(array(
            'tasks_id' => $id,
            'exe_uid' => uid() ))->setField('exe_status', 1);
        if (false === $bool) {
            $name = user()['realname'];
            updateExeLog($id, $name.'已结办任务');
            // M('User')->
            return true;
        }
        return false;
    }
}
//按职务排序
function dutyorder($data){  //dutyorders  多了一个s
    if(count($data)>1){
        foreach($data as &$item){
            //status  0:成员 1:书记 2:副书记 3:管理员 4:	组织委员5:宣传委员6:纪检委员7:生活委员8:青年委员9:群工委员10:学习委员11:文体委员12:保卫委员  14:纪检书记 13:委员
            if($item['status'] == 1){
                $sj[] = $item;
            }elseif($item['status'] == 2 ){
                $fsj[] = $item;
            }elseif($item['status'] == 3 ){
                $adm[]  = $item;
            } elseif($item['status'] == 4 or $item['status'] == 5 or $item['status'] == 6 or $item['status'] == 7 or $item['status'] == 8 or $item['status'] == 9 or $item['status'] == 10 or $item['status'] == 11 or $item['status'] == 12 or $item['status'] == 14){
                $weiyuan[]  = $item;//各种委员
            }elseif($item['status'] == 13){
                $zbdy[] = $item;//支部党员
            } else{
                $member[] = $item;
            }
        }
        //拼装数组
        if($sj){
            $nndata[] = $sj[0];
        }
        foreach ($fsj as $a){
            $nndata[] = $a;
        }
        foreach ($adm as $b){
            $nndata[] = $b;
        }
        foreach ($weiyuan as $c){
            $nndata[] = $c;
        }
        foreach ($zbdy as $h){
            $nndata[] = $h;
        }
        foreach ($member as $d){
            $nndata[] = $d;
        }
        return $nndata;
    }else{
        return $data;
    }


}

//去除重名的
function un_repeat($users){
    $name = array();
    $data = array();
    foreach($users as $key=>$item){
        $name[$item['uid']] =  $item['realname'];
    }
    array_unique($name);
    foreach ($name as $index=>$v){
        $data[] =  D('User')->where(array('uid'=>$index))->field('uid,organization_id,status,realname,headimgurl')->find();
    }
    return $data;
}

//去重uid
function assoc_unique($arr, $key)
{
    $tmp_arr = array();
    foreach ($arr as $k => $v) {
        if (in_array($v[$key], $tmp_arr)) {//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
            unset($arr[$k]);
        } else {
            $tmp_arr[] = $v[$key];
        }
    }
    return $arr;
}
//按积分排名
function score_order($dwdata){
    $key = 'uid';
    $dwdata = assoc_unique($dwdata, $key);//去重uid
    //取积分
    foreach($dwdata as $v){
        $scoreData[] = D('User')->where(array('uid'=>$v['uid']))->field('score,uid,realname,headimgurl')->find();
    }
    //按积分排序
    foreach ($scoreData as $key=>$v){
        $newArr[$key]['score'] = $v['score'];
    }
    array_multisort($newArr,SORT_DESC,$scoreData);//SORT_DESC为降序，SORT_ASC为升序
    return $scoreData;
}
