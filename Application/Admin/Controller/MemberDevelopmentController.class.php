<?php
namespace Admin\Controller;

use Admin\Model\SignInLogViewModel;
use Common\Controller\BaseController;
use Think\Controller;

/**
 * 系统设置
 * Class SystemController
 * @package Admin\Controller
 */
class MemberDevelopmentController extends BaseAdminController {
	function _initialize() {
		parent::_initialize();
		// TODO: Change the autogenerated stub
		$this -> set_sidebar_module('MemberDevelopment');
	}

	// 入党申请列表
	public function userJoinApplyList() {
		$this -> set_sidebar_sub('userJoinApplyList');
		//权限展示列表
		$admin = admin();
		$auth = session_auth();
		$map = array();
		if ($auth == 2) {//总支
			$map['PartyBranch.branch_hq_id'] = $admin['branch_hq_id'];
		}
		if ($auth == 3) {//总支
			$map['User.branch_id'] = $admin['branch_id'];
		}

		//权限展示列表
		$page = D('UserJoinApplyView') -> findPage($map, 15, 'UserJoinApply.create_time desc');
		$this -> assign('page', $page);
		$this -> display("userJoinApplyList");
	}

	public function userReport() {
		
		$this -> set_sidebar_sub('userReport');
		$year = I('year');
		$branch_id = I('branch_id');
		$organiation_id = I('organiation_id');
		$keyword = I('keyword');
		$search['year'] = $year;
		$search['branch_id'] = $branch_id;
		$search['organiation_id'] = $organiation_id;
		$search['keyword'] = $keyword;
		if ($branch_id) {
			$map['User.branch_id'] = $branch_id;
		}
		if ($organiation_id) {
			$map['User.organiation_id'] = $organiation_id;
		}
		if ($keyword) {
			$map['User.realname'] = array("like", "%$keyword%");
			$this -> assign('keyword', $keyword);
		}
		if ($year) {
			$year = date('Y', time());
			$yearTime = strtotime($year . '-1-1');
			$nextYearTime = strtoTime(($year + 1) . '-1-1') - 1;
			$map['UserReport.create_time'] = array('between', array($yearTime, $nextYearTime));
		}
		$map['UserReport.status'] = array('gt', -1);

		$page = D('Mob/UserReportView') -> findPage($map, 15, 'UserReport.create_time desc');
		$this -> assign('page', $page);
		$this -> display();
	}

	public function ajaxDecReport() {
		$id = I('id');
		D('UserReport') -> where(array('id' => $id)) -> save(array('status' => -1));
		ajaxMsg(0, '删除成功');
	}
	
	public function userExam() {
		
		$this -> set_sidebar_sub('userExam');
		$year = I('year');
		$branch_id = I('branch_id');
		$organiation_id = I('organiation_id');
		$keyword = I('keyword');
		$search['year'] = $year;
		$search['branch_id'] = $branch_id;
		$search['organiation_id'] = $organiation_id;
		$search['keyword'] = $keyword;
		if ($branch_id) {
			$map['User.branch_id'] = $branch_id;
		}
		if ($organiation_id) {
			$map['User.organiation_id'] = $organiation_id;
		}
		if ($keyword) {
			$map['User.realname'] = array("like", "%$keyword%");
			$this -> assign('keyword', $keyword);
		}
		if ($year) {
			$year = date('Y', time());
			$yearTime = strtotime($year . '-1-1');
			$nextYearTime = strtoTime(($year + 1) . '-1-1') - 1;
			$map['UserExam.create_time'] = array('between', array($yearTime, $nextYearTime));
		}
		$map['UserExam.status'] = array('gt', -1);

		$page = D('Mob/UserExamView') -> findPage($map, 15, 'UserExam.create_time desc');
		$this -> assign('page', $page);
		$this -> display();
	}

	public function ajaxDecExam() {
		$id = I('id');
		D('UserExam') -> where(array('id' => $id)) -> save(array('status' => -1));
		ajaxMsg(0, '删除成功');
	}
	
