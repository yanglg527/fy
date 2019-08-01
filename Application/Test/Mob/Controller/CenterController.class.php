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

class CenterController extends BaseAuthController {

	function _initialize() {
		parent::_initialize();
	}

	//信箱列表
	public function index() {
		$this -> check_wx_redirect('mob_party_member_index');
		//判断是否重定向登录
		$uid = I('get.uid');
		if ($uid) {
			$user = D('UserView') -> where(array('uid' => $uid)) -> find();
			$this -> assign('uid', $uid);
		} else {
			$user = user();
		}
		$this -> assign('user', $user);
		$this -> assign('taizhang', D('TaizhangView') -> where(array('Taizhang.uid' => $user['uid'], 'Taizhang.status' => array('gt', -1))) -> order('Taizhang.create_time desc') -> select());
		$this -> display();
	}

	public function ajaxDeleteTaizhang() {
		$id = I('id');
		if ($id) {
			$detail = D('Taizhang') -> where(array('id' => $id)) -> find();
			if ($detail['uid'] == uid()) {
				if($detail['status'] > -1 && $detail['add_uid'] != null){
					update_user_score($detail['add_uid'], -5, 1,'删除台账');
				}
				D('Taizhang') -> where(array('id' => $id)) -> save(array('status' => -1));
				ajaxMsg(0, 'success');
			}
			ajaxMsg(1, "没有操作权限");
		}
		ajaxMsg(1, "找不到该台帐");
	}

	public function edu() {
		$this -> assign('edus', D('UserEducation') -> where(array('uid' => uid())) -> order('finish_time asc') -> select());
		$this -> display();
	}

	public function edu_edit() {
		$id = I('id');
		if ($id) {
			$detail = D('UserEducation') -> find($id);
			$this -> assign('detail', $detail);
		}
		$this -> display();
	}

	public function reward() {
		$this -> assign('rewards', D('UserRewardSituation') -> where(array('uid' => uid())) -> order('reward_date asc') -> select());
		$this -> display();
	}

	public function reward_edit() {
		$id = I('id');
		if ($id) {
			$detail = D('UserRewardSituation') -> find($id);
			$this -> assign('detail', $detail);
		}
		$this -> display();
	}

	public function edit() {
		$uid = I('get.uid');
		if ($uid) {
			$user = D('UserView') -> where(array('uid' => $uid, 'status' => 1)) -> find();
			if ($user) {
				$this -> assign('uid', $uid);
				$this -> assign('user', $user);
			}

		}
		$this -> display();
	}

	public function basic() {
		$this -> display();
	}

	public function ajax_save_reward() {
		$id = I('id');
		$name = I('name', null);
		$content = I('content', null);
		$level_name = I('level_name', null);
		$reward_date = I('reward_date', null);

		$reward_date = $reward_date ? strtotime($reward_date) : null;

		$array = array('reward_date' => $reward_date, 'name' => $name, 'content' => $content, 'level_name' => $level_name);
		$user = user();
		if ($id) {
			D('UserRewardSituation') -> where(array('uid' => $user['uid'], 'id' => $id)) -> save($array);
		} else {
			$user = user();
			$array['uid'] = $user['uid'];
			$array['create_time'] = time();
			D('UserRewardSituation') -> add($array);
		}

		ajaxMsg(0, "success");
	}

	public function ajax_save_edu() {
		$id = I('id');
		$school_name = I('school_name', null);
		$type_name = I('type_name', null);
		$finish_time = I('finish_time', null);

		$finish_time = $finish_time ? strtotime($finish_time) : null;

		$array = array('finish_time' => $finish_time, 'school_name' => $school_name, 'type_name' => $type_name);
		$user = user();
		if ($id) {
			D('UserEducation') -> where(array('uid' => $user['uid'], 'id' => $id)) -> save($array);
		} else {
			$user = user();
			$array['uid'] = $user['uid'];
			D('UserEducation') -> add($array);
		}

		ajaxMsg(0, "success");
	}

	public function basic_branch() {
		$branchs = D('PartyBranchSelectView') -> order('sort desc') -> select();
		$this -> assign('branchs', $branchs);
		//        $this->assign('branchs',D('PartyBranch')->order('name asc')->select());
		$adms = D('AdmPostSelectRelation') -> relation(true) -> field(array('id' => 'value', 'name' => 'text')) -> where(array('pid' => array('exp', 'is null'))) -> order('id asc') -> select();

		foreach ($adms as $index => $adm) {
			$adms[$index]['children'] = D('AdmPostSelectRelation') -> relation(true) -> field(array('id' => 'value', 'name' => 'text')) -> where(array('pid' => $adm['value'])) -> order('id asc') -> select();

		}
		array_unshift($adms, array('value' => '0', 'text' => '无', 'children' => array( array('value'=>'0',
			'text'=>'无'))));

		$this -> assign('adms', $adms);
		$this -> assign('units', D('WorkUnitSelectView') -> order('id asc') -> select());
		$this -> assign('roles', D('RoleSelectView') -> order('id asc') -> select());
		$this -> display();
	}

