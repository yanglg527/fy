<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Common\Model;

use Common\Model\BaseViewModel;

class TodoViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'Todo' => array('*','status'=>'todo_status','content'=>'todo_content', '_type' => 'LEFT'),
        'User' => array('realname'=>'issuer_name', '_on'=>'User.uid=Todo.issuer_uid', '_type'=>'LEFT'),
//        'PartyBranch' => array('name' => 'branch_name', '_on' => 'Todo.receiver_branch_id=PartyBranch.id', '_type'=>'LEFT'),
        'PartyBranch ' => array('_as'=>'PartyBranch', 'name' => 'branch_name', '_on' => 'Todo.branch_id=PartyBranch.id', '_type'=>'LEFT'),
        'PartyBranchHq' => array('name'=>'branch_hq_name', '_on'=>'Todo.branch_hq_id=PartyBranchHq.id', '_type'=>'LEFT'),
    );
}