<?php
/**
 * 三会一课 - 评论视图模型
 * @author Calvin
 */
namespace Admin\Model;

use Common\Model\BaseViewModel;

class MeetingCommentViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'ThreeMeetingComment' => array('*', '_type'=>'LEFT'),

        'ThreeMeeting' => array(
            'theme', 'meeting_type',
            '_on'=>'ThreeMeeting.id=ThreeMeetingComment.meeting_id', '_type'=>'LEFT'),

        'User' => array('realname'=>'sendname',
            'organization_id', 'branch_id',
            '_on'=>'ThreeMeetingComment.uid=User.uid','_type'=>'LEFT'),

        'PartyBranch' => array('name'=>'branch_name',
            '_on'=>'User.branch_id=PartyBranch.id', '_type'=>'LEFT'),

		'PartyOrganization'=>array('name'=>'organization_name',
            '_on'=>'User.organization_id=PartyOrganization.id','_type'=>'LEFT'),
    );
}
