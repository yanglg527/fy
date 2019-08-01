<?php
namespace Admin\Controller;

use Think\Controller;
use Admin\Util\AdminUtil;

/**
 * 权限基本控制器
 * Class BaseController
 * @package Common\Controller
 */
class BaseAdminController extends Controller {

	//过滤器
	function _initialize() {
// var_dump(2222);
// exit ;
		if (admin()) {
			if (!authCheck(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME, admin_uid())) {
				if (IS_AJAX) {
					ajaxMsg(1, "你没有操作权限");
				} else {

					$this -> error('你没有该操作权限', __ROOT__ . "/Admin/Login/index");
					exit ;
				}
			}
			$admin = admin();
			$this -> assign('admin', $admin);
			
			$adminGroup = D('AdminAuthGroupAccess') -> where(array('uid' => $admin['uid'])) -> find();
			if ($adminGroup['group_id'] == 1) {
				$this -> assign('auth', 1);
			}else{
				$this -> assign('auth', AdminUtil::auth());
			}
			
		} else {
			if (IS_AJAX) {
				ajaxMsg(1, "登录已失效，请重新登录");
			}
			$url = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
			$this -> error('请先登录', __ROOT__ . "/Admin/Login?url=$url");
		}
	}

	function set_sidebar_module($module) {
		$this -> assign('sidebar_module', $module);
	}

	function set_sidebar_sub($sub) {
		$this -> assign('sidebar_sub', $sub);
	}

	public function setTitle($title) {
		$this -> assign('webTitle', $title);
	}

}
