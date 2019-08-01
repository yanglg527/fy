<?php
/**
 * 操作用的模型
 */
namespace Questionnaire\Model;
use Common\Model\BaseViewModel;
use Think\Model;

class QuestionnaireExamSubjectViewModel extends BaseViewModel{

    protected $viewFields = array(
        'QuestionnaireExamSubject'=>array('*','_type'=>'LEFT'),

        'QuestionnaireExamSubjectType'=>array('type'=>'type_num','name'=>'type_name', '_on'=>'QuestionnaireExamSubject.type_id=QuestionnaireExamSubjectType.id','_type'=>'LEFT'),
    );


}