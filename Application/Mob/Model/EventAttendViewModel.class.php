<?php
namespace Mob\Model;

use Common\Model\BaseViewModel;
use Think\Model\RelationModel;
use Think\Model\ViewModel;
use Think\Page;

class EventAttendViewModel extends BaseViewModel
{

    public $viewFields = array(
        'EventAttend'=>array(
            '*', '_type'=>'LEFT'
        ),
        'User'=>array(
            'realname'=>'user_realname',
            'headimgurl'=>'user_headimgurl',
            'branch_id'=>'branch_id',
            '_on'=>'User.uid=EventAttend.uid', '_type'=>'LEFT'),
          'PartyBranch' => array('name'=>'branch_name', '_on'=>'User.branch_id=PartyBranch.id', '_type'=>'LEFT'),


    );



}