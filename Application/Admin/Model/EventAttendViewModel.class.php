<?php
namespace Admin\Model;

use Common\Model\BaseViewModel;
use Think\Model\RelationModel;
use Think\Model\ViewModel;
use Think\Page;

class EventAttendViewModel extends BaseViewModel
{

    public $viewFields = array(
        'EventAttend'=>array(
            '*','id'=>'attend_id','name'=>'attend_name','mobile'=>'attend_mobile','gender'=>'attend_gender','status'=>'attend_status','position'=>'attend_position','birthday'=>'attend_birthday','work_unit'=>'attend_work_unit', 'remark'=>'attend_remark','_type'=>'LEFT'
        ),
        'Event'=>array(
            '*','title'=>'event_title','name'=>'event_name','mobile'=>'event_mobile','people_limit','status'=>'event_status', '_type'=>'LEFT',
            '_on'=>'Event.id=EventAttend.event_id'
        ),
        'User'=>array(
            '*',
            'gender'=>'user_gender',
            '_on'=>'User.uid=EventAttend.uid'),
        'Role'=>array('name'=>'role_name','_on'=>'User.role_id=Role.id', '_type'=>'LEFT'),
        'PartyBranch'=>array('name'=>'branch_name','_on'=>'User.branch_id=PartyBranch.id', '_type'=>'LEFT'),
        'Post'=>array('name'=>'post_name','_on'=>'User.post_id=Post.id', '_type'=>'LEFT'),
        'User '=>array('_as'=>'EventUser',
            'realname'=>'event_realname',
            'gender'=>'attend_gender',
            '_on'=>'EventUser.uid=Event.uid'),


    );



}