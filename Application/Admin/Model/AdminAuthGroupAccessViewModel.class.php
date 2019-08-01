<?php 
/**
 * 三表联立查询,角色表:auth_group,角色用户明细表:auth_group_access,用户表:user
 * 视图模型
 * 外键:user.uid=auth_group_access.uid,auth_group_access.group_id=auth_group.id
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Admin\Model;
use Common\Model\BaseViewModel;
use Think\Model\ViewModel;
class AdminAuthGroupAccessViewModel extends BaseViewModel{
	public $viewFields=array(	
		'AdminAuthGroupAccess'=>array('*','_type'=>'LEFT'),
		'AdminAuthGroup'=>array('title'=>'group_name', '_on'=>'AdminAuthGroup.id=AdminAuthGroupAccess.group_id','_type'=>'LEFT'),
		'User'=>array('username','create_time','realname','status','admin_branch_hq_id','admin_branch_id','_on'=>'User.uid=AdminAuthGroupAccess.uid','_type'=>'LEFT'),
		'PartyBranch'=>array('name'=>'branch_name','_on'=>'User.admin_branch_id=PartyBranch.id', '_type'=>'LEFT'),
		'PartyBranchHq'=>array('name'=>'branch_hq_name','_on'=>'User.admin_branch_hq_id=PartyBranchHq.id', '_type'=>'LEFT'),
		);
}