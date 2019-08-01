<?php
/**
 * 操作用的模型
 */
namespace Questionnaire\Model;
use Common\Model\BaseViewModel;
use Think\Model;

class QuestionnaireExamViewModel extends BaseViewModel{


    protected $viewFields = array(
        'QuestionnaireExam'=>array('*','_type'=>'LEFT'),

        'User'=>array(
            'headimgurl'=>'user_headimgurl',
            'realname'=>'user_realname',
            'nickname'=>'user_nickname',
            '_on'=>'QuestionnaireExam.uid=User.uid','_type'=>'LEFT'),
        'PartyBranch'=>array('name'=>'branch_name', '_on'=>'User.branch_id=PartyBranch.id','_type'=>'LEFT'),
    );



}