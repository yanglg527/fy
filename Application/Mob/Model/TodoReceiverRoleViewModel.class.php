<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Mob\Model;

use Common\Model\BaseViewModel;

class TodoReceiverRoleViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'TodoReceiverRole' => array('*','role_id'=>'receiver_role_id', '_type' => 'LEFT'),
        'Todo' => array('*', 'create_time'=>'issue_time',  'status'=>'todo_status','content'=>'todo_content', '_on'=>'TodoReceiverRole.todo_id=Todo.id', '_type' => 'LEFT'),
        'TodoUserStatus' => array('uid'=>'user_uid', 'status'=>'todo_user_status', 'create_time'=>'finish_time',  '_on'=>'TodoUserStatus.todo_id=Todo.id', '_type' => 'LEFT'),
    );
}