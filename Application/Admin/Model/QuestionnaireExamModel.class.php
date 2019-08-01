<?php 
namespace Admin\Model;
use Think\Model;

/**
 * 测试
 * Class ExamModel
 * @package exam\Model
 */
class QuestionnaireExamModel extends Model{
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
	 * @param $exam
	 * @param $preSubjectId在这个 id 后面插入新题
	 * @param $subjectId
	 * @return \string对象转
	 */
	public function insertSubjectId($exam, $preSubjectId, $subject)
	{
		$subjectId = $subject['id'];
		$ids = $this->_getSubjectIds($exam);
		foreach ($ids as $index => $id) {
			if ($id == $preSubjectId) {
				array_splice($ids,$index + 1,0,$subjectId);
				break;
			}
		}
//放入题目
		$exam['subject_ids'] = to_json_string($ids);
		$exam['subject_count'] = sizeof($ids);

//计算试卷总分
		$scores = $this->_getScoress($exam);
		$scores[''.$subjectId] =(int)$subject['score'];
		$exam['scores'] = to_json_string($scores);
		$exam['score'] = $this->countScore($ids, $scores);
		return $exam;
	}
	/**
	 * 交换位置
	 * @param $exam
	 * @param $changeSubjectId1
	 * @param $changeSubjectId2
	 * @return \string对象转
	 */
	public function changeSubjectId($exam, $changeSubjectId1, $changeSubjectId2)
	{
		$ids = $this->_getSubjectIds($exam);
		$index1 = -1;
		$index2 = -1;
		foreach ($ids as $index => $id) {
			if ($id == $changeSubjectId1) {
				$index1 = $index;
				if($index2 > -1){//如果已经知道2的位置
					$ids[$index2] = $changeSubjectId1;
					$ids[$index1] = $changeSubjectId2;
					break;
				}
			}else if($id == $changeSubjectId2){
				$index2 = $index;
				if($index1 > -1){//如果已经知道2的位置
					$ids[$index2] = $changeSubjectId1;
					$ids[$index1] = $changeSubjectId2;
					break;
				}
			}
		}


		$idsJson = to_json_string($ids);
		$exam['subject_ids'] = $idsJson;
		$exam['subject_count'] = sizeof($ids);
		return $exam;
	}

	public function downSubjectId($exam, $subject){
		$id = $subject['id'];
		$ids = to_json_obj($exam['subject_ids']);
		$size = count($ids);
		foreach($ids as $index => $sid){
			if($sid == $id){
				if($index < $size-1){
					$tempId = $ids[$index + 1];
					$ids[$index + 1] = $id;
					$ids[$index] = $tempId;
				}
				break;
			}
		}
		$idsJson = to_json_string($ids);
		$exam['subject_ids'] = $idsJson;
		$exam['subject_count'] = sizeof($ids);
		return $exam;
	}

	public function upSubjectId($exam, $subject){
		$id = $subject['id'];
		$ids = to_json_obj($exam['subject_ids']);
		foreach($ids as $index => $sid){
			if($sid == $id){
				if($index > 0){
					$tempId = $ids[$index - 1];
					$ids[$index - 1] = $id;
					$ids[$index] = $tempId;
				}
				break;
			}
		}
		$idsJson = to_json_string($ids);
		$exam['subject_ids'] = $idsJson;
		$exam['subject_count'] = sizeof($ids);
		return $exam;
	}

	/**添加Id
	 * @param $exam
	 * @param $subjectId
	 * @return \string对象转
	 */
	public function addSubjectId($exam, $subject){
		//放入题目
		$subjectId = $subject['id'];
		$ids = $this->_getSubjectIds($exam);
		array_push($ids, $subjectId);
		$idsJson = to_json_string($ids);
		$exam['subject_ids'] = $idsJson;
		$exam['subject_count'] = sizeof($ids);

		//计算总分
		$scores = $this->_getScoress($exam);
		$scores[''.$subjectId] = (int)$subject['score'];
		$exam['scores'] = to_json_string($scores);
		$exam['score'] = $this->countScore($ids, $scores);
		return $exam;
	}

	/**
	 * 移除 id
	 * @param $exam
	 * @param $subjectId
	 * @return \string对象转
	 */
	public function removeSubjectId($exam, $subject)
	{
		$subjectId = $subject['id'];
		$ids = $this->_getSubjectIds($exam);

		foreach ($ids as $index => $id) {
			if ($id == $subjectId) {
				array_splice($ids,(int)$index,1); //删除第n个元素
				break;
			}
		}
		$idsJson = to_json_string($ids);
		$exam['subject_ids'] = $idsJson;
		$exam['subject_count'] = sizeof($ids);

		//计算总分
		$scores = $this->_getScoress($exam);
		$scores[''.$subjectId] = 0;
		$exam['scores'] = to_json_string($scores);
		$exam['score'] = $this->countScore($ids, $scores);
		return $exam;
	}

	public function changeSubject($exam, $subject){
		//计算总分
		$ids = $this->_getSubjectIds($exam);
		$score = $subject['status'] == -1 ? 0 : $subject['score'];//如果题目已经删除，则0分

		if($subject['type_num'] == 'blank'){
			$blanks = to_json_obj($subject['right_answer']);//转换出答案json格式，用于判断填空数量
			foreach($blanks as $index => $blank){
				$scoreSub[(int)$index] = $score;
			}
			$score = $scoreSub;
		}

		$scores = $this->_getScoress($exam);
		$scores[''.$subject['id']] = $score;
		$exam['scores'] = to_json_string($scores);
		$exam['score'] = $this->countScore($ids, $scores);
		return $exam;
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
	public function getIndexCondition($subject, $search){
		$condition['status'] =1;
		$condition['type_name'] =1;

		if($search){
			$condition['concat(Exam.title,Exam.intro)'] =array("like","%$search%");
		}

		if($subject !=0) {
			$condition['course_id'] = $subject;
		}
		return $condition;
	}

}