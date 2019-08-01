<?php
/**
 * 操作用的模型
 */
namespace Mob\Model;
use Common\Model\BaseViewModel;
use Think\Model;

class ExamSubjectViewModel extends BaseViewModel{

    protected $viewFields = array(
        'ExamSubject'=>array('*','_type'=>'LEFT'),

        'ExamSubjectType'=>array('type'=>'type_num','name'=>'type_name', '_on'=>'ExamSubject.type_id=ExamSubjectType.id','_type'=>'LEFT'),
    );


}