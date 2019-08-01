<?php


/**
 * 获得文件后缀
 */
if (!function_exists('getFileExtension')) {
    function getFileExtension($file) {
        return array_pop(explode('.', $file));
    }
}
//保存发布信息
function set_save_auth($array){
    $admin = admin();
    $array['uid'] = $admin['uid'];
    $array['auth_type'] = session_auth();
    $array['branch_id'] = $admin['admin_branch_id'];
    $array['branch_hq_id'] = $admin['admin_branch_hq_id'];
    return $array;
}

//保存发布信息
function set_todo_save_auth($array){
    $admin = admin();
    $array['issuer_uid'] = $admin['uid'];
    $array['auth_type'] = session_auth();
    $array['branch_id'] = $admin['admin_branch_id'];
    $array['branch_hq_id'] = $admin['admin_branch_hq_id'];
    return $array;
}

//显示支部信息
function  show_user_branch_auth($item){
    $auth_type = session_auth();
    if($auth_type == 1){
        return '市局';
    }
//    return to_json_string($item);
    if($auth_type == 2){//总局
        return $item['branch_hq_name'];
    }
    if($auth_type == 3){//总局
        return $item['branch_name'];
    }
}

// 1市局 2总局 3支部 0
function session_auth()
{
//    return 3;

    $sessionNames = C('SESSION_CONFIG');
    $adminAuthSsessionName = $sessionNames['ADMIN_AUTH_SESSION'];
    if (session($adminAuthSsessionName)) {

        return session($adminAuthSsessionName);
    }

    $admin = admin();
    //如果是市局
    //获取管理员权限，是否超级管理员
    $adminGroup = D('AdminAuthGroupAccess')->where(array('uid' => $admin['uid']))->find();
    if ($adminGroup['group_id'] == 1) {//如果是超级管理员
        session($adminAuthSsessionName,1);
        return 1;
    }

    //判断是否总局
    if ($adminGroup['group_id'] == 8) {//有总局身份
        session($adminAuthSsessionName,2);
        return 2;
    }

    //判断是否总局
    if ($adminGroup['group_id'] == 4) {//有支部身份
        session($adminAuthSsessionName,3);
        return 3;
    }

    return 0;
}

function checkAuth($branch_id)
{
    $admin = admin();

    //如果是市局
    //获取管理员权限，是否超级管理员
    $auth = session_auth();
    if ($auth == 1) {//如果是超级管理员
        return 1;
    }

    //判断是否总局
    if ($auth == 2) {//有总局身份
        if (!$branch_id) {
            return 2;
        }
        //判断支部是否在总局下
        $branch = D('PartyBranch')->find($branch_id);
        if ($branch['branch_hq_id'] == $admin['admin_branch_hq_id']) {//是在管理下
            return 2;
        }
    }
    if ($auth == 3 && !$branch_id) {
        return 3;
    }

    //判断支局是否一致
    if ($admin['admin_branch_id'] == $branch_id) {
        return 3;
    }
    if (IS_AJAX) {
        ajaxMsg(1, "抱歉，您没有该操作权限");
    } else {
        return 0;
    }
}

function fill_add_before($number)
{
    if ($number > 0) {
        return '+' . $number;
    }
    return $number;
}

function add_one($number)
{
    return $number + 1;
}

//function get_sex($sex){
//    if($sex == 0){
//        return "女";
//    }
//    return "男";
//}