	public function userAdvice() {
		
		$this -> set_sidebar_sub('userAdvice');
		$year = I('year');
		$branch_id = I('branch_id');
		$organiation_id = I('organiation_id');
		$keyword = I('keyword');
		$search['year'] = $year;
		$search['branch_id'] = $branch_id;
		$search['organiation_id'] = $organiation_id;
		$search['keyword'] = $keyword;
		if ($branch_id) {
			$map['User.branch_id'] = $branch_id;
		}
		if ($organiation_id) {
			$map['User.organiation_id'] = $organiation_id;
		}
		if ($keyword) {
			$map['User.realname'] = array("like", "%$keyword%");
			$this -> assign('keyword', $keyword);
		}
		if ($year) {
			$year = date('Y', time());
			$yearTime = strtotime($year . '-1-1');
			$nextYearTime = strtoTime(($year + 1) . '-1-1') - 1;
			$map['UserAdvice.create_time'] = array('between', array($yearTime, $nextYearTime));
		}
		$map['UserAdvice.status'] = array('gt', -1);

		$page = D('Mob/UserAdviceView') -> findPage($map, 15, 'UserAdvice.create_time desc');
		$this -> assign('page', $page);
		$this -> display();
	}

	public function ajaxDecAdvice() {
		$id = I('id');
        $branch_id = I('branch_id');
        $uid = I('uid');
        //只能是这条记录所在支部的管理员有删除权限
        $check_auth = D('PartyBranchMembers')->where(array('uid'=>$uid,'status'=>3,'branch_id'=>$branch_id))->find();
		if($check_auth){
            D('UserAdvice') -> where(array('id' => $id)) -> save(array('status' => -1));
            ajaxMsg(0, '删除成功');
        }else{
            ajaxMsg(1, '当前账号权限不足');
        }

	}
	public function userApply() {
		
		$this -> set_sidebar_sub('userApply');
		$year = I('year');
		$branch_id = I('branch_id');
		$organiation_id = I('organiation_id');
		$keyword = I('keyword');
		$search['year'] = $year;
		$search['branch_id'] = $branch_id;
		$search['organiation_id'] = $organiation_id;
		$search['keyword'] = $keyword;
		if ($branch_id) {
			$map['User.branch_id'] = $branch_id;
		}
		if ($organiation_id) {
			$map['User.organiation_id'] = $organiation_id;
		}
		if ($keyword) {
			$map['User.realname'] = array("like", "%$keyword%");
			$this -> assign('keyword', $keyword);
		}
		if ($year) {
			$year = date('Y', time());
			$yearTime = strtotime($year . '-1-1');
			$nextYearTime = strtoTime(($year + 1) . '-1-1') - 1;
			$map['UserOfficialApply.create_time'] = array('between', array($yearTime, $nextYearTime));
		}
		$map['UserOfficialApply.status'] = array('gt', -1);

		$page = D('Mob/UserOfficialApplyView') -> findPage($map, 15, 'UserOfficialApply.create_time desc');
		$list = $page["list"]; 
		foreach ($list as $index=>$item ){
			if($item['file_ids']){
				$files = D('UserFile') -> where(array('id' => array('in', $item['file_ids']))) -> select();
				$page['list'][$index]['files'] = $files;
			}else{
				$page['list'][$index]['files'] = array();
			}
		}
		$this -> assign('page', $page);
		$this -> display();
	}

	public function ajaxDecApply() {
		$id = I('id');
		D('UserOfficialApply') -> where(array('id' => $id)) -> save(array('status' => -1));
		ajaxMsg(0, '删除成功');
	}
	

	// 入党申请详情
	public function ajaxUserJoinApplyDetail() {
		$id = I('id');
		$userJoinApply = D('UserJoinApplyView') -> where('UserJoinApply.id=' . $id) -> find();
		if ($userJoinApply) {
			ajaxMsg(0, '通过成功', $userJoinApply);
		}
		ajaxMsg(1, '找不到');
	}

	// 通过入党申请
	public function ajaxUserJoinApplyPass() {
		$id = I('id');
		$userJoinApply = D('UserJoinApply') -> find($id);
		if ($userJoinApply) {
			$userJoinApply['status'] = 1;
			D('UserJoinApply') -> save($userJoinApply);
			ajaxMsg(0, '通过成功');
		} else {
			ajaxMsg(1, '申请不存在');
		}
	}

	// 驳回入党申请
	public function ajaxUserJoinApplyRejected() {
		$id = I('id');
		$userJoinApply = D('UserJoinApply') -> find($id);
		if ($userJoinApply) {
			$userJoinApply['status'] = 2;
			D('UserJoinApply') -> save($userJoinApply);
			ajaxMsg(0, '通过成功');
		} else {
			ajaxMsg(1, '申请存在');
		}
	}

