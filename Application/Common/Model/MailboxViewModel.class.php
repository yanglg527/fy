<?php
namespace Common\Model;
use Common\Model\BaseViewModel;

class MailboxViewModel extends BaseViewModel{
    protected $viewFields = array(
        'TasksMailbox' => array(
            'uid','content', 'create_at' => 'date', '_type'=>'LEFT'),
        'User' => array(
            'realname', 'headimgurl',
            '_on'=>'User.uid=TasksMailbox.uid', '_type'=>'LEFT'),
    );
}
