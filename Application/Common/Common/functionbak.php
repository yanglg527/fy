<?php


/**
 * Created by PhpStorm.
 * User: jervis
 * Date: 16/5/12
 * Time: 下午8:00
 */
/**
 * 权限验证
 * @param rule string|array  需要验证的规则列表,支持逗号分隔的权限规则或索引数组
 * @param uid  int           认证用户的id
 * @param string mode        执行check的模式
 * @param relation string    如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
 * @return boolean           通过验证返回true;失败返回false
 */
function authCheck($rule,$uid,$type=1, $mode='url', $relation='or'){
    //超级管理员跳过验证
    $auth=new \Think\Auth();
    //获取当前uid所在的角色组id
    $groups=$auth->getGroups($uid);

    // 遍历该用户的组信息，若有管理员角色，则返回true
    foreach($groups as $value){
        if(in_array($value['group_id'],C('ADMINISTRATOR'))){
            return true;
        }
    }


    return $auth->check($rule,$uid,$type,$mode,$relation)?true:false;
}



/*******登录 start********/
/***
 * @return mixed
 *
 * uid
 * role_name //角色 党员|群众....
 * post_name//职务
 * branch_name //支部
 * headimgurl//头像 页面显示配合get_head_url方法调用
 * mobile//电话
 * email//邮件
 * realname//真实姓名
 * score//积分
 * birthday//生日
 * nickname//昵称
 * job_resume//工作简历
 * work_unit//工作单位
 * reward_situation//个人奖励情况
 *
 *
 *
 */
function user(){
    $sessionNames = C('SESSION_CONFIG');
    $user = session($sessionNames['USER_SESSION']);

//    测试用的
//    if(!$user){
    if(C('TEST_ON') == 1)
    {
        // $user = D('UserView')->where("User.uid=10089")->find();
    
        $user = D('UserView')->where("User.uid=10022")->find();


//         session('djpt_user',$user);
    }
    if($user['uid']) {
//      $u = D('User')->find($user['uid']);
//      if ($user['update_time'] != $u['update_time']) {   //  需要重置session
            $user = D('UserView')->where("User.uid=" . $user['uid'])->find();//搜索用户信息
            resession_user($user);//重新设定用户session
//      }
    }else{
    	return null;
    }
    return $user;
}


function uid(){

    $user = user();
    if($user){
        return $user['uid'];
    }
    return 0;
}

/**重新设置 session
 * @param $user
 */
function resession_user($user){
    $sessionNames = C('SESSION_CONFIG');
    $userSsessionName = $sessionNames['USER_SESSION'];
    session($userSsessionName, $user);
}

/**
 * @param $value
 * @return string对象转 json
 */
function to_json_string($value){
    return str_replace(PHP_EOL,"",json_encode($value, JSON_UNESCAPED_UNICODE));
}


/**
 * 字符串转对象
 * @param $string
 * @return mixed
 */
function to_json_obj($string){
    return json_decode($string, true);
}


/**************用户信息工具****************/
/*****其他工具****/
function get_ip_address()
{
    $IPaddress = '';
    if (isset($_SERVER)) {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $IPaddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $IPaddress = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $IPaddress = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")) {
            $IPaddress = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $IPaddress = getenv("HTTP_CLIENT_IP");
        } else {
            $IPaddress = getenv("REMOTE_ADDR");
        }
    }
    return $IPaddress;
}

function get_ip_city_info($clientIP)
{
    $taobaoIP = 'http://ip.taobao.com/service/getIpInfo.php?ip=' . $clientIP;
    $IPinfo = json_decode(file_get_contents($taobaoIP));
    $province = $IPinfo->data->region;
    $city = $IPinfo->data->city;
    $data = $city;
    return $data;
}

function ajaxMsg($status, $msg, $data,$jsonp = false)
{
    header("content-type: text/html;charset=utf-8");

    $ajax = array(
        "status" => $status,
        "msg" => $msg
    );
    if ($data) {
        $ajax['data'] = $data;
    }
    $json_data = json_encode($ajax,JSON_UNESCAPED_UNICODE);
    if($jsonp){
        $jsoncallback =$_REQUEST ['callback'];
        echo $jsoncallback . "(" . $json_data . ")";
    }
    else{
        echo json_encode($ajax,JSON_UNESCAPED_UNICODE);
    }
    exit();
}
//function ajaxMsg($status, $msg, $data)
//{
//    header("content-type: text/html;charset=utf-8");
//    $ajax = array(
//        "status" => $status,
//        "msg" => $msg
//    );
//
//    if ($data) {
//        $ajax['data'] = $data;
//    }
//
//    echo json_encode($ajax,JSON_UNESCAPED_UNICODE);
//    exit();
//}


