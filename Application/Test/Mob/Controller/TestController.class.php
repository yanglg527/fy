<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Mob\Controller;

use Common\Controller\BaseController;

class TestController extends BaseController {

	function _initialize() {
		parent::_initialize();
		$this -> setHeader('微测试');
	}
	public function test(){
        $str='zhfy@2018';
        echo md5($str);
    }
	public function ajaxLoadData() {
		// 获取分页参数(上一次的最后一条数据的排序字段)
		$type = I('type', 0);
		$published_at = I('lastItem');
		if ($published_at == 0 || $published_at == null) {
			$published_at = time();
		}

		$where = array('Exam.status' => 1, 'Exam.type' => $type, 'Exam.publish_time' => array('lt', $published_at));
		$page = D('ExamView') -> findPage($where, 15, 'Exam.publish_time desc');

		ajaxMsg(0, $type, $page['list']);
	}

	//测试
	public function index() {
		$this -> check_wx_redirect('test_index');
		//判断是否重定向登录
		$user = user();
		$list = D("ExamView") -> where(array("Exam.status" => 1, 'Exam.end_time' => array('gt', time()), 'Exam.type' => 0)) -> order("Exam.create_time desc") -> select();

		$this -> setHeader('微测试');
		$this -> setTitle('微测试');
		$this -> assign("list", $list);

		$this -> display();
	}


	private function _setExam($exam) {
		//题目ids
		$ids = to_json_obj($exam['subject_ids']);
		if (count($ids) == 0) {
			$this -> setTitle($exam['title']);
			$this -> setHeader($exam['title']);
			$this -> display();
			exit ;
		}
		$ids = implode(",", $ids);

		if ($ids) {
			$selectABCD = array('A','B','C','D');
			$subjects = D('ExamSubjectView') -> where("ExamSubject.id in($ids)") -> order("field(ExamSubject.id, $ids)") -> select();

			foreach ($subjects as $index => $subject) {
				if ($subject['type_num'] == 'choice') {//如果是多选题
					$subject['right_answer_str'] = '   ';
					//解析答案内容
					$subject['answer'] = json_decode($subject['content'], true);
				} elseif ($subject['type_num'] == 'singleChoice') {//如果是单选题
				
				
					$rightA = to_json_obj($subject['right_answer']);
					foreach($rightA as $iiii=>$right){
						if($right == 1){
							$subject['right_answer_str'] = $selectABCD[$iiii];
						}
					}
					
					//解析答案内容
					$subject['answer'] = json_decode($subject['content'], true);
				} elseif ($subject['type_num'] == 'blank') {//如果是填空题
					//解析html 转义字符

					$answerCount = substr_count($subject['content'], 'input-blank');
					$subject['answer_count'] = $answerCount;
					$subject['content'] = $subject['content'] ? htmlspecialchars_decode($subject['content']) : "";
					$answers = to_json_obj($subject['right_answer']);
					foreach ($answers as $answer) {
						$subject['content'] = str_replace("value=\"" . $answer . "\"", "value=\"\"", $subject['content']);
					}

				} elseif ($subject['type_num'] == 'answer') {//如果是简答题
					//解析html 转义字符
					$subject['content'] = $subject['content'] ? htmlspecialchars_decode($subject['content']) : "";
				}
				//重新赋值
				$subjects[(int)$index] = $subject;
			}

			$this -> assign('has_answered', false);
			$this -> assign('subjects', $subjects);
			$this -> setTitle($exam['title']);
			$this -> setHeader($exam['title']);
		} else {
		}
	}

	public function zhenti_detail() {
		$id = I('id');
		$id = $id ? $id : I('state');
		$this -> check_wx_redirect('test_detail', $id);
		//判断是否重定向登录
		$exam = D('ExamView') -> where("Exam.id=$id and Exam.status=1") -> find();
		$user = user();
		if ($exam) {
			

			$this -> assign('detail', $exam);


			$this -> _setExam($exam);

		} else {
			$this -> setTitle("找不到该测试");
			$this -> setHeader("找不到该测试");
		}

        if ($user && $exam) {
            $click_log=D('ExamClickLog')->where(array('uid'=>$user['uid'],'exam_id'=>$exam['id']))->find();
            if (!$click_log) {
                update_user_score($user["uid"], 2, 5,'真题阅读阅读');
                D('ExamClickLog')->add(array('uid'=>$user['uid'],'exam_id'=>$exam['id'],'score'=>2,'create_time'=>time()));
            }
            
        }
		if(!$exam){
			$this->redirect(__ROOT__."/error3.html");
		}
		$this -> display();
	}

