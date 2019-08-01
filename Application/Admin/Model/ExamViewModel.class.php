<?php
namespace Admin\Model;
use Common\Model\BaseViewModel;
use Think\Model\ViewModel;
use Think\Page;

/**
 * Class StudyScheduleViewModel
 * @package Study\Model
 */
class ExamViewModel extends BaseViewModel{
	protected $viewFields = array(
		'Exam'=>array('*','_type'=>'LEFT'),

		'User'=>array(
			'headimgurl'=>'user_headimgurl',
			'realname'=>'user_realname',
			'nickname'=>'user_nickname',
			'_on'=>'Exam.uid=User.uid','_type'=>'LEFT'),
		'Role'=>array('name'=>'role_name','_on'=>'User.role_id=Role.id', '_type'=>'LEFT'),
		'Post'=>array('name'=>'post_name','_on'=>'User.post_id=Post.id', '_type'=>'LEFT'),
		'PartyBranch'=>array('name'=>'branch_name','_on'=>'Exam.branch_id=PartyBranch.id', '_type'=>'LEFT'),
		'PartyBranchHq' => array('name'=>'branch_hq_name', '_on'=>'Exam.branch_hq_id=PartyBranchHq.id', '_type'=>'LEFT'),

	);

}





