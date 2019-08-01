<?php 
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]> 
 */
namespace Admin\Model;
use Think\Model\RelationModel;
class UserModel extends RelationModel{
	protected $tableName="user";
	//关联模型
	protected $_link=array(
		'AdminGroupAccess'=>array(
			'mapping_type'=>self::HAS_MANY,
			'class_name'=>'admin_auth_group_access',
			'foreign_key'=>'uid',			
			'as_fields'=>'uid:guid,group_id',
			),
	
	);
	protected $_auto = array ( 
        array('password', '', self::MODEL_UPDATE, 'ignore'),
        array('password', 'md5', self::MODEL_BOTH, 'function'),
        array('password', NULL, self::MODEL_UPDATE, 'ignore'),
    );
	//自动验证
	//array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
	protected $_validate=array(
		array('username','require','用户名必须填写!'),
		array('password','5,10','密码长度必须6-10位！',2,'length',3),
     	array('password2','password','前后密码不一致！',2,'confirm',3), // 验证确认密码是否和密码一致
		);
}