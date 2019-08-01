<?php
namespace Admin\Model;

use Common\Model\BaseViewModel;
use Think\Model\RelationModel;
use Think\Model\ViewModel;
use Think\Page;

class EventViewModel extends BaseViewModel
{

    public $viewFields = array(
        'Event'=>array(
            '*','title'=>'event_title','name'=>'event_name','mobile'=>'event_mobile','people_limit','status'=>'event_status', '_type'=>'LEFT'
        ),
        'User'=>array(
            'realname',
            '_on'=>'User.uid=Event.uid', '_type'=>'LEFT'),
        'PartyBranch'=>array('name'=>'branch_name','_on'=>'Event.branch_id=PartyBranch.id', '_type'=>'LEFT'),
        'PartyBranchHq' => array('name'=>'branch_hq_name', '_on'=>'Event.branch_hq_id=PartyBranchHq.id', '_type'=>'LEFT'),


    );



}