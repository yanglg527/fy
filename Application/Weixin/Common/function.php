<?php

/**
 * Created by PhpStorm.
 * User: jervis
 * Date: 16/5/12
 * Time: 下午8:00
 */

/**
 * GET 请求
 * @param string $url
 */
function wx_http_get($url)
{
        $oCurl = curl_init();
    if (stripos($url, "https://") !== false) {
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_TIMEOUT, 40);   //只需要设置一个秒的数量就可以
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return false;
    }
}
function wx_http_post($url, $data)
{
    $oCurl = curl_init();
    if (stripos($url, "https://") !== false) {
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_TIMEOUT, 30);   //只需要设置一个秒的数量就可以
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_POST, 1);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, $data);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return false;
    }
}
/**
 * @param $corpID
 * @param $secret
 * {
 * "access_token": "accesstoken000001",
 * "expires_in": 7200
 * "updateTime":time()
 * }
 */
function get_qy_access_token($corpID, $secret)
{
   // file_put_contents('./get_qy_access_token.txt','fff');
    $token = D('WeixinConfig')->where("name='qy_access_token'")->find();
    if ($token) {
        $jsonToken = to_json_obj($token['value']);
     //   file_put_contents('./testqytoken.txt',var_export($jsonToken,true));
        //access_token未失效
        if ($jsonToken['updateTime'] && (time() - $jsonToken['updateTime'] < ($jsonToken['expires_in'] - 10))) {
            return $jsonToken['access_token'];
        } else {//获取 accessToken
            return _reget_qy_access_token($corpID, $secret);
        }
    } else {
        return false;
    }
}

function _reget_qy_access_token($corpID, $secret)
{
    $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$corpID&corpsecret=$secret";
    $result = wx_http_get($url);
    $jsonToken = to_json_obj($result);
   // file_put_contents('./testreget.txt',var_export($jsonToken,true));
    if ($jsonToken['access_token']) {
        S('web_access_token_' . $corpID, $jsonToken['access_token'], $jsonToken['expires_in'] - 10);

//        $jsonToken['updateTime'] = time();
//        $token['value'] = to_json_string($jsonToken);
//        D('WeixinConfig')->where("name='qy_access_token'")->save($token);
        return $jsonToken['access_token'];
    } else {
        return false;
    }
}

/***
 * @param $corpID
 * @param $secret
 * @param $code
 * @return bool
 * 返回 {"UserId":} or {"OpenId"}
 */
function get_qy_user($corpID, $secret, $code, $type = 'wx')
{
    $accessToken = get_qy_access_token($corpID, $secret);
    if ($accessToken) {
       // file_put_contents('./textuser1.txt','fffaqweqweqweqw');
        if ($type == 'web') {
            $url = "https://qyapi.weixin.qq.com/cgi-bin/service/get_login_info?access_token=$accessToken&code";
            $result = wx_http_post($url, to_json_string(array("auth_code" => $code)));
            $info = to_json_obj($result);

            if ($info['user_info']) {
                return $info['user_info'];
            } else {
                return false;
            }
        } else {
          //  file_put_contents('./textuser.txt','fffaqweqweqweqw');
            $url = "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=$accessToken&code=$code";
            $result = wx_http_get($url);

            $info = to_json_obj($result);
     //   file_put_contents('./test2.txt',var_export($info,true));
            if ($info['UserId']) {
                return $info;
            } elseif ($info['OpenId']) {//如果是非企业用户
                return $info;
            } else {
                $accessToken = _reget_qy_access_token($corpID, $secret);
                $url = "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=$accessToken&code=$code";
                $result = wx_http_get($url);
                $info = to_json_obj($result);
                if ($info['UserId']) {
                    return $info;
                } elseif ($info['OpenId']) {//如果是非企业用户
                    return $info;
                } else {
                    return false;
                }
            }
        }

    } else {
        return false;
    }

}

