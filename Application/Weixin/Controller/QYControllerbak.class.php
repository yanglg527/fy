<?php

/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Weixin\Controller;

use Common\Controller\BaseController;
use Weixin\Util\QYConfig;
// use Weixin\Util\QYWechat;
use Admin\Util\AdminUtil;
use Weixin\Util\qywechat;

set_time_limit(240);
class QYController extends BaseController
{




//请在Weinxin/Conf/config.php 设置相应信息
    private $corp_id;
    private $domain;
    private $secret;
    private $errorUrl;//不存在该用户，跳转页面，会再参数中?url=urecode(原来的地址)
    private $tableName;//保存信息表
    private $tableFields;//保存信息表
    function _initialize()
    {
        $config = C('WEIXINQY_CONFIG');
        $this->corp_id = $config['CORP_ID'];
        $this->domain = $config['DOMAIN'];
        $this->secret = $config['SECRET'];
        $this->tableName = QYConfig::$tableName;
        $this->tableFields = QYConfig::$tableFields;
        $this->errorUrl = $config['ERROR_URL'];
    }

    /**
     * 网页授权登录  企业号填写信任域名
     * 提供参数 state : 指定跳转页面
     * /Weixin/QY/webAuth?key=index&state=1
     * key:页面定义的值  在weixin_redirect_url 保存对应跳转页面
     * state:用于页面传值(英文|数字)  在 跳转的action获取字段 state
     *
     */
    public function webAuth()
    {
        $key = I('key', 'index');//url 的 key
        $state = I('state', '0');//传值
        $redirect_uri = urlencode("http://$this->domain/Weixin/QY/webRedirect?key=$key");//重定向地址
        $weixinAuth = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->corp_id&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_base&state=$state#wechat_redirect";
        redirect($weixinAuth);//指向微信跳转
    }

    //get {"key":"", "code":"dfadfadf", "state":"dfdf"}
    public function webRedirect()
    {
        $key = I('key', 'index');//url 的 key
        $state = I('state', '');//传值
        $code = I('code');
        if ($code) {
            $uob = D('Weixin_redirect_url')->where(array('key' => $key))->find();
            $url = $uob['url'];
            $re_url = "http://$this->domain/$url?state=$state";

            //请求用户信息
            $user = check_and_register_user_by_qy($this->corp_id, $this->secret, $code, $this->tableName, $this->tableFields);
            if (!$user || $user['status'] != 1) {
                redirect(__ROOT__ . $this->errorUrl . "?url=" . urlencode($re_url));
                exit;
            }

            //提供 登录方法
            login($user['uid']);

            redirect($re_url);
        }
    }

    /**
     * 网页扫一扫授权跳转
     */
    public function webAuthority()
    {

        $config = C('WEIXINQY_CONFIG');
        $corp_id = $config['CORP_ID'];
        $domain = $config['DOMAIN'];
        $secret = $config['SECRET'];
        $AgentId = $config['AGENTID'];
        $redirect_uri = "http://$domain/Weixin/QY/webAuthorityLogin";
      
      //$url = "https://open.weixin.qq.com/connect/qrconnect?appid={$config['CORP_ID']}&redirect_uri=" . urlencode($redirect_uri). "&response_type=code&scope=snsapi_login&state=#wechat_redirect";
     //   $url = "https://qy.weixin.qq.com/cgi-bin/loginpage?corp_id={$config['CORP_ID']}&redirect_uri=" . urlencode($redirect_uri). "&usertype=member"; //state=&
        $url = "https://open.work.weixin.qq.com/wwopen/sso/qrConnect?appid={$config['CORP_ID']}&agentid={$AgentId}&redirect_uri=" . urlencode($redirect_uri) . "&state=STATE";

     
        header('location: ' . $url);

    }

    /**
     * 网页授权登陆
     */
    // public function webAuthorityLogin()
    // {

    //     $code = I('get.auth_code');
    //     file_put_contents('./weblogin.txt', $code);
    //     if ($code) {

    //         $user = check_web_user_by_qy($this->corp_id, $this->secret, $code, $this->tableName, $this->tableFields);
	// 		if($user){
	// 			AdminUtil::login($user['uid']);
	// 			if(AdminUtil::auth() < 1){
	// 				$this->error('抱歉，您没有管理权限，返回登陆', __ROOT__ . "/Admin/Login/index");
    //         		exit();
	// 			}
	//             $url = __ROOT__.'/Admin/Index/index';
	//             header('location: ' . $url);
	// 			exit();
	// 		}else{
	// 			$this->error('抱歉，您没有管理权限，返回登陆', __ROOT__ . "/Admin/Login/index");
	// 			exit();
	// 		}

    //     }
    // }

    /**
     * 网页授权登陆 企业微信扫码
     */
    public function webAuthorityLogin()
    {

        $code = I('code');
     //   file_put_contents('./weblogin.txt', $code);
        if ($code) {

            $access_token = _reget_qy_access_token($this->corp_id, $this->secret);
         //   var_dump($access_token);
            if ($access_token) {
                $url = "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=$access_token&code=$code";
                $result = wx_http_get($url);
                $info = to_json_obj($result);
        //        var_dump($info);
                if ($info['UserId']) {
                    $user = check_web_user_by_qy($info['UserId'], $this->tableName, $this->tableFields);
                    if ($user) {
                        AdminUtil::login($user['uid']);
                        if (AdminUtil::auth() < 1) {
                            $this->error('抱歉，您没有管理权限，返回登陆', __ROOT__ . "/Admin/Login/index");
                            exit();
                        }
                        $url = __ROOT__ . '/Admin/Index/index';
                        header('location: ' . $url);
                        exit();
                    } else {
                     //   echo '没找到用户';
                        $this->error('抱歉，您没有管理权限，返回登陆', __ROOT__ . "/Admin/Login/index");
                        exit();
                    }
                }
            }


        }
    }




    public function ajaxTestOpenId()
    {
        $openid = cover_userid_to_openid($this->corp_id, $this->secret, 'yzw');
        exit(to_json_string($openid));
    }


    public function ajaxAsyncUser()
    {
        set_time_limit(240);
        update_all_by_qy($this->corp_id, $this->secret, $this->tableName, $this->tableFields);
        ajaxMsg(0, "success");
    }

    /**
     * 主动推送
     */
    public function sendMessage($data)
    {
        $corpID = $this->corp_id;
        $appselect = $this->secret;
       // return $data;
        $accessToken = get_qy_access_token($corpID, $appselect);
        $options = array(
            'access_token' => $accessToken,
            'appid' => $corpID,
            'appsecret' => $appselect,
        );
        $_qy = new QYWechat($options);
        return $_qy->sendMessage($data);
    }


    public function index()
    {
        $this->display();
    }
}