	// 积极分子列表
	public function activistList() {
		$this -> set_sidebar_sub('activistList');
		$page = D('UserView') -> findPage('role_id=' . C('ROLE_ACTIVITE_ID'), 15, 'User.activist_date asc');
		//        exit(to_json_string($page));
		$this -> assign('page', $page);
		$this -> display("activistList");
	}

	// 积极分子详情
	public function ajaxActivistDetail() {
		$uid = I('id');
		$user = D('UserView') -> where('UserJoinApply.id=' . $uid) -> find();
		if ($user) {
			ajaxMsg(0, '通过成功', $user);
		}
		ajaxMsg(1, '找不到');
	}

	// 拟发展积极分子
	public function ajaxActivistDevelop() {
		$uid = I('id');
		$user = D('User') -> find($uid);
		if ($user) {
			$user['role_id'] = C('ROLE_TOBE_ID');
			$user['update_time'] = time();
			$user['develop_date'] = time();
			D('User') -> save($user);
			ajaxMsg(0, '拟发展成功');
		} else {
			ajaxMsg(1, '该用户不存在');
		}
	}

	// 拟发展对象列表
	public function developerList() {
		$this -> set_sidebar_sub('developerList');
		$page = D('UserView') -> findPage('role_id=' . C('ROLE_TOBE_ID'), 15, 'User.develop_date asc');
		//        exit(to_json_string($page));
		$this -> assign('page', $page);
		$this -> display("developerList");
	}

	// 转为预备党员
	public function ajaxDeveloperReady() {
		$uid = I('id');
		$user = D('User') -> find($uid);
		if ($user) {
			$user['role_id'] = C('ROLE_READY_ID');
			$user['update_time'] = time();
			$user['ready_date'] = time();
			D('User') -> save($user);
			ajaxMsg(0, '成功转为预备党员');
		} else {
			ajaxMsg(1, '该用户不存在');
		}
	}

	// 预备党员列表
	public function readyList() {
		$this -> set_sidebar_sub('readyList');
		$page = D('Common/UserView') -> findPage('role_id=' . C('ROLE_READY_ID'), 15, 'User.ready_date asc');
		//        exit(to_json_string($page));
		$this -> assign('page', $page);
		$this -> display("readyList");
	}

	// 转为预备党员
	public function ajaxReadyOfficial() {
		$uid = I('id');
		$user = D('User') -> find($uid);
		if ($user) {
			$user['role_id'] = C('ROLE_PARTY_ID');
			$user['update_time'] = time();
			$user['official_date'] = time();
			D('User') -> save($user);

			ajaxMsg(0, '成功转为预备党员');
		} else {
			ajaxMsg(1, '该用户不存在');
		}
	}

	// 转正申请列表
	public function userOfficialApplyList() {
		$this -> set_sidebar_sub('userOfficialApplyList');
		$page = D('UserOfficialApplyView') -> findPage('', 15, 'UserOfficialApply.create_time desc');
		//        exit(to_json_string($page));
		$this -> assign('page', $page);
		$this -> display("userOfficialApplyList");
	}

	// 通过入党申请
	public function ajaxUserOfficialApplyPass() {
		$id = I('id');
		$userOfficialApply = D('UserOfficialApply') -> find($id);
		if ($userOfficialApply) {
			// 更改申请审核状态
			$userOfficialApply['status'] = 1;
			D('UserOfficialApply') -> save($userOfficialApply);

			// 修改用户身份
			$uid = $userOfficialApply['uid'];
			$user = D('User') -> find($uid);
			if ($user) {
				$user['role_id'] = C('ROLE_PARTY_ID');
				$user['update_time'] = time();
				$user['official_date'] = time();
				D('User') -> save($user);

				//生成缴费记录
				//                $season = ceil((date('n'))/3);//获取当前季度
				//                $year = ceil(date('Y'));//获取当前年份
				//                $pfee = D('PartyFee')->where(array("season"=>$season,"year"=>$year))->find();
				//                if($pfee){
				//                    $fee = $user['wage']*$pfee['coefficient'];
				//                    //每个党员生成一条数据
				//                    $order = make_order($user['uid'], $fee, $year.'年第'.$season.'季度党费缴纳', '党费缴纳', $pfee['id']);
				//                    D('PartyFeeUser')->add(array(
				//                        'fee_id'=> $pfee['id'],
				//                        'fee'=>$fee,
				//                        'uid'=>$user['uid'],
				//                        'create_time'=>time(),
				//                        'wage'=>$user['wage'],
				//                        'order_id'=>$order['id']));
				//                }

				ajaxMsg(0, '成功转为预备党员');
			}
			ajaxMsg(0, '通过成功');
		} else {
			ajaxMsg(1, '申请不存在');
		}
	}

