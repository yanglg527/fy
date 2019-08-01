<?php
/**
 * 操作用的模型
 */
namespace Admin\Model;
use Common\Model\BaseViewModel;
use Think\Model;

class QuestionnaireExamSubjectViewModel extends BaseViewModel{

    protected $viewFields = array(
        'QuestionnaireExamSubject'=>array('*','_type'=>'LEFT'),

        'QuestionnaireExamSubjectType'=>array('type'=>'type_num','name'=>'type_name', '_on'=>'QuestionnaireExamSubject.type_id=QuestionnaireExamSubjectType.id'),
    );

    public function findAllByExamId($examId){
        return $this->where(array('QuestionnaireExamSubject.exam_id' => $examId))->select();
    }

    public function findAllByExam($exam){
        $in = $this->_sqlSubjectIds($exam);
        if($in){
            return $this->where(array('QuestionnaireExamSubject.id' => array('in', $in)))->order("field(QuestionnaireExamSubject.id, ".$in.")")->select();

        }else{
            return array();
        }
    }

    private function _sqlSubjectIds($Exam){
        if($Exam['subject_ids']){
            $ids = to_json_obj($Exam['subject_ids']);
            $in = "";
            foreach($ids as $index => $id){
                    $in = $in.$id.',';
            }
            $in = rtrim($in,',');
            return $in;
        }else{
            return '0';
        }
    }
}