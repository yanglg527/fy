<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */

namespace Mob\Controller;

use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;

class ActivityController extends BaseAuthController {

	function _initialize() {
		parent::_initialize();
	}

	public function index() {
		$this -> check_wx_redirect('mob_party_member_index');
		//判断是否重定向登录
		$where = array("Event.status" => 1);
		$list = D("EventView") -> where($where) -> order("Event.create_time desc") -> select();
		$this -> assign('list', $list);
		$this -> display();
	}

	public function detail() {
		$id = I('id');
		$detail = D('Event') ->where(array('id'=>$id))-> find();
		$this -> assign('attend', D('EventAttend') -> where(array('uid' => uid(),'event_id'=>$id, 'status' => 1)) -> find());
		$this -> assign('detail', $detail);

		if(!$detail){
			$this->redirect(__ROOT__."/error3.html");
		}
		$this->assign("head_title", "活动详情");
		$this -> display();
	}

	public function ajaxAttend() {
		$this -> check_wx_redirect();
		//判断是否重定向登录
		//        ajaxMsg(1, to_json_string($_POST));
		$id = I('id');
		//		$user = user();

		//      if(!$mobile) {
		//          ajaxMsg(1, "请先填写手机号码");
		//      }

		$detail = D('Event')->where(array('id'=>$id))-> find();
		if ($detail['status'] > 0) {
			$attendStartTime = $detail['entry_start_time'];
			$attendEndTime = $detail['entry_end_time'];
			$timeNow = time();
			if ($attendStartTime > $timeNow) {
				ajaxMsg(1, "报名时间还没开始");
			}
			if ($attendEndTime + 24 * 3600 < $timeNow) {
				ajaxMsg(1, "报名已结束");
			}
			if ($detail['people_limit'] > 0) {
				if ($detail['attend_count'] >= $detail['people_limit']) {
					ajaxMsg(1, "抱歉，报名人数已满");
				}
			}

			$user = user();
			$mobile = $user['mobile'];
			//
			//          $roles = D('EventRole')->where(array('event_id'=>$id))->select();
			//          $count = 0;
			//          foreach($roles as $role){
			//              if($role['role_id'] == $user['role_id']){
			//                  $count ++;
			//              }
			//          }
			//          if($count == 0){
			//              ajaxMsg(1, "你没有该活动报名权限");
			//          }

			$uid = $user['uid'];
			$attend = D("EventAttend") -> where("uid=$uid and event_id=$id") -> find();
			if ($attend) {
				if ($attend['status'] == -2) {//被禁止报名了
					ajaxMsg(1, "你没有该活动报名权限");
				} elseif ($attend['status'] == 1) {//已经报名了
					ajaxMsg(1, "你已经报名了");
				} elseif ($attend['status'] == -1) {
					D('EventAttend') -> where("uid=$uid and event_id=$id") -> save(array('name' => $user['realname'], 'remark' => I('remark'), 'mobile' => $mobile, 'birthday' => I('birthday'), 'position' => I('position'), 'work_unit' => I('work_unit'), 'gender' => I('gender'), 'status' => 1, 'attend_time' => $timeNow));

				}
			} else {//新加报名
				D("EventAttend") -> add(array('uid' => $user['uid'], 'event_id' => $id, 'name' => $user['realname'], 'remark' => I('remark'), 'mobile' => $mobile, 'birthday' => I('birthday'), 'position' => I('position'), 'work_unit' => I('work_unit'), 'gender' => I('gender'), 'status' => 1, 'attend_time' => $timeNow));
			}
			$detail['attend_count'] = $detail['attend_count'] + 1;
			D('Event') -> save($detail);

			ajaxMsg(0, "success");
		} else {
			ajaxMsg(1, "抱歉，找不到该活动");
		}

	}

	public function ajaxCancelAttend() {
		$this -> check_wx_redirect();
		//判断是否重定向登录
		$id = I('id');
		$detail = D('Event')->where(array('id'=>$id))-> find();
		if ($detail['status'] > 0) {
			$attendEndTime = $detail['entry_end_time'];
			$timeNow = time();
			if ($attendEndTime + 3600 * 24 < $timeNow) {
				ajaxMsg(1, "报名已截止");
			}
			$user = user();
			$uid = $user['uid'];
			$attend = D("EventAttend") -> where("uid=$uid and event_id=$id") -> find();
			if ($attend) {
				if ($attend['status'] == -2) {//被禁止报名了
					ajaxMsg(0, "你已取消报名了");
				} elseif ($attend['status'] == 1) {//已经报名了
					D('EventAttend') -> where("uid=$uid and event_id=$id") -> save(array('status' => -1, 'sign_status' => 0, 'sign_out_status' => 0, 'sign_time' => null, 'sign_out_time' => null));
					$detail['attend_count'] = $detail['attend_count'] - 1;
					D('Event') -> save($detail);
				}
				ajaxMsg(0, "你已取消报名了");
			} else {//新加报名
				ajaxMsg(0, "你已取消报名了");
			}

		} else {
			ajaxMsg(1, "该活动已经取消了");
		}
	}

	public function attends() {
		$id = I('id');
		$detail = D('Event') ->where(array('id'=>$id))-> find();
		$this -> assign('detail', $detail);
		$this -> assign('list', D('EventAttendView') -> where(array('event_id' => $id, 'EventAttend.status' => 1)) -> order("User.branch_id asc") -> select());
		$this -> display();
	}

	public function attends_management() {
		$id = I('id');
		$detail = D('Event') ->where(array('id'=>$id))-> find();
		$this -> assign('detail', $detail);
		$this -> assign('list', D('EventAttendView') -> where(array('event_id' => $id, 'EventAttend.status' => 1)) -> order("User.branch_id asc") -> select());
		$this -> display();
	}

	public function ajaxAttendsSignIn() {
		$id = I('id');

		$detail = D('Event') ->where(array('id'=>$id))-> find();
		if ($detail['uid'] == uid()) {
			$ids = I('ids');
			D('EventAttend') -> where(array('event_id' => $id, 'sign_status' => 1)) -> save(array('sign_status' => 0));
			if ($ids) {
				D('EventAttend') -> where(array('event_id' => $id, 'uid' => array('in', $ids))) -> save(array('sign_status' => 1));
			}
			ajaxMsg(0, "点到成功");
		}
		ajaxMsg(1, "没有操作权限");
	}

}
