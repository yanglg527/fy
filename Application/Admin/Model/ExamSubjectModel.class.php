<?php
/**
 * 操作用的模型
 */
namespace Admin\Model;
use Think\Model;

class ExamSubjectModel extends Model{

    protected $_validate = array(
        array('title','require','请先填写题目内容'), //默认情况下用正则进行验证
        array('title','1,2000','题目不能超过2000字',3,'length'), // 验证标题长度
        array('content','require','请先填写题目内容'), //默认情况下用正则进行验证
        array('content','1,20000','内容不能超过20000字',3,'length'), // 验证标题长度
//        array('url','require','请先填写链接地址'), //默认情况下用正则进行验证
    );

    public function findAllByExamId($examId){
        $this->where(array('exam_id' => $examId))->select();
    }

}