function login($uid)
{
    $user = D('UserView')->where("User.uid=$uid")->find();

    if($user){
    	
    $ip = get_ip_address();
//    $city = get_ip_city_info($ip);

    $u = array();
    $u['last_login_ip'] = $ip;
    $u['last_login_time'] = time();
    $user['login_count'] = $user['login_count'] + 1;
    $u['login_count'] = $user['login_count'];
    D('User')->where("uid=$uid")->save($u);

    $sessionNames = C('SESSION_CONFIG');
    $userSsessionName = $sessionNames['USER_SESSION'];
    session($userSsessionName, $user);
	 }
}


function logout()
{
    $sessionNames = C('SESSION_CONFIG');
    $userSsessionName = $sessionNames['USER_SESSION'];
    session($userSsessionName, null);
    session_destroy();
}


function updateStudyTime($studyTime){
    $user = user();
    if($user){
        $user['study_time_sum'] += $studyTime;
        D('User')->save($user);
        resession_user($user);
//        create_study_time_log($studyTime);
    }
    return $user['study_time_sum'];
}


function create_study_time_log($studyTime){
    $note = array(
        "uid"=>uid(),
        "create_time"=>time(),
        "study_time"=>$studyTime
    );
    D("UserStudyTimeLog")->add($note);
}

/**
 * @param $uid
 * @param $score
 * @param int $type
 * @param $tipContent
 */
function update_user_score($uid, $score, $type=0, $tipContent){
    $user = D('User')->find($uid);
    if($user){
        $user['score'] = $user['score'] + $score;
        $user['cost_score'] = $user['cost_score'] + $score;
        D('User')->save($user);
//        resession_user($user);  // 只能更新自身的session数据，不能更新指定uid的session数据
        create_score_log($score, $type, $tipContent,$uid);
        create_cost_score_log($score, $type,$uid); // 生成兑换积分记录
    }
}

/**
 *
 * @param $score // 积分改变值
 * @param int $type // 积分获取途径
 */
function update_score($score, $type=0, $tipContent){
    // 更新用户积分
    $user = user();
    $user['score'] = $user['score'] + $score;
    $user['cost_score'] = $user['cost_score'] + $score;
    D('User')->save($user);  // 更新到数据库
    resession_user($user);    // 更新到session
    create_score_log($score, $type, $tipContent); // 生成积分记录
    create_cost_score_log($score, $type); // 生成兑换积分记录
}

/**
 * @param $score  增加、扣除分数，扣除为负数
 * @param $type 积分变动的原因，1
 *
 */
//function update_cost_score($score, $type=0){
//    // 更新用户积分
//    $uid = uid();
//    update_user_cost_score($uid, $score, $type);
//}

//function update_user_cost_score($uid, $score, $type=0){
//    // 更新用户积分
//    $user = D('User')->find($uid);
//    if($user){
////        $user['cost_score'] = ($user['cost_score'] + $score) > 0 ? $user['cost_score'] + $score : 0;
//        $user['cost_score'] = $user['cost_score'] + $score;
//        D('User')->where(array('uid'=>$user['uid']))->save($user);
//
//        resession_user($user);
//
//        // 生成积分记录
//        create_cost_score_log($score, $type);
//    }
//}

function create_score_log($score, $type, $tipContent, $uid=0){
    // 生成积分记录
    if($uid == 0){
        $userScoreLog['uid'] = uid();
    }else{
        $userScoreLog['uid'] = $uid;
    }
    $userScoreLog['score'] = $score;
    $userScoreLog['type'] = $type;
    $userScoreLog['create_time'] = time();
    if($tipContent != null){
        if ($score > 0) {
            $userScoreLog['content'] = date('Y-m-d', $userScoreLog['create_time']) . $tipContent."获得" . $score . "积分";
        }else{
            $userScoreLog['content'] = date('Y-m-d', $userScoreLog['create_time']) . $tipContent."扣除" . $score . "积分";
        }
    }else {
        if ($score > 0) {
            if ($type == 1) {
                $userScoreLog['content'] = date('Y-m-d', $userScoreLog['create_time']) . "于“四个机制”获得" . $score . "积分";
            } elseif ($type == 2) {
                $userScoreLog['content'] = date('Y-m-d', $userScoreLog['create_time']) . "于“群团组织”获得" . $score . "积分";
            } elseif ($type == 3) {
                $userScoreLog['content'] = date('Y-m-d', $userScoreLog['create_time']) . "于“党员发展”获得" . $score . "积分";
            } elseif ($type == 4) {
                $userScoreLog['content'] = date('Y-m-d', $userScoreLog['create_time']) . "于“党员服务”获得" . $score . "积分";
            } elseif ($type == 5) {
                $userScoreLog['content'] = date('Y-m-d', $userScoreLog['create_time']) . "于“学习交流”获得" . $score . "积分";
            } elseif ($type == 6) {
                $userScoreLog['content'] = date('Y-m-d', $userScoreLog['create_time']) . "于“活动参与”获得" . $score . "积分";
            } elseif ($type == 7) {
                $userScoreLog['content'] = date('Y-m-d', $userScoreLog['create_time']) . "于“签到平台”获得" . $score . "积分";
            } elseif ($type == 8) {
                $userScoreLog['content'] = date('Y-m-d', $userScoreLog['create_time']) . "“绩效”增加" . $score . "积分";
            }
        } else {
            if ($type == 1) {
                $userScoreLog['content'] = date('Y-m-d', $userScoreLog['create_time']) . "于“四个机制”扣除" . (0-$score) . "积分";
            } elseif ($type == 2) {
                $userScoreLog['content'] = date('Y-m-d', $userScoreLog['create_time']) . "于“群团组织”扣除" . (0-$score) . "积分";
            } elseif ($type == 3) {
                $userScoreLog['content'] = date('Y-m-d', $userScoreLog['create_time']) . "于“党员发展”扣除" . (0-$score) . "积分";
            } elseif ($type == 4) {
                $userScoreLog['content'] = date('Y-m-d', $userScoreLog['create_time']) . "于“党员服务”扣除" . (0-$score) . "积分";
            } elseif ($type == 5) {
                $userScoreLog['content'] = date('Y-m-d', $userScoreLog['create_time']) . "于“学习交流”扣除" . (0-$score) . "积分";
            } elseif ($type == 6) {
                $userScoreLog['content'] = date('Y-m-d', $userScoreLog['create_time']) . "于“活动参与”扣除" . (0-$score) . "积分";
            } elseif ($type == 7) {
                $userScoreLog['content'] = date('Y-m-d', $userScoreLog['create_time']) . "于“签到平台”扣除" . (0-$score) . "积分";
            } elseif ($type == 8) {
                $userScoreLog['content'] = date('Y-m-d', $userScoreLog['create_time']) . "“绩效”扣除" . (0-$score) . "积分";
            }
        }
    }
    D('UserScoreLog')->add($userScoreLog);
}

