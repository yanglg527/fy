<?php
namespace Weixin\Util;
/**
 *	微信公众平台企业号PHP-SDK, 官方API类库
 *  @author  binsee <binsee@163.com>
 *  @link https://github.com/binsee/wechat-php-sdk
 *  @version 1.0
 *  usage:
 *   $options = array(
 *			'token'=>'tokenaccesskey', //填写应用接口的Token
 *			'encodingaeskey'=>'encodingaeskey', //填写加密用的EncodingAESKey
 *			'appid'=>'wxdk1234567890', //填写高级调用功能的app id
 *			'appsecret'=>'xxxxxxxxxxxxxxxxxxx', //填写高级调用功能的密钥
 *			'agentid'=>'1', //应用的id
 *			'debug'=>false, //调试开关
 *			'logcallback'=>'logg', //调试输出方法，需要有一个string类型的参数
 *		);
 *
 */
class QYConfig
{
	public static $tableName='User';//用户信息保存表
	public static $tableFields = array(
		'id' => 'uid',//必须要有   用户id字段
		'userid' => 'qyuserid',//必须要有   用户唯一标识
		'status' => 'status',//必须有       -1 删除  0禁用  1正常
		'sync_version' => 'qy_sync_version',//必须有 信息同步更新版本

		//以下信息，不需要填写则为空
		'mobile' => 'mobile',//用户信息表中 保存信息的字段，企业号获得信息后自动放入
		'email' => 'email',//邮箱
		'weixinid' => 'weixinid',//微信 id
		'sync_time' => 'qysynctime',//更新时间
		'department' => 'qy_department_ids',//部门 id
		'position' => 'position',//职位
		'avatar' => 'headimgurl',//头像
		'name' => 'realname',//真实姓名
		'gender' => 'gender',// 0女生 1男生 空为中性
	);

	function _initialize(){

	}

	static function check_userid($qyuserid){
		$config = C('WEIXINQY_CONFIG');
		$info = QYConfig::get_qy_user_info($config['CORP_ID'],   $config['SECRET'],$qyuserid);
		if($info){
			return $info;
		}
		return false;
	}

	//获取企业成员信息
	static function get_qy_user_info($corpID, $secret, $userId)
	{


		$accessToken = QYConfig::get_qy_access_token($corpID, $secret);
		$url = "https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=$accessToken&userid=$userId";
		$result = QYConfig::wx_http_get($url);
		$info = to_json_obj($result);
//    echo $result;
//    echo "<br/>";
		if ($info['errcode'] == 0) {
			return $info;
		} else {
			return false;
		}

	}

	/**
	 * GET 请求
	 * @param string $url
	 */
	static function wx_http_get($url)
	{
		$oCurl = curl_init();
		if (stripos($url, "https://") !== FALSE) {
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_TIMEOUT,40);   //只需要设置一个秒的数量就可以
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if (intval($aStatus["http_code"]) == 200) {
			return $sContent;
		} else {
			return false;
		}
	}
	/**
	 * @param $corpID
	 * @param $secret
	 * {
	 * "access_token": "accesstoken000001",
	 * "expires_in": 7200
	 * "updateTime":time()
	 * }
	 */
static function get_qy_access_token($corpID, $secret)
	{
		$token = D('WeixinConfig')->where("name='qy_access_token'")->find();
		if ($token) {
			$jsonToken = to_json_obj($token['value']);
			//access_token未失效
			if ($jsonToken['updateTime'] && (time() - $jsonToken['updateTime'] < ($jsonToken['expires_in'] - 10))) {
				return $jsonToken['access_token'];
			} else {//获取 accessToken
				return QYConfig::_reget_qy_access_token($corpID, $secret);
			}
		} else {
			return false;
		}
	}

	static function _reget_qy_access_token($corpID, $secret)
	{
		$url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$corpID&corpsecret=$secret";
		$result = QYConfig::wx_http_get($url);
		$jsonToken = to_json_obj($result);
		if ($jsonToken['access_token']) {
			$jsonToken['updateTime'] = time();
			$token['value'] = to_json_string($jsonToken);
			D('WeixinConfig')->where("name='qy_access_token'")->save($token);
			return $jsonToken['access_token'];
		} else {
			return false;
		}
	}





}
