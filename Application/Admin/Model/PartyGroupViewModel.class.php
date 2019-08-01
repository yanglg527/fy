<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Admin\Model;

use Common\Model\BaseViewModel;

class PartyGroupViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'PartyGroup' => array('*', '_type'=>'LEFT'),
        'PartyGroupGl' => array('uid'=>'gl_uid', '_on'=>'PartyGroupGl.party_group_id=PartyGroup.id', '_type'=>'LEFT'),
        'User' => array('realname'=>'gl_realname', '_on'=>'User.uid=PartyGroupGl.uid', '_type'=>'LEFT'),
        'PartyBranch'=>array('name'=>'branch_name', '_on'=>'PartyGroup.branch_id=PartyBranch.id', '_type'=>'LEFT'),
    );

}