/**
 * @param $score
 * @param int $type 预留字段
 */
function create_cost_score_log($score, $type=0, $uid=0){
    // 生成积分记录
    if($uid == 0){
        $userCostScoreLog['uid'] = uid();
    }else{
        $userCostScoreLog['uid'] = $uid;
    }
    // 生成积分记录
    $userCostScoreLog['score'] = $score;
    $userCostScoreLog['type'] = $type;
    $userCostScoreLog['create_time'] = time();
    if($score > 0){
        $userCostScoreLog['content'] = date('Y-m-d',$userCostScoreLog['create_time']) ."获得".$score."积分";
    }else{
        $userCostScoreLog['content'] = date('Y-m-d',$userCostScoreLog['create_time']) ."扣除".(0-$score)."积分";
    }

    D('UserCostScoreLog')->add($userCostScoreLog);
}


function is_weixin(){
    if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
        return true;
    }
    return false;
}

function get_head_url($url, $pre=''){


    if ($url) {
        if (strpos($url, 'http:')>-1 || strpos($url, 'https:') >-1) {
            return $url;
        } elseif (strpos($url, '/Uploads')>-1) {
            return __ROOT__ . '' . $url;
        } elseif (strpos($url, 'Uploads')>-1) {
            return __ROOT__ . '/' . $url;
        }
        return __ROOT__ . '/Uploads/' . $url;
    } else {
        return __ROOT__ . '/Public/Common/img/common-head.png';
    }
}

/**
 * 显示图片
 * @param $str
 * @param $size 头像尺寸 默认空 middle中  small 小
 * @return string
 */
