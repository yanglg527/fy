<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Mob\Model;

use Common\Model\BaseViewModel;

class UserAdviceViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'UserAdvice' => array('*','_type'=>'LEFT'),
        'User'=>array('realname'=>'user_realname','branch_id'=>'user_branch_id','_on'=>'UserAdvice.uid=User.uid', '_type'=>'LEFT'),
        'Role'=>array('name'=>'role_name','_on'=>'UserAdvice.role_id=Role.id', '_type'=>'LEFT'),
        'PartyBranch'=>array('name'=>'name','_on'=>'User.branch_id=PartyBranch.id', '_type'=>'LEFT'),
    );
}