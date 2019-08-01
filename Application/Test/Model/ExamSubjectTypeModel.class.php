<?php
/**
 * 操作用的模型
 */
namespace Test\Model;
use Think\Model;

class ExamSubjectTypeModel extends Model{
	public function findByType($type){
		return $this->where(array('type'=> $type))->find();
	}
}