/**
 *
 * 截取中文字符串
 *
 **/
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
{
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

function show_content($content)
{
    $content = strip_tags($content);
    return msubstr($content, 0, 50);

}

function show_outbox($content)
{
    return str_replace(array("<br>"), "\r\n", $content);
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
function admin()
{
    $sessionNames = C('SESSION_CONFIG');
    $adminSsessionName = $sessionNames['ADMIN_SESSION'];
    $user = session($adminSsessionName);

    //测试用的
//    if(!$user){
//        $user = D('UserView')->where("User.uid=48")->find();
//        session('user', $user);
//    }
    //end

    return $user;
}

function admin_uid()
{

    $user = admin();
    if ($user) {
        return $user['uid'];
    }
    return 0;
}


function ui_sidebar($sidebar_module,$match_module,$sidebar_sub,$match_sub,$level = 1){
    if($level == 1){
        if($sidebar_module == $match_module){
            return 'am-in';
        }
        return '';
    }elseif($level == 2){
        if($sidebar_module == $match_module && $sidebar_sub == $match_sub){
            return 'selected';
        }
        return '';
    }
}

function admin_login($uid)
{

    $user = D('UserView')->where("User.uid=$uid")->find();

    $ip = get_ip_address();

    $u = array();
    $u['last_login_ip'] = $ip;
    $u['last_login_time'] = time();
    $user['login_count'] = $user['login_count'] + 1;
    $u['login_count'] = $user['login_count'];
    D('User')->where("uid=$uid")->save($u);

    $sessionNames = C('SESSION_CONFIG');
    $adminSsessionName = $sessionNames['ADMIN_SESSION'];
    $adminAuthSsessionName = $sessionNames['ADMIN_AUTH_SESSION'];
    session($adminSsessionName, $user);
}


function admin_logout()
{
    $sessionNames = C('SESSION_CONFIG');
    $adminSsessionName = $sessionNames['ADMIN_SESSION'];
    $adminAuthSsessionName = $sessionNames['ADMIN_AUTH_SESSION'];
    session($adminAuthSsessionName, null);
    session($adminSsessionName, null);
    session_destroy();
}

/**
 * 添加一条待办事项
 * @param $todo
 * @return todo_id
 */
function addTodo($todo){

    $todo['create_time'] = time();
    $todo['status'] = 1;
    $todoId = D('Todo')->add($todo);

//    ajaxMsg(1,"todoId = ".$todoId);

    $roles = explode(',', $todo['receiver_roles']);
    $branchs = explode(',', $todo['receiver_branchs']);
    foreach ($roles as $item) {
        foreach ($branchs as $branch) {
            $todo_receiver_role['role_id'] = $item;
            $todo_receiver_role['branch_id'] = $branch;
            $todo_receiver_role['todo_id'] = $todoId;
            D('TodoReceiverRole')->add($todo_receiver_role);
        }
    }
    return $todoId;
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
//        //最后合并同一个人的
////        foreach($nndata as &$v){
////            $nndata[$v['name']] =  $v['status'];
////        }
        return $nndata;
    }else{
        return $data;
    }

}

/**
 * gl 党小组
 */
function voteOrder($data,$gl=array()){  //dutyorders  多了一个s
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
       
        foreach ($weiyuan as $c){
            $nndata[] = $c;
        }
        if($gl){
            foreach ($gl as $gv){
                $nndata[] = $gv;
            } 
        }
        foreach ($adm as $b){
            $nndata[] = $b;
        }
        foreach ($zbdy as $h){
            $nndata[] = $h;
        }
        foreach ($member as $d){
            $nndata[] = $d;
        }
//        //最后合并同一个人的
////        foreach($nndata as &$v){
////            $nndata[$v['name']] =  $v['status'];
////        }
        return $nndata;
    }else{
        return $data;
    }

}



/**
 * 当前年份
 */
if (!function_exists('thisYear')) {
  function thisYear()
  {
    return date('Y');
  }
}

/**
 * 上传附件
 * @param  string $savePath   子目录
 * @param  string $fatherName 模块名
 */
// if (!function_exists('uploadAnnex')) {
//     function uploadAnnex($savePath, $fatherName)
//     {
//       $upload = new \Think\Upload();// 实例化上传类
//       $upload->maxSize = 3145728;// 设置附件上传大小
//       $upload->exts = array('jpg', 'gif', 'png', 'jpeg', 'doc', 'pdf', 'xlsx', 'xls');// 设置附件上传类型
//       $upload->rootPath = './Uploads/'; // 设置附件上传根目录
//       $upload->savePath = $savePath.'/'; // 设置附件上传（子）目录
//       $upload->replace = true;
//
//       $file = $_FILES['file'];
//       $info = $upload->uploadOne($file);
//       if (!$info) {
//         ajaxMsg(1, $upload->getError());
//       } else {
//         $path = '/Uploads/'.$info['savepath'].$info['savename'];
//         $data['father_name'] = $fatherName;
//         $data['files_path'] = $path;
//         $data['former_name'] = $info['name'];
//         $data['filetype'] = $info['type'];
//         $_files = D('Files');
//         $res = $_files->insert($data);
//         return $res;
//       }
//     }
// }

/**
 * 试图告诉你这是干嘛的
 * 但很遗憾
 * 我也不知道
 * include_once('Application/Admin/Common/function.php');
 */
if (!function_exists('formatToArray')) {
    function formatToArray($arr, $num, $attend_type, $entity){
      $time = time();
      $array = array();
      // 更改阅读状态
      $status = $attend_type == 2 ? 1 : 0;
      // $arr = explode(',', $string);
      foreach ($arr as $key => $value) {
        $array[$key][$entity] = $num;
        $array[$key]['uid'] = $value;
        $array[$key]['attend_type'] = $attend_type;
        $array[$key]['status'] = $status;
        $array[$key]['create_at'] = $time;
      }
      return $array;
    }
}