	// 驳回入党申请
	public function ajaxUserOfficialApplyRejected() {
		$id = I('id');
		$userOfficialApply = D('UserOfficialApply') -> find($id);
		if ($userOfficialApply) {
			$userOfficialApply['status'] = 2;
			D('UserOfficialApply') -> save($userOfficialApply);
			ajaxMsg(0, '申请已驳回');
		} else {
			ajaxMsg(1, '申请存在');
		}
	}

	// 发起转正征集
	public function ajaxOfficialCollect() {
		$uid = I('id');
		$user = D('User') -> find($uid);

		if ($user['official_collect_status'] == 0) {
			$user['official_collect_status'] = 1;
			$user['update_time'] = time();
			D('User') -> save($user);

			$userOfficialCollect['uid'] = $uid;
			$userOfficialCollect['status'] = 1;
			$userOfficialCollect['create_time'] = time();
			$userOfficialCollect['end_time'] = $userOfficialCollect['create_time'] + 2592000;
			// 30天后结束
			$userOfficialCollect['branch_id'] = $user['branch_id'];
			$collectId = D('UserOfficialCollect') -> add($userOfficialCollect);

			// 生成一条待办事项，提醒本支部人去发表转正意见
			$todo['title'] = $user['realname'] . "转正意见征集";
			$todo['content'] = $user['realname'] . "成为预备党员以来表现良好，现欲予以转正，请大家发表各自的意见。";
			$todo['request_finish_date'] = $userOfficialCollect['end_time'];
			$todo['receiver_roles'] = "1";
			$todo['receiver_branchs'] = "" . $user['branch_id'];
			$todo['type'] = "OFFICIAL_COLLECT";
			$todo['source_id'] = $collectId;
			$todo['url'] = "/Home/Work/officialCollectDetail?id=" . $collectId;
			$todo = set_todo_save_auth($todo);
			addTodo($todo);

			ajaxMsg(0, "成功发起转正意见征集");
		} else {
			ajaxMsg(1, "已经在征集意见了，请不要重复征集。");
		}
	}

	//
	public function officialCollectList() {
		$this -> set_sidebar_sub('officialCollectList');
		$page = D('UserOfficialCollectView') -> findPage('', 15, 'UserOfficialCollect.create_time desc');
		//        exit(to_json_string($page));
		$this -> assign('page', $page);
		$this -> display();
	}

	public function ajaxDisagreeOfficial() {
		$id = I('id');
		$userOfficialCollect = D('UserOfficialCollect') -> find($id);
		if ($userOfficialCollect) {
			$userOfficialCollect['status'] = 0;
			$userOfficialCollect['result'] = -1;
			$user = D('User') -> find($userOfficialCollect['uid']);
			if ($user) {
				$user['official_collect_status'] = 0;
				$user['update_time'] = time();
				D('User') -> save($user);
			}
			D('UserOfficialCollect') -> save($userOfficialCollect);
			ajaxMsg(0, "成功");
		}
		ajaxMsg(1, "失败");
	}

	public function ajaxAgreeOfficial() {
		$id = I('id');
		$userOfficialCollect = D('UserOfficialCollect') -> find($id);
		if ($userOfficialCollect) {
			$userOfficialCollect['result'] = 1;
			D('UserOfficialCollect') -> save($userOfficialCollect);
			ajaxMsg(0, "成功");
		}
		ajaxMsg(1, "失败");
	}
	public function userRoleLog()
	{
		# code...
		$this -> set_sidebar_sub('userRoleLog');
		$map = array();
		$keyword = I('keyword');
		if ($keyword) {
			$map['User.realname'] = array("like", "%$keyword%");
			$this -> assign('keyword', $keyword);
		}
	
		$page = D('UserRoleLogView') -> findPage($map, 15, 'UserRoleLog.conversion_time desc');
		//        exit(to_json_string($page));
		
		$temp = $page['list'];
		foreach($temp as $k=>$v){
			$temp[$k]['old_role_name'] = D('UserRole')->where(array('id'=>$v['old_role']))->getField('role_name');
			$temp[$k]['new_role_name'] = D('UserRole')->where(array('id'=>$v['new_role']))->getField('role_name');		
		}
		$page['list'] = $temp;
		$this -> assign('page', $page);
		$this->display();
	}
}
