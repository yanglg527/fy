<?php
/**
 * 志愿服务-报名人员列表
 */
namespace Api\Model;
use Common\Model\BaseViewModel;

class VolunteerPartnerViewModel extends BaseViewModel{
	protected $viewFields = array(
		'VolunteerServicePartner' => array('uid', '_type'=>'LEFT'),

		'User' => array('headimgurl'=>'img','realname',
		'_on'=>'User.uid=VolunteerServicePartner.uid', '_type'=>'LEFT'),
	);
}
