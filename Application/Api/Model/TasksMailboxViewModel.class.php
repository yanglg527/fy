<?php
namespace Api\Model;
use Common\Model\BaseViewModel;

class TasksMailboxViewModel extends BaseViewModel{
	protected $viewFields = array(
		'TasksMailbox' => array('uid','content',
		'create_at' => 'date', '_type'=>'LEFT'),

		'User' => array('headimgurl','realname',
		'_on'=>'User.uid=TasksMailbox.uid', '_type'=>'LEFT'),
	);
}
