<?php
namespace Event\Model;

use Common\Model\BaseViewModel;
use Think\Model\RelationModel;
use Think\Model\ViewModel;
use Think\Page;

class EventViewModel extends BaseViewModel
{

    public $viewFields = array(
        'Event'=>array(
            '*','title'=>'event_title', '_type'=>'LEFT'
        ),
        'EventType'=>array(
            '_on'=>'EventType.id=Event.type_id', '_type'=>'LEFT'
        ),
        'User'=>array(
            '*',
            '_on'=>'User.uid=Event.uid'),
        'Role'=>array('name'=>'role_name','_on'=>'User.role_id=Role.id', '_type'=>'LEFT'),
        'PartyBranch'=>array('name'=>'branch_name','_on'=>'User.branch_id=PartyBranch.id', '_type'=>'LEFT'),
        'Post'=>array('name'=>'post_name','_on'=>'User.post_id=Post.id', '_type'=>'LEFT')


    );



}