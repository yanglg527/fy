<?php
/**
 * 发件箱
 */
namespace Admin\Model;
use Common\Model\BaseViewModel;

class MailOutboxViewModel extends BaseViewModel{
	protected $viewFields = array(
		'MailOutbox' => array('*','_type'=>'LEFT'),
		'User'=>array('realname'=>'realname','_on'=>'User.uid=MailOutbox.uid','_type'=>'LEFT')
	);
}