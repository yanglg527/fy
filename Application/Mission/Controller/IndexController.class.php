<?php
/**
 * 任务中心 - 控制器
 * @author Calvin
 */
namespace Mission\Controller;

use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;

class MissionController extends BaseAuthController {

	function _initialize() {
		parent::_initialize();
	}

	/**
	 * 我的任务 - 首页
	 */
	public function index() {
		//判断是否重定向登录
		$this->check_wx_redirect();
		$list = array();
		$this -> assign('list', $list);
		$this -> display();
	}

	/**
	 * 发布任务
	 */
	public function add()
	{
		$this->display();
	}

	/**
	 * 草稿箱列表
	 * @var [type]
	 */
	public function missionDraftList()
	{
		$this->display();
	}

	/**
	 * 督察督办列表
	 * @return [type] [description]
	 */
	public function superviseList()
	{
		$this->display();
	}

	/**
	 * 已完成列表
	 */
	public function perfectionList()
	{
		$this->display();
	}

	/**
	 * 未完成列表
	 */
	public function notPerfectionList()
	{
		$this->display();
	}

	/**
	 * 添加办理记录
	 */
	public function addMissionReplyLog()
	{
		ajaxMsg(0, '办理记录添加完成');
	}

	/**
	 * 添加办理记录
	 */
	public function addMissionSuperviseLog()
	{
		ajaxMsg(0, '添加督促记录');
	}



}
