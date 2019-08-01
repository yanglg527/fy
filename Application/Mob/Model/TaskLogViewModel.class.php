<?php
namespace Mob\Model;
use Common\Model\BaseViewModel;

class TaskLogViewModel extends BaseViewModel{
    protected $viewFields = array(
        'TasksExecutorLog' => array(
            'content', 'exe_uid', 'create_at', '_type'=>'LEFT'),
        'User' => array(
            'realname'=>'exe_user','_on'=>'User.uid=TasksExecutorLog.exe_uid', '_type'=>'LEFT'),
    );
}
