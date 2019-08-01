<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Mob\Model;

use Common\Model\BaseRelationModel;

class AdmPostSelectRelationModel extends BaseRelationModel
{
    protected $tableName = 'adm_post';
//    protected $_link = array(
//        'adm_post' => array(
//            'mapping_type'  => self::HAS_MANY,
//            'class_name'    => 'adm_post',
//            'parent_key'   => 'pid',
//            'foreign_key'   => 'pid',
//            'mapping_name'  => 'children',
//            'mapping_fields' => 'id as value,name as text',
//            'mapping_order' => 'id asc',
//        )
//    );
}