function get_qy_departments($corpID, $secret)
{
    $accessToken = get_qy_access_token($corpID, $secret);
    if ($accessToken) {
        $url = "https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token=$accessToken";
        $result = wx_http_get($url);
//        echo $accessToken;
//        echo $result;
//        echo "<br/>";
        $info = to_json_obj($result);
   //     file_put_contents('./test2.txt',var_export($info,true));
        if ($info['errcode'] == 0) {//获取成功
            return $info;
        } else {//获取失败，更新 token
            $accessToken = _reget_qy_access_token($corpID, $secret);
            $url = "https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token=$accessToken";
            $result = wx_http_get($url);
            $info = to_json_obj($result);
            if ($info['errcode'] == 0) {
                return $info;
            } else {
                return false;
            }
        }
    } else {
        return false;
    }

}
function get_qy_users_by_departmentid($corpID, $secret, $departmentid)
{
    $accessToken = get_qy_access_token($corpID, $secret);
    if ($accessToken) {
        $url = "https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token=$accessToken&department_id=$departmentid&fetch_child=0&status=0";
        $result = wx_http_get($url);
//        echo $result;
//        echo "<br/>";
        $info = to_json_obj($result);
        if ($info['errcode'] == 0) {//获取成功
            return $info;
        } else {//获取失败，更新 token
            $accessToken = _reget_qy_access_token($corpID, $secret);
            $url = "https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token=$accessToken&department_id=$departmentid&fetch_child=0&status=0";
            $result = wx_http_get($url);
            $info = to_json_obj($result);
            if ($info['errcode'] == 0) {
                return $info;
            } else {
                return false;
            }
        }
    } else {
        return false;
    }

}

//获取企业成员信息
function get_qy_user_info($corpID, $secret, $userId)
{


    $accessToken = get_qy_access_token($corpID, $secret);
    $url = "https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=$accessToken&userid=$userId";
    $result = wx_http_get($url);
    $info = to_json_obj($result);
//    echo $result;
//    echo "<br/>";
    if ($info['errcode'] == 0) {
        return $info;
    } else {
        return false;
    }

}

//获取非企业成员信息
function get_fei_qy_user_info($corpID, $secret, $openId)
{


}


function check_and_register_user_by_qy($corpID, $secret, $code, $tableName, $tableFields)
{
    //获取用户 id 信息
    $user = get_qy_user($corpID, $secret, $code);
    if ($user['UserId']) {//企业成员
        //判断数据库是否有该用户
        $userId = $user['UserId'];
        $userIdField = $tableFields['userid'];
        $user = D($tableName)->where("$userIdField='$userId'")->find();
        if ($user) {//已经注册了,返回用户
            return $user;
        } else {//获取用户信息，注册新用户
            $userInfo = get_qy_user_info($corpID, $secret, $userId);
            if ($userInfo) {

                $sync = D("WeixinConfig")->where("name='sync_version'")->find();
                $sync_version = $sync['value'];
                $user = update_or_add_by_qy($userInfo, $sync_version, null, $tableName, $tableFields);

                return $user;
            } else {
                return false;
            }

        }

    } else {//注册非企业用户
        return false;
//        redirect(__ROOT__ . "/error2.html");

    }

}

function check_web_user_by_qy($UserId='',$tableName, $tableFields)
{
    //获取用户 id 信息
    //$user = get_qy_user($corpID, $secret, $code, 'web');

    if ($UserId) {//企业成员
        //判断数据库是否有该用户
        $userId = $UserId;
        $userIdField = $tableFields['userid'];
        $user = D($tableName)->where("$userIdField='$userId'")->find();
        if ($user) {//已经注册了,返回用户
            return $user;
        } else {
            return false;
        }

    } else {
        return false;

    }

}

function update_all_by_qy($corpID, $secret, $tableName, $tableFields)
{
    $sync = D("WeixinConfig")->where("name='sync_version'")->find();
    $sync_version = $sync['value'];

    $newsync_version = time();
    D("WeixinConfig")->where("name='sync_version'")->save(array('value' => $newsync_version));

    //获取部门列表
    $departments = get_qy_departments($corpID, $secret);

    $userIdField = $tableFields['userid'];

//    echo to_json_string($departments);
//    echo "<br>";
    //遍历部门列表获取成员
    if ($departments) {
        //遍历获取的成员更新信息
        foreach ($departments['department'] as $department) {
            $qy_users = get_qy_users_by_departmentid($corpID, $secret, $department['id']);
//            echo to_json_string($qy_users);
//            echo "<br>";
            foreach ($qy_users['userlist'] as $qy_user) {
                $userId = $qy_user['UserId'] ? $qy_user['UserId'] : $qy_user['userid'];


                $user = D($tableName)->where("$userIdField='$userId'")->find();
                //同步excel数据，暂时的
//              if($qy_user['mobile']){
//                  $user = D($tableName)->where(array('mobile'=>$qy_user['mobile'],'realname'=>$qy_user['name']))->find();
//                    exit(to_json_string($user));
//              }
//                exit(to_json_string($qy_user));
//              ajaxMsg(1,to_json_string($qy_user));
                update_or_add_by_qy($qy_user, $newsync_version, $user, $tableName, $tableFields);
            }
        }
    }
    $syncVersionField = $tableFields['sync_version'];
    $statusField = $tableFields['status'];
    D('User')->where("$syncVersionField<$newsync_version and $userIdField is not null")->save(array("$statusField" => -1));


}

