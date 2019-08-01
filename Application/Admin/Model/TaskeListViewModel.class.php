<?php
/**
 * 待办任务
 * @author Calvin
 */
namespace Admin\Model;

use Common\Model\BaseViewModel;

class TaskeListViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'TasksItem' => array(
            'id','uid', 'tasks_title', 'tasks_type', 'start_date',
            'end_date', 'status', '_type'=>'LEFT'),
        'User' => array('realname'=>'sendname',
            '_on'=>'TasksItem.uid=User.uid','_type'=>'LEFT'),
    );
}
