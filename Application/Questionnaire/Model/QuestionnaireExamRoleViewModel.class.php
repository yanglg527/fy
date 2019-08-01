<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Questionnaire\Model;

use Common\Model\BaseViewModel;

class QuestionnaireExamRoleViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'QuestionnaireExamRole' => array('*', '_type'=>'LEFT'),
        'Role' => array('name'=>'role_name', '_on'=>'QuestionnaireExamRole.role_id=Role.id', '_type'=>'LEFT'),
        'QuestionnaireExam'=>array('*','_on'=>'QuestionnaireExamRole.exam_id=QuestionnaireExam.id', '_type'=>'LEFT'),

        'User'=>array(
            'headimgurl'=>'user_headimgurl',
            'realname'=>'user_realname',
            'nickname'=>'user_nickname',
            '_on'=>'QuestionnaireExam.uid=User.uid','_type'=>'LEFT'),
        'PartyBranch' => array('name'=>'branch_name', '_on'=>'QuestionnaireExam.branch_id=PartyBranch.id', '_type'=>'LEFT'),
        'PartyBranchHq' => array('name'=>'branch_hq_name', '_on'=>'QuestionnaireExam.branch_hq_id=PartyBranchHq.id', '_type'=>'LEFT'),
    );


}