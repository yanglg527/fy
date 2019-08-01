<?php
namespace Common\Controller;

use Common\Util\AuthUtils;
use Think\Controller;

/**
 * 基本控制器
 * Class BaseController
 * @package Common\Controller
 */
class BaseController extends Controller
{

    //过滤器
    function _initialize(){
        $user = user();
        if($user){//如果用户已经登录了，判断是否拥有权限
//                if(!authCheck(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,uid())){
//                    if(IS_AJAX){
//                        ajaxMsg(1,"你没有操作权限");
//                    } else{
//                        redirect(__ROOT__."/error2.html");
//                        exit;
//                    }
//                }

            if($user['status'] != 1){
                redirect(__ROOT__."/Home/Error/index");
            }
            $this->assign('user', $user);
            $this->assign('adm_auth',AuthUtils::adm_post($user));
        }
    }

    public function setTitle( $title)
    {
        $this->assign('webTitle', $title);
    }


    function setHeader($header){
        $this->assign('header',$header);
    }

    //微信重定向 $needAuthLogin = 0|不需要授权，其它浏览器可以打开  1|必须微信浏览器 2|必须企业号成员
    function check_wx_redirect($name, $state=0, $needAuthLogin = 2){


        //如果已经登录了 或者不需要授权登录的页面
        if(user()|| $needAuthLogin == 0){//不进行操作

        } elseif($needAuthLogin == 1){//未登录
            if(IS_AJAX){
                ajaxMsg(1,"请刷新页面后再试");
            }
            if(is_weixin()){//重定向登录，注册用户
                redirect(__ROOT__."/");
            }else{//请在微信浏览器中打开
                redirect(__ROOT__."/error1.html");
            }

            exit;
        }elseif($needAuthLogin == 2){//未登录
            if(IS_AJAX){
                ajaxMsg(1,"请刷新页面后再试");
            }
            if(is_weixin()){//重定向登录，注册用户
                redirect(__ROOT__."/Weixin/QY/webAuth?key=$name&state=$state");
            }else{//请在微信浏览器中打开
                redirect(__ROOT__."/Home/Error/weixin?url=".urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']));
            }

            exit;
        }
    }


}