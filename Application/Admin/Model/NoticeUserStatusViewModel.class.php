<?php 
namespace Admin\Model;
use Common\Model\BaseRelationModel;
use Common\Model\BaseViewModel;
use Think\Model\ViewModel;
use Think\Page;

/**
 * 课程
 * Class NoticeViewModel
 * @package Admin\Model
 */
class NoticeUserStatusViewModel extends BaseViewModel{
	protected $viewFields = array(
		'NoticeUserStatus' => array('*', '_type'=>'LEFT'),
		'User' => array('realname'=>'user_name', '_on'=>'NoticeUserStatus.uid=User.uid', '_type'=>'LEFT'),
		// 'PartyBranch'=>array('name'=>'branch_name','_on'=>'NoticeUserStatus.branch_id=PartyBranch.id', '_type'=>'LEFT'),
        // 'PartyOrganization'=>array('name'=>'organization_name','_on'=>'NoticeUserStatus.organization_id=PartyOrganization.id', '_type'=>'LEFT'),
	);
}
