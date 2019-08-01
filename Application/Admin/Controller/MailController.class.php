<?php
namespace Admin\Controller;

use Common\Controller\BaseController;
use Think\Controller;

/**
 * 系统设置
 * Class SystemController
 * @package Admin\Controller
 */
class MailController extends BaseAdminController
{
    function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->set_sidebar_module('App');
        $this->set_sidebar_sub('Mail');
    }

    public function index()
    {
        $this->outboxList();
    }

    /****规则 start******/
    //规则列表
    public function outboxList()
    {
        $keyword = I('keyword');
        if($keyword){
            $page = D('MailOutboxView')->findPage("concat(User.realname,title,msg) like '%$keyword%'",15,'create_time desc');
            $this->assign('keyword', $keyword);
        }else{
            $page = D('MailOutboxView')->findPage('',15,'create_time desc');
        }

        $this->assign('page', $page);
        //读取模块信息
        $this->display('outboxList');
    }

    //添加规则页面
    public function  ajaxSaveOutboxStatus(){
        $id = I('id');
        $rule = D('MailOutbox')->find($id);
        if($rule){
            $status = $rule['status'];
            if($status == 1){
                $rule['status'] = -1;
            }else{
                $rule['status'] = 1;
            }
            D('MailOutbox')->where(array('id'=>$id))->save($rule);
            ajaxMsg(0,'保存成功');
        }else{
            ajaxMsg(1,'该权限已经删除了');
        }

    }




}