	public function result() {
		$id = I('id');
		$id = $id ? $id : I('state');
		$this -> check_wx_redirect('test_detail', $id);
		$exam = D('ExamView') -> where("Exam.id=$id") -> find();
		
		if ($exam) {
			
			//所有人的考试记录
			$all = D('ExamPaperView')->group('ExamPaper.uid')->order('ExamPaper.branch_id asc')->select();
			
			 $all = D()->query("select user.realname,user.headimgurl,user.branch_id,
			 party_branch.name as branch_name,
(select sum(exam_paper.total_count) from exam_paper where exam_paper.uid = user.uid) AS total_count,
(select sum(exam_paper.right_count) from exam_paper where exam_paper.uid = user.uid) AS right_count
from user LEFT JOIN party_branch 
         ON party_branch.id=user.branch_id  
          where (user.status=1) order by user.branch_id asc");
			
			$branchPaper = array();
			foreach($all as $item){
				if($item['total_count']>0){
				$branch_id = $item['branch_id'];
				$branch_id = $branch_id?'branch_0':'branch_'.$branch_id;
				$branch_name = $branch_id?'其他':$item['branch_name'];
				$branchPaper['$branch_id']['name'] = $branch_name;
				$branchPaper['$branch_id']['list'][] = $item;
			}
			}
			$this -> assign('allPaper', $branchPaper);
			
			
			$user = user();
			$uid = $user['uid'];
			$this -> assign('detail', $exam);
			$paper = D('ExamPaperView') -> where("Exam.id=$id and ExamPaper.uid=$uid") -> find();
			if ($paper) {//考过试的显示之前的试卷
			//题目ids
				$ids = implode(",", to_json_obj($exam['subject_ids']));
				if ($ids) {

					$subjects = D('ExamSubjectView') -> where("ExamSubject.id in($ids)") -> order("field(ExamSubject.id, $ids)") -> select();

					$userScore = to_json_obj($paper['answer_scores']);
					$userAnswer = to_json_obj($paper['answers']);

					foreach ($subjects as $index => $subject) {
						/***********放入用户答案**********/
						$subject['user_score'] = $userScore['' . $subject['id']];
						$subject['user_answer'] = $userAnswer['' . $subject['id']];

						/***********放入原题内容**********/
						if ($subject['type_num'] == 'choice') {//如果是多选题

							//用户回答
							$userA = array(0, 0, 0, 0);
							foreach ($userAnswer['' . $subject['id']] as $select) {
								$userA[(int)$select] = 1;
							}
							$subject['user_answer'] = $userA;

							//解析显示的正确答案(A、B、C)
							$right_answerStr = array('A', 'B', 'C', 'D');
							$show = "";
							$answer = json_decode($subject['right_answer']);
							$right_answerS = array();
							$righr_answer_indexs = array();
							foreach ($answer as $index2 => $value) {
								if ($value == 1) {
									$righr_answer_indexs[(string)$index2] = 1;
									array_push($right_answerS, $right_answerStr[$index2]);
								}
							}
							$len = sizeof($right_answerS);
							foreach ($right_answerS as $index2 => $s) {
								if ($index2 < $len - 1) {
									$show = $show . $s . '、';
								} else {
									$show = $show . $s;
								}
							}
							$subject['right_answer_str'] = $show;
							$subject['righr_answer_indexs'] = $righr_answer_indexs;

							//解析答案内容
							$subject['answer'] = json_decode($subject['content'], true);
						} elseif ($subject['type_num'] == 'singleChoice') {//如果是单选题

							//用户回答
							$subject['user_answer'] = $userAnswer['' . $subject['id']];

							//解析显示正确答案(A)
							$right_answerStr = array('A', 'B', 'C', 'D');
							$answer = json_decode($subject['right_answer'], true);
							$righr_answer_indexs = array();
							foreach ($answer as $index2 => $value) {
								if ($value == 1) {
									$righr_answer_indexs[(string)$index2] = 1;
									$show = $right_answerStr[$index2];
								}
							}
							$subject['right_answer_str'] = $show;
							$subject['righr_answer_indexs'] = $righr_answer_indexs;

							//解析答案内容
							$subject['answer'] = json_decode($subject['content'], true);
						} elseif ($subject['type_num'] == 'blank') {//如果是填空题

							//用户回答
							$subject['user_answer'] = $userAnswer['' . $subject['id']];

							//解析html 转义字符
							$answerCount = substr_count($subject['content'], 'input-blank');
							$subject['answer_count'] = $answerCount;
							$subject['content'] = $subject['content'] ? htmlspecialchars_decode($subject['content']) : "";

							$answer = to_json_obj($subject['right_answer']);
							$righA = "";
							$userAnswerS = "";
							$showContent = $subject['content'];
							foreach ($answer as $index2 => $value) {
								$ua = $subject['user_answer'][$index2];
								if (!$ua) {
									$ua = "&nbsp;&nbsp;";
								}
								$showContent = str_replace("value=\"" . $value . "\"", "value=\"" . $ua . "\" disabled=\"true\"", $showContent);
								//                            $userAnswerS = $userAnswerS."<span style='padding: 0 10px;border-bottom: 1px solid black;margin-left: 10px;margin-right: 10px'>".$ua."</span>";
								$righA = $righA . '<span style="display:block">' . ($index2 + 1) . '.' . $value . "</span>";
								$subject['content'] = str_replace("value=\"" . $value . "\"", "value=\"" . $ua . "\"", $subject['content']);

								$righA = $righA . '<span style="display:block">' . ($index2 + 1) . '.' . $value . "</span>";
							}
							$subject['show_content'] = str_replace_once("<input class=", $userAnswerS . "<input class=", $showContent);

							$subject['right_answer_str'] = $righA;

							if (is_array($subject['user_score'])) {
								foreach ($subject['user_score'] as $index1 => $value) {
									if ($index1 > 0) {
										$subject['user_score'][0] = $subject['user_score'][0] + $subject['user_score'][$index1];
									}
								}
							}

						} elseif ($subject['type_num'] == 'answer') {//如果是简答题
							//解析html 转义字符
							$subject['content'] = $subject['content'] ? htmlspecialchars_decode($subject['content']) : "";
							$subject['user_answer'] = $userAnswer['' . $subject['id']];
						}
						//重新赋值
						$subjects[(int)$index] = $subject;
					}

					$this -> assign('exam', $exam);
					$this -> assign('examPaper', $paper);
					$this -> assign('subjects', $subjects);

					$this -> assign('is_show', true);
					$this -> assign('has_answered', true);
					$this -> setTitle($exam['title']);
					$this -> setHeader($exam['title']);
				} else {
					$this -> setTitle($exam['title']);
					$this -> setHeader($exam['title']);
					$this -> display();
					exit ;
				}

			} else {
				redirect(__ROOT__ . "/Mob/Test/detail?id=$id");
			}
		}

		$this -> display();

	}

	public function detail() {
		$id = I('id');
		$id = $id ? $id : I('state');
		$this -> check_wx_redirect('test_detail', $id);
		//判断是否重定向登录
		$exam = D('ExamView') -> where("Exam.id=$id and Exam.status=1") -> find();

		if ($exam) {
			$user = user();

			$this -> assign('detail', $exam);

			$uid = uid();
			$paper = D('ExamPaperView') -> where("Exam.id=$id and ExamPaper.uid=$uid") -> find();
			if ($paper) {//考过试的显示之前的试卷

				redirect(__ROOT__ . "/Mob/Test/result?id=".$id);
				exit();

			} else {
				$this -> _setExam($exam);

			}
		} else {
			$this -> setTitle("找不到该测试");
			$this -> setHeader("找不到该测试");
		}

		if(!$exam){
			$this->redirect(__ROOT__."/error3.html");
		}
		$this -> display();
	}

	/**
	 * 检测测试状态
	 * @param $exam
	 */
	private function _checkExamStatus($exam) {
		if (!$exam) {
			ajaxMsg(1, '抱歉，没有找到这份测试');
		}
		$examId = $exam['id'];
		$uid = uid();
		$examPaper = D('ExamPaper') -> where("exam_id=$examId and uid=$uid") -> find();
		if ($examPaper) {
			ajaxMsg(1, '你已经考过试了');
		} elseif ($exam['status'] == -1) {
			ajaxMsg(3, '抱歉，该测试已经被删除了');
		} elseif ($exam['status'] == 0) {
			ajaxMsg(3, '抱歉，该测试正在编辑中，请刷新重试');
		} elseif ($exam['status'] == -2) {
			ajaxMsg(4, '抱歉，该测试已经被禁用了');
		} elseif ($exam['end_time'] + 24*60*60 < time()) {
			ajaxMsg(4, '抱歉，考试已结束');
		}

		//		$user = user();
		//		$roles = D('ExamRole') -> where(array('exam_id' => $exam['id'])) -> select();
		//		$count = 0;
		//		foreach ($roles as $role) {
		//			if ($role['role_id'] == $user['role_id']) {
		//				$count++;
		//			}
		//		}
		//		if ($count == 0) {
		//			ajaxMsg(1, "你没有参与考试的权限");
		//		}

	}

	/**
	 * 交试卷
	 * @param $exam_id
	 */
	public function ajaxHand() {
		$this -> check_wx_redirect();
		//判断是否重定向登录
		$id = I('id');
		$exam = D('Exam') -> find($id);

		$this -> _checkExamStatus($exam);
		//检测权限

		$examPaperModel = D('ExamPaper');

		$examPaper = array('exam_id' => $id, );
		//获取该试卷所有题目
		$ids = implode(",", to_json_obj($exam['subject_ids']));
		$subjects = D('ExamSubjectView') -> where("ExamSubject.id in($ids)") -> order("field(ExamSubject.id, $ids)") -> select();

		$answerCount = 0;
		$choiceCount = 0;

		$userAnswers = array();
		//用户回答的答案{'id':['answer'],'id':[...]...}
		$wrongAnswerIds = array();
		//答错的题目 id[id,id,id...]
		$rightAnswerIds = array();
		//答对的题目 id
		$subjectVersion = array();
		//题目版本号
		$scores = array();
		//{'id':2,'id':3,'id':[2,2]...}
		foreach ($subjects as $subject) {
			$answerCount++;
			$subjectId = $subject['id'];
			$subjectVersion['' . $subjectId] = $subject['version'];
			//正确答案解析成对象
			$right_answers = to_json_obj($subject['right_answer']);
			//题目类型
			$subjectType = $subject['type_num'];
			if ($subjectType == 'text') {//如果是 标题得分为0，不记录答对答错
				$answerCount--;
			} elseif ($subjectType == 'trueFalse') {//如果是判断
				$choiceCount++;
				//单选题提交的答案格式为 subjet-id[]:['0~3']
				$userAnswer = $_POST['subject-' . $subjectId];
				$userAnswers['' . $subjectId] = $userAnswer;

				//获取正确答案的长度
				$rightAnswerLen = 0;
				foreach ($right_answers as $right_answer) {
					if ((int)$right_answer == 1) {
						$rightAnswerLen = $rightAnswerLen + 1;
					}
				}

				if (count($userAnswer) == $rightAnswerLen && $rightAnswerLen != 0) {//判断是否和答案数量一致
					if ((int)$right_answers[(int)$userAnswer[0]] == 1) {//答案正确
						$scores['' . $subjectId] = array((int)$subject['score']);
						//获得分数
						//放到正确题组
						array_push($rightAnswerIds, $subjectId);
					} else {
						$scores['' . $subjectId] = array(0);
						//放到错误题组
						array_push($wrongAnswerIds, $subjectId);
					}
				} else {
					$scores['' . $subjectId] = array(0);
					//放到错误题组
					array_push($wrongAnswerIds, $subjectId);
				}

			} elseif ($subjectType == 'singleChoice') {//如果是单选题
				$choiceCount++;
				//单选题提交的答案格式为 subjet-id[]:['0~3']
				$userAnswer = $_POST['subject-' . $subjectId];
				$userAnswers['' . $subjectId] = $userAnswer;

				//获取正确答案的长度
				$rightAnswerLen = 0;
				foreach ($right_answers as $right_answer) {
					if ((int)$right_answer == 1) {
						$rightAnswerLen = $rightAnswerLen + 1;
					}
				}

				if (count($userAnswer) == $rightAnswerLen && $rightAnswerLen != 0) {//判断是否和答案数量一致
					if ((int)$right_answers[(int)$userAnswer[0]] == 1) {//答案正确
						$scores['' . $subjectId] = array((int)$subject['score']);
						//获得分数
						//放到正确题组
						array_push($rightAnswerIds, $subjectId);
					} else {
						$scores['' . $subjectId] = array(0);
						//放到错误题组
						array_push($wrongAnswerIds, $subjectId);
					}
				} else {
					$scores['' . $subjectId] = array(0);
					//放到错误题组
					array_push($wrongAnswerIds, $subjectId);
				}

			} elseif ($subjectType == 'choice') {//如果是多选题
				$choiceCount++;
				//多选题提交的答案格式为 subjet-id[]:['0~3','0~3'...]
				$userAnswer = $_POST['subject-' . $subjectId];
				$userAnswers['' . $subjectId] = $userAnswer;

				//获取正确答案的长度
				$rightAnswerLen = 0;
				foreach ($right_answers as $right_answer) {
					if ((int)$right_answer == 1) {
						$rightAnswerLen = $rightAnswerLen + 1;
					}
				}
				if (count($userAnswer) == $rightAnswerLen && $rightAnswerLen != 0) {//判断是否和答案数量一致
					$isAllRight = true;
					foreach ($userAnswer as $answer) {
						if ((int)$right_answers[(int)$answer] == 0) {//如果有一题答错
							$isAllRight = false;
							break;
						}
					}
					if ($isAllRight) {//如果全对
						$scores['' . $subjectId] = array((int)$subject['score']);
						//获得分数
						//放到正确题组
						array_push($rightAnswerIds, $subjectId);
					} else {
						$scores['' . $subjectId] = array(0);
						//放到错误题组
						array_push($wrongAnswerIds, $subjectId);
					}
				} else {
					$scores['' . $subjectId] = array(0);
					//放到错误题组
					array_push($wrongAnswerIds, $subjectId);
				}
				

			} elseif ($subjectType == 'blank') {//如果是填空
				//填空题提交的答案格式为 subjet-id[]:['答案1','答案2'...]
				$userAnswer = $_POST['subject-' . $subjectId];
				$userAnswers['' . $subjectId] = $userAnswer;

				//填空题正确答案格式[[答案1a;;答案1b],[],[]...]
				$blankScore = array();
				$isAllRight = true;
				foreach ($right_answers as $index => $right_answer) {
					$right_answer_array = explode(" ", $right_answer);
					$blankScore[$index] = 0;
					if (is_array($right_answer_array)) {
						foreach ($right_answer_array as $r) {//根据 ;; 划分一个填空多个答案
							if ($userAnswer[$index] == $r) {
								$blankScore[$index] = (int)$subject['score'];
								//只要匹配上一个答案
								break;
							}
						}
					} else {
						if ($userAnswer[$index] == $right_answer) {
							$blankScore[$index] = (int)$subject['score'];
							//只要匹配上一个答案
							break;
						}
					}

					if ($blankScore[$index] == 0) {//只要有一个填空错了就放入错题组
						$isAllRight = false;
					}
				}

				$rightAnswerLen = count($right_answers);
				if (count($userAnswer) == $rightAnswerLen && $rightAnswerLen != 0) {//判断是否和答案数量一致
					if ($isAllRight) {//如果全对
						//放到正确题组
						array_push($rightAnswerIds, $subjectId);
					} else {
						$scores['' . $subjectId] = array(0);
						//放到错误题组
						array_push($wrongAnswerIds, $subjectId);
					}
				} else {
					$scores['' . $subjectId] = array(0);
					//放到错误题组
					array_push($wrongAnswerIds, $subjectId);
				}
				$scores['' . $subjectId] = $blankScore;
				//获得分数
			} elseif ($subjectType == 'answer') {//如果是简答题
				//简答题提交的答案格式为 subjet-id[]:['答案']
				$userAnswers['' . $subjectId] = $_POST['subject-' . $subjectId];

			}
		}

		if ($choiceCount == $answerCount) {
			$examPaper['is_corrected'] = 1;
		}

		$user = user();
		$examPaper['total_count'] = $answerCount;
		$examPaper['right_count'] = count($rightAnswerIds);
		$examPaper['uid'] = $user['uid'];
		$examPaper['branch_id'] = $user['branch_id'];
		$examPaper['hand_time'] = time();
		$examPaper['answers'] = to_json_string($userAnswers);
		$examPaper['wrong_answer_ids'] = to_json_string($wrongAnswerIds);
		$examPaper['right_answer_ids'] = to_json_string($rightAnswerIds);
		$examPaper['answer_scores'] = to_json_string($scores);
		$examPaper['subject_version'] = to_json_string($subjectVersion);
		$examPaper['create_time'] = time();
		$examPaper['status'] = 1;
		$examPaper['exam_version'] = $exam['version'];

		$examPaper = $examPaperModel -> countScore($examPaper);
		$examPaperModel -> add($examPaper);

		//考试人数+1
		$exam['exam_count'] = $exam['exam_count'] + 1;
		D('Exam') -> save($exam);
		ajaxMsg(0, 'success');
	}

}
