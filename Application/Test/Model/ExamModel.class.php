<?php
/**
 * 操作用的模型
 */
namespace Test\Model;

use Think\Model;

class ExamModel extends Model
{


    /**
     * 计算测试总分
     */
    public function countScore($ids, $scores){
        $score = 0;
        foreach($ids as $id){
            $sd = $scores[''.$id];
            if(is_array($sd)){
                foreach($sd as $s){
                    $score = $score + (int)$s;
                }
            }else{
                $score = $score + (int)$sd;
            }
        }
        return (int)$score;
    }

    public function findById($id)
    {
        return $this->where(array('id' => $id, 'status'=>array('gt', -1)))->find();
    }


    public function findByIdAndUid($id, $uid)
    {
        return $this->where(array('id' => $id, 'uid' => $uid, 'status'=>array('gt', -1)))->find();
    }

    private function _getScoress($exam)
    {
        $ids = to_json_obj($exam['scores']);

        if (is_array($ids)) {
            return $ids;
        } else {
            return array();
        }
    }

    private function _getSubjectIds($exam)
    {
        $ids = to_json_obj($exam['subject_ids']);

        if (is_array($ids)) {
            return $ids;
        } else {
            return array();
        }
    }



    /**
     * 获取长度
     * @param $exam
     * @return int
     */
    public function countSubjects($exam)
    {
        $ids = $this->_getSubjectIds($exam);

        return sizeof($ids);
    }


    /**
     * @link    处理index页面的条件
     * @param   $subject 科目的id
     * @param   $search 检索内容
     * @return  array
     * */
    public function getIndexCondition($subject=null, $search=null,$grade=null){
        $condition['status'] =1;
        $condition['type_name'] =1;
        $condition['Exam.city_id'] =current_city_id();

        if($search){
            $condition['concat(Exam.title,Exam.intro)'] =array("like","%$search%");
        }
        if($grade != 0){
            $condition['grade_id'] = $grade;
        }

        if($subject != 0) {
            $condition['course_id'] = $subject;
        }
        return $condition;
    }



}