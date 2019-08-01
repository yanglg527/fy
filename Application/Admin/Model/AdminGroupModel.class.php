<?php 
/**
 * 角色表:auth_group,角色用户明细表:auth_group_access
 * 关联模型 一对一 HAS_ONE
 *  * 外键:auth_group.id=auth_group_access.group_id
 *  @author [黄药师] <[46914685@qq.com]>
 */
namespace Admin\Model;
use Think\Model\RelationModel;
class AdminGroupModel extends RelationModel{
	protected $tableName="admin_auth_group";
	protected $_link=array(		
		'AdminGroupAccess'=>array(
			'mapping_type'=>self::HAS_ONE,
			'class_name'=>'admin_auth_group_access',
			'foreign_key'=>'group_id',
			'as_fields'=>'uid,group_id',
			),
		);
}
