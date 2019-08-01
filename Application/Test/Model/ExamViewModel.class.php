<?php
/**
 * 操作用的模型
 */
namespace Test\Model;
use Common\Model\BaseViewModel;
use Think\Model;

class ExamViewModel extends BaseViewModel{


    protected $viewFields = array(
        'Exam'=>array('*','_type'=>'LEFT'),

        'User'=>array(
            'headimgurl'=>'user_headimgurl',
            'realname'=>'user_realname',
            'nickname'=>'user_nickname',
            '_on'=>'Exam.uid=User.uid','_type'=>'LEFT'),
        'PartyBranch'=>array('name'=>'branch_name', '_on'=>'User.branch_id=PartyBranch.id','_type'=>'LEFT'),
    );



}