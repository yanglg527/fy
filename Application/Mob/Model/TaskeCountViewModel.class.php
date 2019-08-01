<?php
/**
 * 这个模型就做一件事 统计任务数
 */
namespace Mob\Model;

use Common\Model\BaseViewModel;

class TaskeCountViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'TasksItem' => array(
            'id','uid', 'tasks_title', 'tasks_type', 'start_date',
            'end_date', 'status', '_type'=>'LEFT'),
        'TasksExecutor' => array('*','_on'=>'TasksItem.id=TasksExecutor.tasks_id','_type'=>'LEFT'),
        // 'User' => array('realname'=>'sendname',
            // '_on'=>'TasksItem.uid=User.uid','_type'=>'LEFT'),
    );
}
