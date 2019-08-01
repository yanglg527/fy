<?php 
namespace Mob\Model;
use Common\Model\BaseViewModel;

/**
 * Class NoticeViewModel
 * @package Mob\Model
 */
class NoticeUserStatusViewModel extends BaseViewModel{
	protected $viewFields = array(
		'NoticeUserStatus' => array('*', '_type'=>'LEFT'),
		'Notice' => array('content'=>'notice_content','type_id'=>'type_id','finish_time'=>'notice_finish_time','taizhang_type'=>'notice_taizhang_type','publish_time'=>'notice_publish_time', '_on'=>'NoticeUserStatus.notice_id=Notice.id', '_type'=>'LEFT'),
		'NoticeType'=>array('name'=>'type_name','_on'=>'Notice.type_id=NoticeType.id', '_type'=>'LEFT'),
        // 'PartyOrganization'=>array('name'=>'organization_name','_on'=>'NoticeUserStatus.organization_id=PartyOrganization.id', '_type'=>'LEFT'),
	);
}
