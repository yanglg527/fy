<?php
/**
 * 操作用的模型
 */
namespace Mob\Model;

use Common\Model\BaseViewModel;
use Think\Model;

class ExamPaperViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'ExamPaper'=>array('*','_type'=>'LEFT'),
        'User'=>array(
            'headimgurl'=>'user_headimgurl',
            'realname'=>'user_realname',
            '_on'=>'ExamPaper.uid=User.uid','_type'=>'LEFT'),
        'Exam'=>array('type'=>'exam_title', '_on'=>'ExamPaper.exam_id=Exam.id','_type'=>'LEFT'),
         'PartyBranch' => array('name', '_on'=>'PartyBranch.id=ExamPaper.branch_id', '_type'=>'LEFT'),
		
    );

    public function findById($id){
        return $this->where('ExamPaper.id='.$id)->find();
    }

    public function findNext($examId){
        $list = $this->where('ExamPaper.exam_id='.$examId.' and ExamPaper.status=1')->limit(1)->order('ExamPaper.hand_time asc')->select();
        return $list[0];
    }

}