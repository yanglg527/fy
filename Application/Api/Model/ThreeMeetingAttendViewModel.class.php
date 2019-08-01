<?php
/**
 * 三会一课 移动端 考勤视图模型
 */
namespace Api\Model;
use Common\Model\BaseViewModel;

class ThreeMeetingAttendViewModel extends BaseViewModel{
	protected $viewFields = array(
		'ThreeMeetingAttend' => array('uid','meeting_id',
		'attend_type', '_type'=>'LEFT'),

		'User' => array('headimgurl'=>'img','realname',
		'_on'=>'User.uid=ThreeMeetingAttend.uid', '_type'=>'LEFT'),
	);
}
