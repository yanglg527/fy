<?php
namespace Admin\Controller;

use Admin\Model\AdminAuthRuleViewModel;
use Common\Controller\BaseController;
use Think\Controller;

/**
 * 文章管理
 * Class ContentController
 * @package Home\Controller
 */
class HelpController extends BaseAdminController
{

    function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->set_sidebar_module('App');
        $this->set_sidebar_sub('help');
    }

    public function index()
    {
        // 获得所有符合条件的支部
        $keyword = I('keyword');
        $map = array();
        if ($keyword) {
            $map['Help.title'] = array('like',"%$keyword%");
            $this->assign('keyword', $keyword);
        }


        $map['Help.status'] = array('gt',-1);

//field(Help.status, 0, 1, 2),
        $page =D('HelpView')->findPage($map, 15, 'Help.create_time desc');

        $this->assign('page', $page);
        $this->display();
    }


    public function ajaxDelete($id = 0){
        D('Help')->where(array('id'=>$id))->save(array('status'=>-1));
        ajaxMsg(0, '已删除');
    }
	
	public function ajaxPass($id = 0){
        D('Help')->where(array('id'=>$id))->save(array('status'=>1));
        ajaxMsg(0, 'success');
    }
	
	public function ajaxUnpass($id = 0){
        D('Help')->where(array('id'=>$id))->save(array('status'=>2));
        ajaxMsg(0, 'success');
    }

}