<?php
namespace Admin\Controller;

use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;
use Think\Controller;

/**
 * 网页版登录
 * Class IndexController
 * @package Admin\Controller
 */
class IndexController extends BaseAdminController
{
    function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->set_sidebar_module('Index');
        $this->set_sidebar_sub('index');
    }

    public function index()
    {
        //今日访问
      //echo 'index';
	//var_dump(admin());
	  $today = strtotime(date('Y-m-d', time()));
        $end = $today + 24 * 3600;
        $todayCount = D('User')->where("last_login_time > $today and last_login_time < $end")->count();
        $this->assign('today_count',$todayCount);

        //支部数量
        $branchCount = D('PartyBranch')->count();
        $this->assign('branch_count', $branchCount);

        //党员数量
        $partyGroupId = C('ROLE_PARTY_ID');
        $partyCount = D('User')->where("role_id=$partyGroupId AND status=1")->count();
        $this->assign('party_count', $partyCount);

        //党员数量
        $userCount = D('User')->where("status=1")->count();
        $this->assign('user_count', $userCount);

        $list = D('UserView')->where("User.last_login_time > $today and User.last_login_time < $end")->order('User.last_login_time desc')->select();
//        $list = D('SignInLogView')->where("SignInLog.create_time > $today and SignInLog.create_time < $end")->order('SignInLog.create_time desc')->select();
        $this->assign('list', $list);

        $this->display();
    }

    public function test(){
        $this->display();
    }
    public function test2(){
//        change_global();
//        echo_global();
        echo "realpath:".$_SERVER['DOCUMENT_ROOT'].__ROOT__;
        exit();
    }






}