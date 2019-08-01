<?php
/**
 * 操作用的模型
 */
namespace Questionnaire\Model;
use Think\Model;

class QuestionnaireExamSubjectTypeModel extends Model{
	public function findByType($type){
		return $this->where(array('type'=> $type))->find();
	}
}