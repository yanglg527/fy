<?php
namespace Common\Model;
use Common\Model\BaseViewModel;
/**
 * 待办任务出勤列表
 * @var array
 */
class TasksAttendanceListViewModel extends BaseViewModel{
    protected $viewFields = array(
        'TasksExecutor' => array(
            'exe_uid','exe_status', '_type'=>'LEFT'),
        'User' => array(
            'realname',
            '_on'=>'User.uid=TasksExecutor.exe_uid', '_type'=>'LEFT'),
    );
}
