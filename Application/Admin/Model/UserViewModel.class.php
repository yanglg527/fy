<?php 
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]> 
 */
namespace Admin\Model;
use Common\Model\BaseViewModel;

class UserViewModel extends BaseViewModel{
	protected $viewFields = array(
		'User' => array('*','headimgurl','_type'=>'LEFT'),
		'AdminAuthGroupAccess'=>array('*','_on'=>'User.uid=AdminAuthGroupAccess.uid','_type'=>'LEFT'),
		'AdminAuthGroup'=>array('title'=>'group_name','_on'=>'AdminAuthGroupAccess.group_id=AdminAuthGroup.id', '_type'=>'LEFT'),
		'Role'=>array('name'=>'role_name','_on'=>'User.role_id=Role.id', '_type'=>'LEFT'),
		'PartyBranch'=>array('name'=>'admin_branch_name','_on'=>'User.admin_branch_id=PartyBranch.id', '_type'=>'LEFT'),
		'PartyBranch '=>array('_as'=>'PartyBranch2', 'name'=>'branch_name','sort'=>'branch_sort','_on'=>'User.branch_id=PartyBranch2.id', '_type'=>'LEFT'),
		'PartyBranchHq'=>array('name'=>'branch_hq_name','_on'=>'User.admin_branch_hq_id=PartyBranchHq.id', '_type'=>'LEFT'),
		'Post'=>array('name'=>'post_name','_on'=>'User.post_id=Post.id', '_type'=>'LEFT'),
		'WorkUnit'=>array('name'=>'work_unit_name', '_on'=>'WorkUnit.id=User.work_unit_id', '_type'=>'LEFT' ),
	);
}