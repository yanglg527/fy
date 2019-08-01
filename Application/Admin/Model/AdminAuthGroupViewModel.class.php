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
class AdminAuthGroupViewModel extends BaseViewModel{
	public $viewFields=array(	
		'AdminAuthGroup'=>array('describe'=>'a_describe','id'=>'id','type'=>'type','title'=>'title','status'=>'status','_type'=>'LEFT'),
	);
}