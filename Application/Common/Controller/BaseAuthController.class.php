<?php
namespace Common\Controller;

use Common\Util\AuthUtils;
use Think\Controller;

/**
 * 权限基本控制器
 * Class BaseController
 * @package Common\Controller
 */
class BaseAuthController extends BaseController
{

    //过滤器
    function _initialize(){

        $user = user();
        if(I('djpt')=='test'){
            echo 'ddd';
        }
        else if($user){//如果用户已经登录了，判断是否拥有权限

//
//                if(!authCheck(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,uid())){
//                    if(IS_AJAX){
//                        ajaxMsg(1,"你没有操作权限");
//                    } else{
//                        redirect(__ROOT__."/error2.html");
//                        exit;
//                    }
//                }
//
            if($user['status'] != 1){
                redirect(__ROOT__."/Home/Error/index");
            }
            $this->assign('user', $user);
            $this->assign('adm_auth',AuthUtils::adm_post($user));
        }else{
            if(MODULE_NAME == 'Admin'){
//                $this->error('请先登录',__ROOT__);
            }
        }
    }


    function setWebTitle($title){
        $this->assign('webTitle',$title);
    }


}