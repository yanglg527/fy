<?php
/**
 * 操作用的模型
 */
namespace Questionnaire\Model;

use Common\Model\BaseModel;
use Think\Model;

class QuestionnaireExamPaperModel extends BaseModel
{


    /**
     * 获取长度
     * @param $Exam
     * @return int
     */
    public function countScore($examPager)
    {

        $scores = to_json_obj($examPager['answer_scores']);
        $examScore = $this->_countScore($scores);
        $examPager['exam_score'] = $examScore;
        return $examPager;
    }


    private function _countScore($scores){
        $examScore = 0;
        foreach($scores as $score){
            if(is_array($score)){
                foreach($score as $s){
                    $examScore = (int)$s + $examScore;
                }
            }else{
                $examScore = (int)$score + $examScore;
            }
        }
        return $examScore;
    }

    public function changeScore($examPager, $subjectId,$score){
        $scores = to_json_obj($examPager['answer_scores']);
        foreach($score as $index => $s){
            $score[$index] = (int)$s;
        }
        $scores[''.$subjectId] = $score;//获得分数
        $examPager['answer_scores'] = to_json_string($scores);
        $examScore = $this->_countScore($scores);
        $examPager['exam_score'] = $examScore;
        return $examPager;
    }


}