function update_or_add_by_qy($qy_info, $sync_version, $user, $tableName, $tableFields)
{


    $userId = $qy_info['UserId'] ? $qy_info['UserId'] : $qy_info['userid'];
    $mobile = $qy_info['mobile'];
    $email = $qy_info['email'];
    $weixinid = $qy_info['weixinid'];
    $gender = $qy_info['gender'] == 2 ? 0 : ($qy_info['gender'] == 0 ? null : 1);
    $department = $qy_info['department'] ? implode(',', $qy_info['department']) : "";
    $status = $qy_info['status'] == 2 ? 0 : 1;
    $position = $qy_info['position'];
    $avatar = $qy_info['avatar'];
    $realname = $qy_info['name'];
//    $user = D('User')->where("qyuserid='$userId'")->find();
    $user = D('User')->where(array('mobile'=>$mobile,'realname'=>$realname))->find();
    if ($user) {//已经注册了,更新空的字段 和版本号
        $user[$tableFields['mobile'] . ''] = $user['mobile'] ? $user['mobile'] : $mobile;
        $user[$tableFields['email']] = $user['email'] ? $user['email'] : $email;
        $user[$tableFields['sync_time']] = time();
        $user[$tableFields['weixinid']] = $weixinid;
        $user[$tableFields['position']] = $user['position'] ? $user['position'] : $position;
        $user[$tableFields['avatar']] = $user['headimgurl'] ? $user['headimgurl'] : $avatar;
        $user[$tableFields['name']] = $user['realname'] ? $user['realname'] : $realname;
        $user[$tableFields['sync_version']] = $sync_version;
        $user[$tableFields['department']] = $department;
        $user[$tableFields['gender']] = $user['gender'] ? $user['gender'] : $gender;
        $user[$tableFields['status']] = $status;
        $user[$tableFields['userid']] = $userId;
        $idField = $tableFields['id'];
        D($tableName)->where("$idField=" . $user[$idField])->save($user);
        return $user;
    } else {//获取用户信息，注册新用户
        $user = array(
            $tableFields['mobile'] => $mobile,
            $tableFields['email'] => $email,
            $tableFields['sync_time'] => time(),
            $tableFields['create_time'] => time(),
            $tableFields['weixinid'] => $weixinid,
            $tableFields['sync_version'] => $sync_version,
            $tableFields['department'] => $department,
            $tableFields['position'] => $position,
            $tableFields['avatar'] => $avatar,
            $tableFields['name'] => $realname,
            $tableFields['gender'] => $gender,
            $tableFields['userid'] => $userId,
            $tableFields['status'] => $status
        );


        //添加用户,自定义，搬迁需要删除掉
        $user['role_id'] = C('ROLE_NORMAL_ID');
        $user['branch_id'] = null;
        //end 自定义


        //添加用户
        $uid = D($tableName)->add($user);


        //添加权限，自定义，搬迁需要删除掉
//      $normalGroupId = C('NORMAL_GROUP_ID');
        D('admin_auth_group_access')->add(array('uid' => $uid, 'group_id' => 4));
        //end 自定义

        $user['uid'] = $uid;
        return $user;
    }

}

/**
 * @param $corpID
 * @param $secret
 * @param $userid
 * @return bool|mixed
 * {"errcode":0,"errmsg":"ok","openid":"o5IOtsyPCSuT9s8Q52CJjJwrf9Ms"}
 */
function cover_userid_to_openid($corpID, $secret, $userid)
{
    $accessToken = get_qy_access_token($corpID, $secret);
    $url = "https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid?access_token=$accessToken";
//    exit(to_json_string(array("userid"=>$userid)));
    $result = wx_http_post($url, to_json_string(array("userid" => $userid)));
    $info = to_json_obj($result);
//    exit($result);
//    echo $result;
//    echo "<br/>";
    if ($info['errcode'] == 0) {
        return $info;
    } else {
        return false;
    }
}

    function ceshi(){
        $sf='1';
        return $sf;
    }

function curl_get_contents($url, $timeout = 1)
{
    $curlHandle = curl_init();
    curl_setopt($curlHandle, CURLOPT_URL, $url);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlHandle, CURLOPT_TIMEOUT, $timeout);
    $result = curl_exec($curlHandle);
    curl_close($curlHandle);
    return $result;
}
