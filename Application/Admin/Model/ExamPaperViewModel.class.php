<?php
namespace Admin\Model;
use Common\Model\BaseViewModel;
use Think\Model\ViewModel;
use Think\Page;

/**
 * 课程表
 * Class StudyScheduleViewModel
 * @package Study\Model
 */
class ExamPaperViewModel extends BaseViewModel{

	protected $viewFields = array(
		'ExamPaper'=>array('*','_type'=>'LEFT'),
		'User'=>array(
			'role_id',
			'gender'=>'user_gender',
			'mobile'=>'user_mobile',
			'headimgurl'=>'user_headimgurl',
			'realname'=>'user_realname',
			'_on'=>'ExamPaper.uid=User.uid','_type'=>'LEFT'),
		'Exam'=>array('type'=>'exam_title', '_on'=>'ExamPaper.exam_id=Exam.id','_type'=>'LEFT'),
		'Role'=>array('name'=>'role_name','_on'=>'User.role_id=Role.id', '_type'=>'LEFT'),
		'PartyBranch'=>array('name'=>'branch_name','_on'=>'User.branch_id=PartyBranch.id', '_type'=>'LEFT'),
		'Post'=>array('name'=>'post_name','_on'=>'User.post_id=Post.id', '_type'=>'LEFT')
	);
	public function findMyPageByUid($id){
		$where = array('ExamPaper.exam_id' => $id,'ExamPaper.status' => 1);
		return $this->findPage($where, 8, 'hand_time desc' );
	}

	public function findPageByUid($id){
		$where = array('ExamPaper.uid' => $id);
		return $this->findPage($where, 8, 'hand_time desc' );
	}




	public function findById($id){
		return $this->where('ExamPaper.id='.$id)->find();
	}

	public function findNext($examId){
		$list = $this->where('ExamPaper.exam_id='.$examId)->limit(1)->order('ExamPaper.hand_time asc')->select();
		return $list[0];
	}

	public function findNextByPaperId($examId,$paperId){
		$paper = $this->find($paperId);
		$list = $this->where('ExamPaper.exam_id='.$examId.' and ExamPaper.hand_time>'.$paper['hand_time'])->limit(1)->order('ExamPaper.hand_time asc')->select();
		return $list[0];
	}

	public function findNextByPaper($examId,$paper){
		$list = $this->where('ExamPaper.exam_id='.$examId.' and ExamPaper.hand_time>'.$paper['hand_time'])->limit(1)->order('ExamPaper.hand_time asc')->select();
		return $list[0];
	}

	public function findLastByPaperId($examId,$paperId){
		$paper = $this->find($paperId);
		$list = $this->where('ExamPaper.exam_id='.$examId.' and ExamPaper.hand_time<'.$paper['hand_time'])->limit(1)->order('ExamPaper.hand_time asc')->select();
		return $list[0];
	}

	public function findLastByPaper($examId,$paper){
		$list = $this->where('ExamPaper.exam_id='.$examId.' and ExamPaper.hand_time<'.$paper['hand_time'])->limit(1)->order('ExamPaper.hand_time asc')->select();
		return $list[0];
	}
}





