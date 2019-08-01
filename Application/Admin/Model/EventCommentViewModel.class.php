<?php 
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]> 
 */
namespace Admin\Model;
use Common\Model\BaseViewModel;

class EventCommentViewModel extends BaseViewModel{
	protected $viewFields = array(
		'EventComment' => array('*','_type'=>'LEFT'),
		'Event'=>array('title'=>'event_title','type_id'=>'type_id','_on'=>'EventComment.event_id=Event.id','_type'=>'LEFT'),
		'User'=>array('realname'=>'user_realname','_on'=>'User.uid=EventComment.uid','_type'=>'LEFT'),
	);
}