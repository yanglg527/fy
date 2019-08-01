<?php
/**
 * 三会一课 移动端 详情视图模型
 */
namespace Api\Model;
use Common\Model\BaseViewModel;

class MeetingDetailViewModel extends BaseViewModel{
	protected $viewFields = array(
		'ThreeMeeting' => array('id','branch_id','tasks_id',
		'uid','meeting_type','theme','likes'=> 'likenum','comment','collection',
		'place','create_at', 'content','_type'=>'LEFT'),

		'User' => array('realname', 'branch_id',
		'_on'=>'User.uid=ThreeMeeting.uid', '_type'=>'LEFT'),

		'PartyBranch' => array('name'=>'branch_name',
		'_on'=>'User.branch_id=PartyBranch.id', '_type'=>'LEFT'),

		'ThreeMeetingLike'=>array('id'=>'like_id','is_like',
		'_on'=>'ThreeMeetingLike.uid=User.uid and ThreeMeetingLike.meeting_id=ThreeMeeting.id',
		'_type'=>'LEFT'),

		'ThreeMeetingTibet'=>array('id'=>'collection_id','is_collection',
		'_on'=>'ThreeMeetingTibet.uid=User.uid and ThreeMeetingTibet.meeting_id=ThreeMeeting.id',
		'_type'=>'LEFT'),
	);

	function _initialize()
	{
		parent::_initialize();
		$this->viewFields['ThreeMeetingLike']['_on'] = 'ThreeMeetingLike.uid='.uid().' and ThreeMeetingLike.meeting_id=ThreeMeeting.id';
		$this->viewFields['ThreeMeetingTibet']['_on'] = 'ThreeMeetingTibet.uid='.uid().' and ThreeMeetingTibet.meeting_id=ThreeMeeting.id';
	}
}
