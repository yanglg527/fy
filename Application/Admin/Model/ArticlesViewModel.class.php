<?php 
namespace Admin\Model;
use Common\Model\BaseRelationModel;
use Common\Model\BaseViewModel;
use Think\Model\ViewModel;
use Think\Page;

/**
 * 课程
 * Class ArticlesViewModel
 * @package Admin\Model
 */
class ArticlesViewModel extends BaseViewModel{
	protected $viewFields = array(
		'Articles' => array('*', '_type'=>'LEFT'),
		'User' => array('realname'=>'user_realname', '_on'=>'Articles.uid=User.uid', '_type'=>'LEFT'),
		'PartyBranch' => array('name'=>'branch_name', '_on'=>'Articles.branch_id=PartyBranch.id', '_type'=>'LEFT'),
		'PartyBranchHq' => array('name'=>'branch_hq_name', '_on'=>'Articles.branch_hq_id=PartyBranchHq.id', '_type'=>'LEFT'),
	);
}
