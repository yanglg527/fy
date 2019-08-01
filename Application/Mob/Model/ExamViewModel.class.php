<?php
/**
 * 操作用的模型
 */
namespace Mob\Model;
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
        'PartyBranch'=>array('name'=>'branch_name', '_on'=>'Exam.branch_id=PartyBranch.id','_type'=>'LEFT'),
        // 'AdmPost'=>array('name'=>'adm_post_name','_on'=>'User.adm_id=AdmPost.id', '_type'=>'LEFT'),
    );



}