	public function head() {
		$this -> display();
	}

	public function ajax_upload_head() {
		$base64 = I('file');
		if (!$base64) {// 上传错误提示错误信息
			ajaxMsg(1, "请上传适当大小的图片");
		} else {
			$path = $this -> _base64_upload_avatar($base64);
			//            ajaxMsg(1, $base64);
			if ($path) {
				$user = user();
				$user['headimgurl'] = $path;
				D('User') -> where(array("uid" => uid())) -> save(array("headimgurl" => $user['headimgurl']));
				resession_user($user);
				ajaxMsg(0, 'success');
			} else {
				ajaxMsg(1, '抱歉，文件保存失败');
			}
		}
	}

	public function ajax_save_zzmm() {
		$work_unit_id = I('work_unit_id');
		$branch_id = I('branch_id', null);
		$adm_id = I('adm_id', null);
		$role_id = I('role_id', 5);
		$work_unit_entry_time = I('work_unit_entry_time', null);
		$branch_entry_time = I('branch_entry_time', null);
		$role_entry_time = I('role_entry_time', null);

		$work_unit_entry_time = $work_unit_entry_time ? strtotime($work_unit_entry_time) : null;
		$branch_entry_time = $branch_entry_time ? strtotime($branch_entry_time) : null;
		$role_entry_time = $role_entry_time ? strtotime($role_entry_time) : null;
$adm_id == 0?null:$adm_id;
		$array = array('work_unit_id' => $work_unit_id, 'branch_id' => $branch_id, 'adm_id' => $adm_id, 'role_id' => $role_id, 'is_leader' => 0, 'work_unit_entry_time' => $work_unit_entry_time, 'branch_entry_time' => $branch_entry_time, 'role_entry_time' => $role_entry_time);
		if ($adm_id > 3) {
			$array['is_leader'] = 1;
		}else{
			$array['is_leader'] = 0;
		}
		$user = user();
		if ($user['post_id'] == 1) {
			$array['is_leader'] = 1;
		}
		D('User') -> where(array('uid' => $user['uid'])) -> save($array);
		ajaxMsg(0, "success");
	}

	public function ajax_save_other() {
		$reward_situation = I('reward_situation');
		$education = I('education');
		$array = array('reward_situation' => $reward_situation, 'education' => $education);
		$user = user();
		D('User') -> where(array('uid' => $user['uid'])) -> save($array);
		ajaxMsg(0, "success");
	}

	public function ajax_save() {
		$realname = I('realname');
		$gender = I('gender');
		$sign = I('sign');
		$mobile = I('mobile');
		$activist_date = I('activist_date');
		// $birthday = I('birthday');
		//        ,'mobile'=>$mobile,'realname'=>$realname,
		$array = array('gender' => $gender,  'sign' => $sign);
		if ($activist_date) {
			$array['activist_date'] = strtotime($activist_date);
		}
		$user = user();

		D('User') -> where(array('uid' => $user['uid'])) -> save($array);
		ajaxMsg(0, "success");
	}

	private function _base64_upload_avatar($base64) {
		$base64_image = $base64;
		//str_replace(' ', '+', $base64);
		//post的数据里面，加号会被替换为空格，需要重新替换回来，如果不是post的数据，则注释掉这一行
		if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)) {
			//匹配成功
			if ($result[2] == 'jpeg') {
				$image_name = uniqid() . '.jpg';
			} else {
				$image_name = uniqid() . '.' . $result[2];
			}
			$path = '/Uploads/Avatar/' . $image_name;
			mkDirs("./Uploads/Avatar/");
			//服务器文件存储路径
			if (file_put_contents('.' . $path, base64_decode(str_replace($result[1], '', $base64_image)))) {

				//文件上传记录
				//                $file = array(
				//                    'savename' => $image_name,
				//                    'ext' => "jpg/".$result[2],
				//                    'type' => 'file',
				//                    'savepath' => $path,
				//                    'uid' => uid(),
				//                    'size' => 0,
				//                    'create_time' => time()
				//                );
				//                D('Filelist')->add($file);

				return $path;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

}
