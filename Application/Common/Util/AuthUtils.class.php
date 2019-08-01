<?php
namespace Common\Util;
class AuthUtils
{
	//市局党组成员或班子 1
	//处级非领导职务 2
	//科级干部 3
	//科员及办事员或其他 4
	//市局党组书记 5
	public static function check_adm_post($user, $adm_code){
		return true;
		$adm_id = $user['adm_id'];
		if(!$adm_id){
			return false;
		}
		if($adm_id == $adm_code){
			return true;
		}
		return false;
	}

	public static function adm_post($user){
		return $user['adm_id'];
	}

		public function test(){

			$user = user();
			$admAuth = AuthUtils::check_adm_post($user, 1);//是否有 市局党组成员或班子 权限
			if($admAuth){
				//如果有该权限
				//do something...
			}


		}
}
