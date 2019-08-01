<?php
namespace Event\Model;

use Common\Model\BaseViewModel;
use Think\Model\RelationModel;
use Think\Model\ViewModel;
use Think\Page;

class EventAttendViewModel extends BaseViewModel
{

    public $viewFields = array(
        'EventAttend'=>array(
            '*','id'=>'attend_id','mobile'=>'attend_mobile', '_type'=>'LEFT'
        ),
        'Event'=>array('*',
            '_on'=>'Event.id=EventAttend.event_id', '_type'=>'LEFT'
        ),
        'User'=>array(
            '*',
            '_on'=>'User.uid=EventAttend.uid', '_type'=>'LEFT'),
        'Role'=>array('name'=>'role_name','_on'=>'User.role_id=Role.id', '_type'=>'LEFT'),
        'PartyBranch'=>array('name'=>'branch_name','_on'=>'User.branch_id=PartyBranch.id', '_type'=>'LEFT'),
        'Post'=>array('name'=>'post_name','_on'=>'User.post_id=Post.id', '_type'=>'LEFT')


    );



}