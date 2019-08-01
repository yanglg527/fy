<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * 网页版登录
 * Class IndexController
 * @package Admin\Controller
 */
class LoginController extends Controller
{
    public function index()
    {
// var_dump(1111);
        $this->assign('url',I('url'));
        $this->display();
    }

    public function wx_login()
    {

//        header("Location: http://dj.zhgs.gov.cn/Weixin/QY/webAuthority");
////        redirect("http://dj.zhgs.gov.cn/Weixin/QY/webAuthority");
//        exit();
        $this->display();
    }

    public function ajaxLogin(){
        $username = I('username');
        $password = I('password');
        if(!$username)
            ajaxMsg(1,"请先填写用户名");
        if(!$password)
            ajaxMsg(1,"请先填写密码");
        $user = D('User')->where(array('username'=>$username,'password'=>md5($password),'status'=>1))->find();
        if($user){

            admin_login($user['uid']);
            ajaxMsg(0,"success");
        }
        ajaxMsg(1,"帐号或者密码错误");

    }

    public function logout(){
        admin_logout();
        redirect(__ROOT__."/Admin/Login/index");
    }

  

}