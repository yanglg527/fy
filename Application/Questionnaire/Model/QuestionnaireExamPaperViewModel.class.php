<?php
/**
 * 操作用的模型
 */
namespace Questionnaire\Model;

use Common\Model\BaseViewModel;
use Think\Model;

class QuestionnaireExamPaperViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'QuestionnaireExamPaper'=>array('*','_type'=>'LEFT'),
        'User'=>array(
            'headimgurl'=>'user_headimgurl',
            'realname'=>'user_realname',
            '_on'=>'QuestionnaireExamPaper.uid=User.uid','_type'=>'LEFT'),
        'QuestionnaireExam'=>array('type'=>'exam_title', '_on'=>'QuestionnaireExamPaper.exam_id=QuestionnaireExam.id','_type'=>'LEFT')
    );

    public function findById($id){
        return $this->where('ExamPaper.id='.$id)->find();
    }

    public function findNext($examId){
        $list = $this->where('QuestionnaireExamPaper.exam_id='.$examId.' and QuestionnaireExamPaper.status=1')->limit(1)->order('QuestionnaireExamPaper.hand_time asc')->select();
        return $list[0];
    }

}