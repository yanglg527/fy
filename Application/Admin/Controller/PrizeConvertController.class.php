<?php
namespace Admin\Controller;

use Admin\Model\SignInLogViewModel;
use Common\Controller\BaseController;
use Think\Controller;

/**
 * 系统设置
 * Class SystemController
 * @package Admin\Controller
 */
class PrizeConvertController extends BaseAdminController
{
    function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->set_sidebar_module('App');
        $this->set_sidebar_sub('PrizeConvert');
    }

    public function index()
    {
        $auth = session_auth();
        if ($auth == 1) {
            $map = array();
            $keyword = I('keyword');
            if ($keyword) {
                $map['User.realname | User.mobile'] = array('like', '%' . $keyword . '%');
                $this->assign('keyword', $keyword);
            }

            $page = D('PrizeConvertLogView')->findPage($map, 15, 'PrizeConvertLog.status asc, PrizeConvertLog.create_time desc');
            $this->assign('page', $page);

            $count = D('PrizeConvertLog')->where(array('status'=>0))->count();
            $this->assign('count',$count);
        }

        $this->display();
    }


    public function ajaxSaveGetStatus(){
        $id = I('id');
        $rule = D('PrizeConvertLog')->find($id);
        if($rule){
            $rule['status'] = 1;
            D('PrizeConvertLog')->where(array('id'=>$id))->save($rule);
            ajaxMsg(0,'保存成功');
        }else{
            ajaxMsg(1,'该权限已经删除了');
        }
    }

}