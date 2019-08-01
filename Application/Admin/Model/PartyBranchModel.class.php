<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Common\Model;

use Common\Model\BaseRelationModel;

use Common\Model\BaseViewModel;

class PartyBranchModel extends BaseRelationModel{
   protected $_link = array(
       'users' => array(
           'mapping_type'  => self::HAS_MANY,
           'class_name'    => 'User',
           'foreign_key'   => 'branch_id',
           'mapping_name'  => 'users',
       ),
       'admins' => array(
           'mapping_type'  => self::HAS_MANY,
           'class_name'    => 'PartyBranchAdm',
           'foreign_key'   => 'branch_id',
           'mapping_name'  => 'admins',
       ),


   );
}
