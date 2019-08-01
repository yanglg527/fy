<?php
/**
 * 志愿服务-列表
 */
namespace Api\Model;
use Common\Model\BaseViewModel;

class VolunteerListViewModel extends BaseViewModel{
	protected $viewFields = array(
		'VolunteerService' => array('*', '_type'=>'LEFT'),

		'User' => array('realname'=>'send_name',
		'_on'=>'User.uid=VolunteerService.uid', '_type'=>'LEFT'),
	);
}