function show_img($str, $size)
{
    if ($str) {
        if (strpos($str, 'http:')>-1 || strpos($str, 'https:')>-1) {
            return $str;
        } elseif (strpos($str, 'paint/')>-1) {
            return __ROOT__ . '/Uploads' . $str;
        } elseif (strpos($str, '/Uploads/')>-1) {
            if(strpos($str, '/Uploads/newtz')>-1){
                return  $str;
            }
            elseif ($size == 'middle') {
                return __ROOT__ . '' . $str . '-m.png';
            } elseif ($size == 'small') {
                return __ROOT__ . '' . $str . '-s.png';
            } else {
                return __ROOT__ . '' . $str;
            }
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
        return __ROOT__ . '/Public/Common/img/common.png';
    }
}

function show_article_img($str, $size)
{
    if ($str) {
        if (strpos($str, 'http:') || strpos($str, 'https:')) {
            return $str;
        } elseif (strpos($str, 'paint/')) {
            return __ROOT__ . '/Uploads' . $str;
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
        return '';
    }
}

/**
 * @param $str 图片路径地址
 * @param $size 头像尺寸 默认空 middle中  small 小
 * @return string
 */
function head_icon($str, $size)
{
    if ($str) {

        if (strpos($str, 'http:') || strpos($str, 'https:')) {
            return $str;
        } else {
            if ($size == 'middle') {
                return __ROOT__ . '/Uploads' . $str . '-m.png';
            } elseif ($size == 'small') {
                return __ROOT__ . '/Uploads' . $str . '-s.png';
            } else {
                return __ROOT__ . '/Uploads' . $str;
            }
        }
    } else {
        return __ROOT__ . '/Public/Common/img/head-icon.jpg';
    }
}

/**
 * @param $time 问题更新的时间
 * @return int
 * */
function time_to_string_ques($time){
    $timeSpace = time() - $time;
    $minute =60; 
    $hour =60 *60;
    switch($timeSpace){
        case $timeSpace <60:
            return "刚刚...";break;
        case $timeSpace/$minute <60:
            return floor($timeSpace /$minute)."分钟前";break;
        case $timeSpace/$hour <24:
            return floor($timeSpace /$hour)."小时前";break;
        case $timeSpace/$hour <48:
            return "昨天".date("H:i", $time);break;
        default:
            if(date("Y", $time) ==date("Y")){
                return date("m-d H:i", $time);
            }else {
                return date("Y-m-d H:i", $time);
            }
    }
}

function show_date($time, $pre = ""){
    if($time){
        return $pre.date("Y-m-d h:i", $time);
    }else{
        return "";
    }
}


/***
 * @param $dir
 * @return bool
 * 递归创建文件夹
 */
function mkDirs($dir){
    if(!is_dir($dir)){
        if(!mkDirs(dirname($dir))){
            return false;
        }
        if(!mkdir($dir,0777)){
            return false;
        }
    }
    return true;
}

/*****模板加法***/
function template_add($a, $b)
{
    echo((int)$a + (int)$b);
}

/*****模板减法法***/
function template_sub($a, $b)
{
    return ((int)$a - (int)$b);
}

/*****模板成法***/
function template_multiply($a, $b)
{
    echo((int)$a * (int)$b);
}

/******百分比******/
function template_percent($per, $total)
{
    if(!$per || !$total)
        return '0%';
    return round(($per / $total) * 100) . '%';
}



function get_sex($sex){
    if($sex == null || $sex == ''){
        return "";
    }elseif($sex == 0){
        return "女";
    }else {
        return "男";
    }
}

//显示子标题
function show_sub_title($content, $size=50){
    $start = 0;
    $charset = "utf-8";
    $suffix = true;
    $content =  strip_tags($content);
    if (function_exists("mb_substr")) {
        $slice = mb_substr($content, $start, $size, $charset);
    } elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($content, $start, $size, $charset);
    } else {
        $re['utf-8'] = "/[x01-x7f]|[xc2-xdf][x80-xbf]|[xe0-xef][x80-xbf]{2}|[xf0-xff][x80-xbf]{3}/";
        $re['gb2312'] = "/[x01-x7f]|[xb0-xf7][xa0-xfe]/";
        $re['gbk'] = "/[x01-x7f]|[x81-xfe][x40-xfe]/";
        $re['big5'] = "/[x01-x7f]|[x81-xfe]([x40-x7e]|xa1-xfe])/";
        preg_match_all($re[$charset], $content, $match);
        $slice = join("", array_slice($match[0], $start, $size));
    }
    $fix = '';
    if (strlen($slice) < strlen($content)) {
        $fix = '...';
    }
    return $suffix ? $slice . $fix : $slice;
}
/**
 * 1、生成订单，如：返回中order 含有id和唯一标识out_trade_no
 * $order = make_order($uid, $total_fee='0', $body='订单描述', $title="订单标题");
 *
 * 2、生成需求订单，订单中的 order_id = order['id']  对应指向 参考 party_fee_user
 * $party_fee_user = array(
        'order_id'=>$order['id'],
        'fee'..........
 * )
 * D('PartyFeeUser')->add($party_fee_user);
 *
 * 3、支付连接 参考/Home/View/Cost/lists.html 如:href="__ROOT__/Weixin/Weixinpay/pay?state={$item.out_trade_no}"
 * 生成上面的数据后直接跳转链接  __ROOT__/Weixin/Weixinpay/pay?state={$item.out_trade_no} 就可以了
 *
 * 4、支付成功后PayOrder 下字段 status变为1
 * 通过视图模型可以查看支付状态，参考 Home/Model/PartyFeeUserViewModel.class.php
 *  'PayOrder' => array('out_trade_no','status'=>'order_status', '_on'=>'PayOrder.id=PartyFeeUser.order_id', '_type'=>'LEFT'),
 *
 * 注意：请不要直接修改订单价格，修改价格时make_order创建新的订单，保存新的order_id就可以了，参考Admin/Controller/CostController下的ajaxSaveInfoUser
 *
 * 创建订单
 * @param $uid 创建订单用户的uid
 * @param string $total_fee 订单金额(最多小数点后两位)
 * @param string $body 订单描述
 * @param string $title 订单标题
 * @param int $product_id 可以忽略 不用填写
 * $type COST|党费  DONATION|捐款
 * @return array(
 * 'id'=>id
'out_trade_no'=>$out_trade_no,
'total_fee'=>$total_fee,
'status'=>0,
'create_time'=>time(),
'body'=>$body,
'uid'=>$uid,
'title'=>$title,
'product_id'=>$product_id,
)
 */
function make_order($uid, $total_fee='0', $body='订单描述', $title="订单标题" ,$product_id=1)
{
    $total_fee = intval($total_fee*100);
    $total_fee = $total_fee/100;
    if($total_fee <= 0){
        return false;
    }
    $out_trade_no = getMillisecond().''.$product_id. ''.rand(1000, 9000). ''.rand(10, 99);
    $order = array(
        'out_trade_no'=>$out_trade_no,
        'total_fee'=>$total_fee,
        'status'=>0,
        'create_time'=>time(),
        'body'=>$body,
        'uid'=>$uid,
        'title'=>$title,
        'product_id'=>$product_id,
    );
    $id = D('PayOrder')->add(
        $order
    );
    $order['id'] = $id;
    return $order;
}
function order_save_resoure($order_id, $type='',$resoure_id)
{
    D('PayOrder')->where(array('id'=>$order_id))->save(
        array('type'=>$type,'resoure_id'=>$resoure_id)
    );
}

function getMillisecond() {
    list($t1, $t2) = explode(' ', microtime());
    return sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
}

//显示支部信息
function  show_branch_auth($item, $pre=''){
    $auth_type = $item[$pre.'auth_type'];
    if($auth_type == 1){
        return '市局';
    }
    if($auth_type == 2){//总局
        return $item['branch_hq_name'];
    }
    if($auth_type == 3){//总局
        return $item['branch_name'];
    }
}

/**截取中文字符串
 * @param $str 传入字符
 * @param int $start 开始位置 可不填
 * @param $length 长度 可不填
 * @param string $charset 可不填
 * @param bool|true $suffix可不填
 * @return string
 */
function sub_str_text($str, $start = 0, $length=50, $charset = "utf-8", $suffix = true)
{
	
	$s1='<style';
	$s2='</style>';
	$i1=strpos($str,$s1);//开始位置
	$i2=strpos($str,$s2);//结束位置
	if ($i1!==false && $i2!==false)//找到
		$str=substr($str,0,$i1-1) . substr($str,$i2+strlen($s2));
    $str = strip_tags($str);
    if (function_exists("mb_substr")) {
        $slice = mb_substr($str, $start, $length, $charset);
    } elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
    } else {
        $re['utf-8'] = "/[x01-x7f]|[xc2-xdf][x80-xbf]|[xe0-xef][x80-xbf]{2}|[xf0-xff][x80-xbf]{3}/";
        $re['gb2312'] = "/[x01-x7f]|[xb0-xf7][xa0-xfe]/";
        $re['gbk'] = "/[x01-x7f]|[x81-xfe][x40-xfe]/";
        $re['big5'] = "/[x01-x7f]|[x81-xfe]([x40-x7e]|xa1-xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    $fix = '';
    if (strlen($slice) < strlen($str)) {
        $fix = '...';
    }
    return $suffix ? $slice . $fix : $slice;
}

/**
 * 检查手机号码格式
 * @param $mobile 手机号码
 */
function check_mobile($mobile){
    if(preg_match('/1[34578]\d{9}$/',$mobile))
        return true;
    return false;
}



function verify_sms_code($mobile, $code){
    $session_id = session_id();
    $data = D('SmsLog')->where(array('session_id'=>$session_id, 'mobile'=>$mobile,'code'=>$code,'status'=>0))->order('id desc')->find();
    if($data){
        //判读验证码是否超时,10分钟
        if(time() - $data['create_time'] > 10 *60){
            ajaxMsg(1,"验证码已失效");
        }else{
            D('SmsLog')->where(array('session_id'=>$session_id, 'mobile'=>$mobile,'code'=>$code,'status'=>1))->save(array('status'=>1));
            ajaxMsg(0,"验证成功");
        }
    }else{
        ajaxMsg(1,"请填写正确的验证码");
    }
}


function send_sms_code($mobile){
    if(!check_mobile($mobile)){
        ajaxMsg(1,"请输入正确的手机格式");
    }
    $session_id = session_id();
    $timeOut = time() - 60;//10分钟内有效
    $data = D('SmsLog')->where(array('session_id'=>$session_id, 'mobile'=>$mobile,'create_time'=>array('gt', $timeOut)))->order('id desc')->find();
    if($data && $data['create_time'] < $timeOut){
        ajaxMsg(1,"60秒内不能重复发送");
    }


    $code = rand(1000,9999);

    $send = sendSMS($mobile, $code);
    if($send){
        D('SmsLog')->add(array('session_id'=>$session_id, 'mobile'=>$mobile,'create_time'=>time(),'code',$code));
        ajaxMsg(0,"验证码已发送,请注意查收");
    }else{
        ajaxMsg(1,"验证码发送失败");
    }


}

//    /**
//     * 发送短信
//     * @param $mobile  手机号码
//     * @param $code    验证码
//     * @return bool    短信发送成功返回true失败返回false
//     */
function send_sms($mobile, $sms_templateCode, $sms_content)
{
    //时区设置：亚洲/上海
    date_default_timezone_set('Asia/Shanghai');
    //这个是你下面实例化的类
    vendor('Alidayu.TopClient');
    //这个是topClient 里面需要实例化一个类所以我们也要加载 不然会报错
    vendor('Alidayu.ResultSet');
    //这个是成功后返回的信息文件
    vendor('Alidayu.RequestCheckUtil');
    //这个是错误信息返回的一个php文件
    vendor('Alidayu.TopLogger');
    //这个也是你下面示例的类
    vendor('Alidayu.AlibabaAliqinFcSmsNumSendRequest');

    $c = new \TopClient;
    $config =  C('ALIDAYU_CONFIG');
    //短信内容：公司名/名牌名/产品名
    $product = $config['sms_product'];
    //App Key的值 这个在开发者控制台的应用管理点击你添加过的应用就有了
    $c->appkey = $config['sms_appkey'];
    //App Secret的值也是在哪里一起的 你点击查看就有了
    $c->secretKey = $config['sms_secretKey'];
    //这个是用户名记录那个用户操作
    $req = new \AlibabaAliqinFcSmsNumSendRequest();
    //代理人编号 可选
//    $req->setExtend("123456");
    //短信类型 此处默认 不用修改
    $req->setSmsType("normal");
    //短信签名 必须
    $req->setSmsFreeSignName("智慧建设");
    //短信模板 必须
//    $req->setSmsParam("{\"code\":\"$code\",\"product\":\"$product\"}");
    $paramStr = to_json_string($sms_content);

    $req->setSmsParam("$paramStr");
    //短信接收号码 支持单个或多个手机号码，传入号码为11位手机号码，不能加0或+86。群发短信需传入多个号码，以英文逗号分隔，
    $req->setRecNum("$mobile");
    //短信模板ID，传入的模板必须是在阿里大鱼“管理中心-短信模板管理”中的可用模板。
    $req->setSmsTemplateCode($sms_templateCode); // templateCode

    $c->format='json';
    //发送短信
    $resp = $c->execute($req);
    //短信发送成功返回True，失败返回false
    //if (!$resp)
//    exit(json_encode(array('status'=>-1,'msg'=>json_encode($resp, JSON_UNESCAPED_UNICODE)), JSON_UNESCAPED_UNICODE));
    $session_id = session_id();
    D('SmsLog')->add(array('session_id'=>$session_id, 'mobile'=>$mobile,'create_time'=>time(),'sms_templateCode'=>$sms_templateCode,'sms_content'=>to_json_string($sms_content)));
    if ($resp && $resp->result)   // if($resp->result->success == true)
    {
        return true;
    }
    else
    {
        return false;
    }
}
//    /**
//     * 发送短信
//     * @param $mobile  手机号码
//     * @param $code    验证码
//     * @return bool    短信发送成功返回true失败返回false
//     */
function sendSMS($mobile, $code)
{
    //时区设置：亚洲/上海
    date_default_timezone_set('Asia/Shanghai');
    //这个是你下面实例化的类
    vendor('Alidayu.TopClient');
    //这个是topClient 里面需要实例化一个类所以我们也要加载 不然会报错
    vendor('Alidayu.ResultSet');
    //这个是成功后返回的信息文件
    vendor('Alidayu.RequestCheckUtil');
    //这个是错误信息返回的一个php文件
    vendor('Alidayu.TopLogger');
    //这个也是你下面示例的类
    vendor('Alidayu.AlibabaAliqinFcSmsNumSendRequest');

    $c = new \TopClient;
    $config =  C('ALIDAYU_CONFIG');
    //短信内容：公司名/名牌名/产品名
    $product = $config['sms_product'];
    //App Key的值 这个在开发者控制台的应用管理点击你添加过的应用就有了
    $c->appkey = $config['sms_appkey'];
    //App Secret的值也是在哪里一起的 你点击查看就有了
    $c->secretKey = $config['sms_secretKey'];
    //这个是用户名记录那个用户操作
    $req = new \AlibabaAliqinFcSmsNumSendRequest();
    //代理人编号 可选
//    $req->setExtend("123456");
    //短信类型 此处默认 不用修改
    $req->setSmsType("normal");
    //短信签名 必须
    $req->setSmsFreeSignName("智慧建设");
    //短信模板 必须
//    $req->setSmsParam("{\"code\":\"$code\",\"product\":\"$product\"}");
    $req->setSmsParam("{\"code\":\"$code\"}");
    //短信接收号码 支持单个或多个手机号码，传入号码为11位手机号码，不能加0或+86。群发短信需传入多个号码，以英文逗号分隔，
    $req->setRecNum("$mobile");
    //短信模板ID，传入的模板必须是在阿里大鱼“管理中心-短信模板管理”中的可用模板。
    $req->setSmsTemplateCode($config['sms_templateCode']); // templateCode

    $c->format='json';
    //发送短信
    $resp = $c->execute($req);
    //短信发送成功返回True，失败返回false
    //if (!$resp)
//    exit(json_encode(array('status'=>-1,'msg'=>json_encode($resp, JSON_UNESCAPED_UNICODE)), JSON_UNESCAPED_UNICODE));

    exit(to_json_string($resp));
    if ($resp && $resp->result)   // if($resp->result->success == true)
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 * @param $user 操作人
 * @param $target_user 操作用户
 * @param $type 类型 0转出 1调入 2内部调动
 * @param $content 显示内容
 */

/**
 * @param $user
 * @param $type
 * @param $branch_id 调出 调入 branchid
 * @param $content array()
 * @param $admin
 * @param $auth_type
 * @return bool
 */
function relation_change_log($user, $type, $branch_id, $content=array(),$admin){
    if($type == 0){//如果是调出，
        if(!$user['branch_id']){//如果没有所属支部
            return false;
        }
        $out = $content['out'];
        $reason = $content['reason'];

        $post_name = $content['post_name'];
        $role_name = $content['role_name'];
        $org_branch_name = $content['org_branch_name'];
        $content = "";
        if($org_branch_name){//原支部
            $content = $content."原支部：$org_branch_name<br>";
        }
        if($post_name){//原职务
            $content = $content."原职务：$post_name<br>";
        }
        if($role_name){//原角色
            $content = $content."原角色：$role_name<br>";
        }
        $content = $content."去处：$out<br>原因：$reason";
    }elseif($type == 1){
        if($user['branch_id'] > 0){//如果已经有所属支部
            return false;
        }
        $in = $content['in'];
        $reason = $content['reason'];

        $branch_name = $content['branch_name'];
        $content = "";
        if($branch_name){//现支部
            $content = $content."调入支部：$branch_name<br>";
        }
        if($in || $reason)
        $content = $content."来处：$in<br>原因：$reason";
    }elseif($type == 2){
        if(!$admin && $user['post_id']){//该用户有职务
            return false;
        }
        $post_name = $content['post_name'];
        $role_name = $content['role_name'];
        $org_branch_name = $content['org_branch_name'];
        $branch_name = $content['branch_name'];
        $content = "";
        if($org_branch_name){//原支部
            $content = $content."原支部：$org_branch_name<br>";
        }
        if($branch_name){//现支部
            $content = $content."现支部：$branch_name<br>";
        }
        if($post_name){//原职务
            $content = $content."原职务：$post_name<br>";
        }
        if($role_name){//原角色
            $content = $content."原角色：$role_name<br>";
        }
    }else{
        return false;
    }

    $array = array('target_uid'=>$user['uid'],'type'=>$type,'content'=>$content,'create_time'=>time(),'org_branch_id'=>$user['branch_id']);
    $array['uid'] = $admin?$admin['uid']:$user['uid'];
    $array['auth_type'] = 3;

    $array['branch_id'] = $branch_id;
    if($user[$branch_id] != $branch_id)
    D('PartyBranchRelationLog')->add($array);

    if($type == 0){//如果是调出，
        D('User')->where(array('uid'=>$user['uid']))->save(array('branch_id'=>null,'branch_hq_id'=>null,'role_id'=>5,'post_id'=>null,'update_time'=>time()));
        return true;
    }elseif($type == 1){
        D('User')->where(array('uid'=>$user['uid']))->save(array('branch_id'=>$branch_id,'branch_hq_id'=>null,'role_id'=>$user['role_id'],'post_id'=>null,'update_time'=>time()));
        return true;
    } elseif($type == 2){
        if($user[$branch_id] != $branch_id)
        D('User')->where(array('uid'=>$user['uid']))->save(array('branch_id'=>$branch_id,'branch_hq_id'=>$user['branch_hq_id'],'role_id'=>$user['role_id'],'post_id'=>$user['post_id'], 'update_time'=>time()));
        return true;
    }

}



function downloadWxImg($base_img,$path)
{


    $tzdate =   date('Y-m-d',time());
    $path = $path.'/'.$tzdate.'/';

    //先判断路径是否存在
    if(!is_dir($path))
    {
        //不存在就创建
        if(!mkDirs($path)){
            return array('message'=>'创建目录失败','flag'=>false);
        }

    }
//    $base_img = str_replace('data:image/jpeg;base64,', '', $base_img);
//  设置文件路径和文件前缀名称
    $prefix = 'tz_';
    $output_file = $prefix . time() . rand(100, 999) . '.jpg';
    $path = $path . $output_file;
//  创建将数据流文件写入我们创建的文件内容中
    file_put_contents($path, base64_decode($base_img));
    return array('message'=>'创建成功','flag'=>true,'path'=>$path);
    //file_put_contents($path, base64_decode($base_img));
// 输出文件
//    print_r($output_file);
}

/**
 * 制作图片一套流程
 * 
 * @return [type] [description]
 */
function makePic($imgarr=array(),$path,$prefix='',$thumb=false){
    if(!$imgarr){
        return array('message' =>'error' ,'status' =>1001);
    }
    foreach($imgarr as $k=>$v){
                $res = downloadWxImg($v,$path,$prefix);//记得要给权限创建文件夹
                // file_put_contents('./wx3.txt',var_export($res,true));
                if($res['flag']){
                    $or_path = $res['path'];
                    //可能要判断图片大小才决定是否生成缩略图
                    //还要生成缩略图
                    if($humb){
                       $image = new \Think\Image();
                    $start_str = substr($or_path,0,strlen($or_path)-4);
                    $last_str = substr($or_path,-4,4);
                    $thumb_path = $start_str.'_thumb'.$last_str;
                    $image->open($or_path);
                    $image->thumb(240, 250,\Think\Image::IMAGE_THUMB_CENTER)->save($thumb_path);  
                    }
                   

                    //这里是存入数据库的操作，去除 ./uploads 前的那个点。
                    $tzpath= substr($res['path'],1,strlen($res['path']));
                    $or_path_arr[] = $tzpath;
                }
                else{
                    return $res;
                }
            }
            return $data = array('message' =>'success' ,'status' =>1000,'data' =>$or_path_arr );
}

//生成一年中的所有周数的开始日期和结束日期，计算多少个周
function get_week($year) { 
    $year_start = $year . "-01-01"; 
    $year_end = $year . "-12-31"; 
    $startday = strtotime($year_start); 
    if (intval(date('N', $startday)) != '1') { 
        $startday = strtotime("next monday", strtotime($year_start)); //获取年第一周的日期 
    } 
    $year_mondy = date("Y-m-d", $startday); //获取年第一周的日期 
 
    $endday = strtotime($year_end); 
    if (intval(date('W', $endday)) == '7') { 
        $endday = strtotime("last sunday", strtotime($year_end)); 
    } 
 
    $num = intval(date('W', $endday)); 
    for ($i = 1; $i <= $num; $i++) { 
        $j = $i -1; 
        $start_date = date("Y-m-d", strtotime("$year_mondy $j week ")); 
 
        $end_day = date("Y-m-d", strtotime("$start_date +6 day")); 
 
        $week_array[$i] = array (
            $start_date ,
         $end_day); 
    } 
    return $week_array; 
} 
function getThumb($img){
    $start_str = substr($img,0,strlen($img)-4);
    $last_str = substr($img,-4,4);
    return $start_str.'_thumb'.$last_str;
}
/**
 * 获取企业微信的access_token
 * @param $appID
 * @param $appsecret
 * @return bool
 */
function get_qytoken($appID,$appsecret){
    //如果tokken失效
    //'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxa6848e496395a341&secret=Jy1AQE_ej2me7iVGms_eGn3tTMcwUnaH_9f867eEmcJtmEltn0x1_B
    $url = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid='.$appID.'&corpsecret='.$appsecret;
   // https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=wxa6848e496395a341&corpsecret=Jy1AQE_ej2me7iVGms_eGn3tTMcwUnaH_9f867eEmcJtmEltn0x1_B-NzQoeqHn0
    $return_msg = http_curl($url);

    if(isset($return_msg['access_token'])){
        return $return_msg['access_token'];
    }
    else return false;

}

/**
 * 获取企业微信的jssdk签名需要的ticket
 * @param $access_token
 * @return bool
 */
function get_qyticket($access_token){

    $ticket = 'https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token='.$access_token;
    $ticket_return = http_curl($ticket);
    if(isset($ticket_return['ticket'])){
        return $ticket_return['ticket'];
    }
    else return false;
    //var_dump($ticket_return);
    // return $ticket_return['ticket'];
}

/**
 * 微信加密签名方法
 * @param $ticket
 * @param $time
 * @param string $noncestr
 * @param $url
 * @return string
 */
function makeSignature($ticket,$time,$noncestr='agdgsdfsfrerewrw',$url){

    $js_ticket = $ticket;//存到缓存里
    $timestamp =$time;//要一起存到缓存里
    $noncestr= $noncestr;
    $url=$url;
    $string = 'jsapi_ticket='.$js_ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;
////生成签名 8618150f27d353be96993a165b4eddcc84bc55b2
////  appId: '', // 必填，公众号的唯一标识
////   timestamp: , // 必填，生成签名的时间戳
////   nonceStr: '', // 必填，生成签名的随机串
////  signature: '',// 必填，签名，见附录1
    $signature = sha1($string);

    return $signature;
}

/**
 * http_curl
 * @param $url
 * @param string $type
 * @param string $res
 * @param string $arr
 * @return mixed
 */
function http_curl($url,$type='get',$res='json',$arr=''){
    /*
    $url 请求的url
    $type 请求类型
    $res 返回数据类型
    $arr post请求参数
     */

    $ch=curl_init();

    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $output = curl_exec($ch);
    file_put_contents('./weixinerror.txt',$url);
    if($output === false)
    {
        echo 'Curl error22: ' . curl_error($ch);
    }
    else
    {
        curl_close($ch);
        if($res=='json'){
            return json_decode($output,true);
        }
    }
}
function curPageURL()
{
    $pageURL = 'http';

    if ($_SERVER["HTTPS"] == "on")
    {
        $pageURL .= "s";
    }
    $pageURL .= "://";

    if ($_SERVER["SERVER_PORT"] != "80")
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    }
    else
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}


