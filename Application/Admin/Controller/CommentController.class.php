<?php
namespace Admin\Controller;

use Admin\Model\AdminAuthRuleViewModel;
use Common\Controller\BaseController;
use Think\Controller;
use Admin\Util\AdminUtil;

/**
 * 文章管理
 * Class ContentController
 * @package Home\Controller
 */
class CommentController extends BaseAdminController
{

	function _initialize()
	{
		parent::_initialize(); // TODO: Change the autogenerated stub
		$this->set_sidebar_module('Comment');
		
	}


    public function index()
    {

        $this->set_sidebar_sub('activity');
    


        $this->display();
    }
    public  function taizhangComment()
    {
        # code...
        $this->set_sidebar_sub('taizhangComment');

        $keyword = I('keyword');
        $map = array();
        if ($keyword) {
            $map['content'] = array('like',"%$keyword%");
            $this->assign('keyword', $keyword);
        }
        $map['status']=array('gt',-1);
        $page =D('TaizhangCommentView')->findPage($map, 15, 'create_time desc');

        $this->assign('page', $page);
        $this->display();
    }

    public  function activityComment()
    {
        # code...
        $this->set_sidebar_sub('activityComment');

        $keyword = I('keyword');
        $map = array();
        if ($keyword) {
            $map['content'] = array('like',"%$keyword%");
            $this->assign('keyword', $keyword);
        }
        $map['status']=array('gt',-1);
        $page =D('EventCommentView')->findPage($map, 15, 'comment_time desc');

        $this->assign('page', $page);
        $this->display();
    }
    public  function helpComment()
    {
        # code...
        $this->set_sidebar_sub('helpComment');

        $keyword = I('keyword');
        $map = array();
        if ($keyword) {
            $map['content'] = array('like',"%$keyword%");
            $this->assign('keyword', $keyword);
        }
        $map['status']=array('gt',-1);
        $page =D('HelpCommentView')->findPage($map, 15, 'create_time desc');

        $this->assign('page', $page);
        $this->display();
    }
    public  function articleComment()
    {
        # code...
        $this->set_sidebar_sub('articleComment');

        $keyword = I('keyword');
        $map = array();
        if ($keyword) {
            $map['content'] = array('like',"%$keyword%");
            $this->assign('keyword', $keyword);
        }
        $map['status']=array('gt',-1);
        $page =D('ArticleCommentView')->findPage($map, 15, 'create_time desc');

        $this->assign('page', $page);
        $this->display();
    }

    public  function speakingComment()
    {
        # code...
        $this->set_sidebar_sub('speakingComment');

        $keyword = I('keyword');
        $map = array();
        if ($keyword) {
            $map['content'] = array('like',"%$keyword%");
            $this->assign('keyword', $keyword);
        }
        $map['status']=array('gt',-1);
        $page =D('SpeakingCommentView')->findPage($map, 15, 'create_time desc');

        $this->assign('page', $page);
        $this->display();
    }
    public function ajaxStatus($id = 0){
        $type = I('type'); 
        $status = I('status',0);
        if(!$type){
            ajax(1,'删除失败');
        }      
        D($type)->where(array('id'=>$id))->save(array('status'=>$status));
        ajaxMsg(0, '已修改');
    }
	


}