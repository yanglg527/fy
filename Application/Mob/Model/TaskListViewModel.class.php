<?php
namespace Mob\Model;
use Common\Model\BaseViewModel;

class TaskListViewModel extends BaseViewModel{
    protected $viewFields = array(
        'TasksExecutor' => array(
            'id' => 'exe_id',
            'tasks_id',
            'exe_status',
            '_type'=>'LEFT'),
        'TasksItem' => array(
            'id', 'tasks_title', 'tasks_contents', 'status',
            'start_date', 'end_date', 'create_at',
            '_on'=>'TasksItem.id=TasksExecutor.tasks_id', '_type'=>'LEFT'),
        'User' => array(
            'realname'=>'send_user',
            '_on'=>'User.uid=TasksItem.uid', '_type'=>'LEFT'),
    );
}
