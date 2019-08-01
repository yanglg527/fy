<?php
/**
 * 发件箱
 */
namespace Mail\Model;
use Common\Model\BaseViewModel;

class MailInboxViewModel extends BaseViewModel{
	protected $viewFields = array(
		'MailInbox' => array('*','_type'=>'LEFT'),
		'MailOutbox' => array('*','_on'=>'MailInbox.outbox_id=MailOutbox.id','_type'=>'LEFT'),
		'User'=>array('uid'=>'receiver_uid','realname'=>'realname','_on'=>'User.uid=MailInbox.uid','_type'=>'LEFT'),
		'User '=>array('_as'=>"SenderUser", 'uid'=>'sender_uid','realname'=>'sender_realname','_on'=>'SenderUser.uid=MailOutbox.uid','_type'=>'LEFT')
	);
}