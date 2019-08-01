<?php
namespace Admin\Util;
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/12/5
 * Time: 21:04
 */
class AdminUtil
{

	static function login($uid)
	{

		$user = D('UserView')->where("User.uid=$uid")->find();

		$ip = get_ip_address();

		$u = array();
		$u['last_login_ip'] = $ip;
		$u['last_login_time'] = time();
		$user['login_count'] = $user['login_count'] + 1;
		$u['login_count'] = $user['login_count'];
		D('User')->where("uid=$uid")->save($u);

		$group = D('AdminAuthGroupAccess')->where(array('uid' => $uid))->find();

		$auth = 0;
		if ($group['group_id'] == 3) {
			$auth = 3;
			$branchA = D('PartyBranchAdm')->where(array('uid' => $uid))->find();
			$branch = D('PartyBranch')->where(array('id' => $branchA['branch_id']))->find();
			session("adm_auth_branch", $branch);
		} elseif ($group['group_id'] == 2) {
			$auth = 2;
		} elseif ($group['group_id'] == 1) {
			$auth = 1;
		} else {
			$auth = 1;
		}


		if ($auth > 0) {
			$sessionNames = C('SESSION_CONFIG');
			$adminSsessionName = $sessionNames['ADMIN_SESSION'];
			session($adminSsessionName, $user);

		}
		session("adm_auth", $auth);
	}
	//$sessionNames = C('SESSION_CONFIG');
    // $adminSsessionName = $sessionNames['ADMIN_SESSION'];
    // $adminAuthSsessionName = $sessionNames['ADMIN_AUTH_SESSION'];
    // session($adminSsessionName, $user);
	/**
	 * @return mixed 3支部用户| 2市局
	 */
	static function auth()
	{
		return session("adm_auth");
	}

	/**
	 * 登录后所管理的支部
	 */
	static function auth_branch()
	{
		return session("adm_auth_branch");
	}

	/***
	 * 获取登录后的管理员
	 */
	static function admin()
	{
		$sessionNames = C('SESSION_CONFIG');
		$adminSsessionName = $sessionNames['ADMIN_SESSION'];
		return session("$adminSsessionName");
	}


}
