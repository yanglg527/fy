<?php
/**
 * Created by PhpStorm.
 * User: jervis
 * Date: 16/5/12
 * Time: 下午8:00
 */

function makePage($page,$limit,$total,$arr){
    $j = 0;
    $list = array();
    for($i=($page-1)*$limit;$i<$limit*$page;$i++){
        $list[$j] = $arr[$i];
        $j++;
        if($i+1>=$total){
            break;
        }
    }
    return $list;
}
function quick_sort($arr,$direct = 'asc'){
    $len=count($arr);
    //设置一个空数组 用来接收冒出来的泡
    //该层循环控制 需要冒泡的轮数
    if($direct == 'desc'){
        for($i=1;$i<$len;$i++)
        { //该层循环用来控制每轮 冒出一个数 需要比较的次数
            for($k=0;$k<$len-$i;$k++)
            {
                if($arr[$k]['sort']<$arr[$k+1]['sort'])
                {
                    $tmp=$arr[$k+1];
                    $arr[$k+1]=$arr[$k];
                    $arr[$k]=$tmp;
                }
            }
        }
    }
    else{
        for($i=1;$i<$len;$i++)
        { //该层循环用来控制每轮 冒出一个数 需要比较的次数
            for($k=0;$k<$len-$i;$k++)
            {
                if($arr[$k]['sort']>$arr[$k+1]['sort'])
                {
                    $tmp=$arr[$k+1];
                    $arr[$k+1]=$arr[$k];
                    $arr[$k]=$tmp;
                }
            }
        }
    }

    return $arr;
}

if (!function_exists('responseError')) {
    function responseError($message, $statusCode=400)
    {
        header('Access-Control-Allow-Origin: *');
        // header("Access-Control-Allow-Headers: x-requested-with,content-type");
        header( "Access-Control-Allow-Methods:POST,GET" );

        $res = [
            'code' => $statusCode,
            'msg' => $message,
        ];
        echo json_encode($res);
        exit;
    }
}

if (!function_exists('responseSuccess')) {
    function responseSuccess($data='', $message='success', $statusCode=200)
    {
        header('Access-Control-Allow-Origin: *');
        // header("Access-Control-Allow-Headers: x-requested-with,content-type");
        header( "Access-Control-Allow-Methods:POST,GET" );
        $res = [
            'code' => $statusCode,
            'msg' => $message ,
            'data' => array()
        ];
        if( is_string($data) && $data ) {
            $res['msg'] = $data;
        }else{
            if( !empty($data) ) {
                $res['data'] = $data;
            }
        }
        echo json_encode($res);
        exit;
    }
}

/**
 * base64ToFile base64转文件
 * @param  string $base64
 * @param  string $path       保存位置根目录 Uploads开始
 * @param  string $fileName   保存的文件名
 * @return string|bool 成功返回 文件地址 失败返回false
 */
if (!function_exists('base64ImageContent')) {
    function base64ImageContent($base64, $path)
    {
        $domain = C('DOMAIN').'/';
        $path = 'Uploads/'.$path;
        if (!is_dir($path)) { // 如果目录不存在
            if(!mkDirs($path)) return false;
        }
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)){
            $path .= time() . rand(100, 999) . '.'.$result[2];
            $base64_string = explode(',', $base64);
            if($base64_string[1] == null){
                $tosave = $base64;
            }else{
                $tosave = $base64_string[1];
            }
            file_put_contents($path, base64_decode($tosave));
            return ($domain.$path);
        }
        return false;
    }
}

/**
 * 检查请求类型
 */
if (!function_exists('checkRequestType')) {
    function checkRequestType($quest)
    {
      switch ($quest) {
        case 'post':
            if (IS_POST) return true;
          break;

        case 'get':
            if (IS_GET) return true;
          break;

        case 'ajax':
            if (IS_AJAX) return true;
          break;
      }
      responseError('非法请求,错误的请求类型');
    }
}

if (!function_exists('getUserListByBranch')) {
    function getUserListByBranch($branchId)
    {
        if ($branchId) {
            $users = M('User')
            ->field('uid,realname,headimgurl')
            ->where(array('branch_id' => $branchId, 'status' => 1))
            ->select();
            return $users;
        }
        return array();
    }
}

if (!function_exists('meetingIsLike')) {
    function meetingIsLike($meetingId, $isLike)
    {
        $_meeting = D('ThreeMeeting');
        $_meeting->where(array('id'=>$meetingId));
        if ($isLike) {
            $bool = $_meeting->setInc('likes',1);
        }else {
            // if(0 == ($_meeting->getField('likes'))) return true;
            $bool = $_meeting->setDec('likes',1);
        }
        return $bool;
    }
}

if (!function_exists('meetingCollection')) {
    function meetingCollection($meetingId, $isCollection)
    {
        $_meeting = D('ThreeMeeting');
        $_meeting->where(array('id'=>$meetingId));
        if ($isCollection) {
            $bool = $_meeting->setInc('collection',1);
        }else {
            // if(0 == ($_meeting->getField('likes'))) return true;
            $bool = $_meeting->setDec('collection',1);
        }
        return $bool;
    }
}

/**
 * 更新浏览量
 */
if (!function_exists('addPageviews')) {
  function addPageviews($meetingId){
    M('ThreeMeeting')->where("id=$meetingId")->setInc('pageviews');
  }
}

/**
 * 获得用户已报名的志愿获得列表
 * @return [type] [description]
 */
if (!function_exists('getUserVolunteerSignUp')) {
    function getUserVolunteerSignUp(){
        $uid = uid();
        $_partner = M('VolunteerServicePartner');
        if ($_partner) {
            $list = $_partner->where(array('uid' => $uid, 'status' => 1))
                ->getField('volunteer_id',true);
            return $list;
        }
        return false;
    }
}

/**
 * 通用日志记录
 * @param $content
 * @param string $subDir
 * @return bool
 */
if (!function_exists('logs')) {
    function logs($content, $subDir = ''){
        $subDir = trim($subDir, '/\\');
        if (empty($content)) return false;
        static $arrConfig = array();
        if (empty($arrConfig)){
            $arrPath  = array(MODULE_NAME, CONTROLLER_NAME, ACTION_NAME);
            $arrConfig['root_path'] = './Runtime/Logs/';
            $arrConfig['sub_dir']   = implode('/', $arrPath);
            $arrConfig['file_name'] = '/' . date('Ymd') . '.log';
            $arrConfig['client_ip'] = get_client_ip();
            $arrConfig['logs_no']   = date('ymdHis') . microtime() * 1000000;
        }

        $filepath = $arrConfig['root_path'];
        if ($subDir){
            $filepath .= $subDir ;
        } else {
            $filepath .= $arrConfig['sub_dir'] ;
        }

        if (!file_exists($filepath)){
            mkdir($filepath, 0777, TRUE);
        }

        $filepath .= $arrConfig['file_name'];

        $header = sprintf("\r\n\r\n[%s][%s][%s] ", date('Y-m-d H:i:s'), $arrConfig['client_ip'],$arrConfig['logs_no']);
        @file_put_contents($filepath, $header . $content, FILE_APPEND);

        return $arrConfig['logs_no'] ;
    }
}
