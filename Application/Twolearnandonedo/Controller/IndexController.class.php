<?php
/**
 * 两学一做
 * @author Calvin
 */
namespace Twolearnandonedo\Controller;

use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;


class IndexController extends BaseAuthController {
	const learntype = array(
		'学党章党规', '学系列讲话', '做合格党员'
	);

	function _initialize()
	{
		parent::_initialize();
	}

	/**
	 * 两学一做 入口列表
	 */
	public function index()
	{
		$userBranchId = user()['branch_id'];
		$branchId = I('get.branchId') ? I('get.branchId', 'all') : $userBranchId;
		$p = I('p', 1);
		$where['TwoStudyOneDo.status'] = array('eq', 2);
		// 过滤测试支部
		$where['TwoStudyOneDo.branch_id'] = array('neq', 318);
		if (is_numeric($branchId)) {
			$where['TwoStudyOneDo.branch_id'] = array('eq', $branchId);
		}
		$item = D('LearnAndDoView')
			->where($where)
			// ->page("$p,30")
			->order('create_at desc, id desc')->select();

		$this->assign('branchs', M('PartyBranch')->field('id,name,cover')->where('id>318')->select());
		$this->assign('types', self::learntype);
		$this->assign('item', $item);
		$this->assign('userBranchId', $branchId);
		$this->display();
	}

	/**
	 * 详情
	 */
	public function info()
	{
		$id = intval(I('get.id'));
		$info = array();
		$attend = array();

		if ($id) {
		$info = D('Admin/TwoStudyOneDo')->info($id);
		// var_dump($info);exit;
		if (!empty($info)) {
			$res = D('StudyDoAttendView')
				->where(array('StudyDoAttend.do_id' => $id))
				->select();
				foreach ($res as $key => $value) {
					if (empty($value['image'])) {
						$value['image'] = '/Public/Common/img/common-head.png';
					}
					$attend[$value['attend_type']][] = $value;
				}
			}
		}
		$this->assign('info', $info);
		$this->assign('attend', $attend);
		$this->assign('types', self::learntype);
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
