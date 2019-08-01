<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Common\Model;

use Common\Model\BaseViewModel;

class TodoSituationViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'Todo' => array('*','id'=>'todo_id_re', 'status'=>'todo_status','content'=>'todo_content', '_type' => 'RIGHT'),
        'TodoReceiverRole' => array('*', '_on'=>'TodoReceiverRole.todo_id=Todo.id', '_type' => 'RIGHT'),
        'User' => array('uid'=>'user_uid', 'realname'=>'user_realname', '_on'=>'User.role_id=TodoReceiverRole.role_id', '_type' => 'LEFT'),
        'TodoUserStatus' => array('*', 'status'=>'todo_user_status', '_on'=>'User.uid=TodoUserStatus.uid and Todo.id=TodoUserStatus.todo_id'),
        'Role' => array('name'=>'role_name', '_on'=>'Role.id=User.role_id'),
    );
}