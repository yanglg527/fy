<?php

/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Weixin\Controller;
use Common\Controller\BaseController;
use Weixin\Util\GZHUtils;
use Weixin\Util\QYConfig;
use Weixin\Util\QYWechat;
set_time_limit(240);
class WebController extends BaseController {




    function _initialize(){
        $config = C('WEIXINGZHWEB_CONFIG');
    }

    /**
     * 网页扫一扫授权跳转
     */
    public function wxWebAuthority()
    {
        $config = C('WEIXINGZHWEB_CONFIG');
        $domain = $config['DOMAIN'];
        $redirect_uri = "http://$domain/Weixin/GZH/wxWebAuthorityLogin" ;
        $config = C('WEIXINGZHWEB_CONFIG');
        $url = "https://open.weixin.qq.com/connect/qrconnect?appid={$config['APP_ID']}&redirect_uri=" . urlencode($redirect_uri). "&response_type=code&scope=snsapi_login&state=#wechat_redirect";
        header('location: ' . $url);

    }

    /**
     * 网页授权登陆
     */
    public function wxWebAuthorityLogin()
    {
        $code = I('get.code');
        if ($code) {

            $user = GZHUtils::get_web_auth_user_info($code,'web');
            CenterUtil::center_login($user['uid']);

            $url = __ROOT__.'/Center/Index';
            header('location: ' . $url);
        }
    }
}