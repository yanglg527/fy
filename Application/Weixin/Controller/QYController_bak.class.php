<?php

/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Weixin\Controller;

use Common\Controller\BaseController;
use Weixin\Util\QYConfig;
// use Weixin\Util\QYWechat;
use Admin\Util\AdminUtil;
use Weixin\Util\qywechat;

set_time_limit(240);
class QYController extends BaseController
{




//请在Weinxin/Conf/config.php 设置相应信息
    private $corp_id;
    private $domain;
    private $secret;
    private $errorUrl;//不存在该用户，跳转页面，会再参数中?url=urecode(原来的地址)
    private $tableName;//保存信息表
    private $tableFields;//保存信息表
    function _initialize()
    {
        $config = C('WEIXINQY_CONFIG');
        $this->corp_id = $config['CORP_ID'];
        $this->domain = $config['DOMAIN'];
        $this->secret = $config['SECRET'];
        $this->tableName = QYConfig::$tableName;
        $this->tableFields = QYConfig::$tableFields;
        $this->errorUrl = $config['ERROR_URL'];
    }

    /**
     * 网页授权登录  企业号填写信任域名
     * 提供参数 state : 指定跳转页面
     * /Weixin/QY/webAuth?key=index&state=1
     * key:页面定义的值  在weixin_redirect_url 保存对应跳转页面
     * state:用于页面传值(英文|数字)  在 跳转的action获取字段 state
     *
     */
    public function webAuth()
    {
        $key = I('key', 'index');//url 的 key
        $state = I('state', '0');//传值
        $redirect_uri = urlencode("http://$this->domain/Weixin/QY/webRedirect?key=$key");//重定向地址
        $weixinAuth = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->corp_id&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_base&state=$state#wechat_redirect";
        redirect($weixinAuth);//指向微信跳转
    }

    //get {"key":"", "code":"dfadfadf", "state":"dfdf"}
    public function webRedirect()
    {
        $key = I('key', 'index');//url 的 key
        $state = I('state', '');//传值
        $code = I('code');
        if ($code) {
            $uob = D('Weixin_redirect_url')->where(array('key' => $key))->find();
            $url = $uob['url'];
            $re_url = "http://$this->domain/$url?state=$state";

            //请求用户信息
            $user = check_and_register_user_by_qy($this->corp_id, $this->secret, $code, $this->tableName, $this->tableFields);
            if (!$user || $user['status'] != 1) {
                redirect(__ROOT__ . $this->errorUrl . "?url=" . urlencode($re_url));
                exit;
            }

            //提供 登录方法
            login($user['uid']);

            redirect($re_url);
        }
    }

    /**
     * 网页扫一扫授权跳转
     */
    public function webAuthority()
    {

        $config = C('WEIXINQY_CONFIG');
        $corp_id = $config['CORP_ID'];
        $domain = $config['DOMAIN'];
        $secret = $config['SECRET'];
        $AgentId = $config['AGENTID'];
        $redirect_uri = "http://$domain/Weixin/QY/webAuthorityLogin";

      //$url = "https://open.weixin.qq.com/connect/qrconnect?appid={$config['CORP_ID']}&redirect_uri=" . urlencode($redirect_uri). "&response_type=code&scope=snsapi_login&state=#wechat_redirect";
     //$url = "https://qy.weixin.qq.com/cgi-bin/loginpage?corp_id={$config['CORP_ID']}&redirect_uri=" . urlencode($redirect_uri). "&state=&usertype=member"; //state=&
        $url = "https://open.work.weixin.qq.com/wwopen/sso/qrConnect?appid={$config['CORP_ID']}&agentid={$AgentId}&redirect_uri=" . urlencode($redirect_uri) . "&state=STATE";


        header('location: ' . $url);

    }

    /**
     * 网页授权登陆
     */
    //  public function webAuthorityLogin()
    //  {

    //      $code = I('get.auth_code');
    //      file_put_contents('./weblogin.txt', $code);
    //      if ($code) {

    //          $user = check_web_user_by_qy($this->corp_id, $this->secret, $code, $this->tableName, $this->tableFields);
	 		// if($user){
	 		// 	AdminUtil::login($user['uid']);
	 		// 	if(AdminUtil::auth() < 1){
	 		// 		$this->error('抱歉，您没有管理权限，返回登陆', __ROOT__ . "/Admin/Login/index");
    //          		exit();
	 		// 	}
	   //           $url = __ROOT__.'/Admin/Index/index';
	   //           header('location: ' . $url);
	 		// 	exit();
	 		// }else{
	 		// 	$this->error('抱歉，您没有管理权限，返回登陆', __ROOT__ . "/Admin/Login/index");
	 		// 	exit();
	 		// }

    //      }
    //  }

    /**
     * 网页授权登陆 企业微信扫码
     */
    public function webAuthorityLogin()
    {

       $code = I('code');
       file_put_contents('./weblogin.txt', $code);
        if ($code) {

           $access_token = _reget_qy_access_token($this->corp_id, $this->secret);
           var_dump($access_token);
            if ($access_token) {
               $url = "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=$access_token&code=$code";
               $result = wx_http_get($url);
               $info = to_json_obj($result);
               var_dump($info);
                if ($info['UserId']) {
                   $user = check_web_user_by_qy($info['UserId'], $this->tableName, $this->tableFields);
                   if ($user) {
                       AdminUtil::login($user['uid']);
                       if (AdminUtil::auth() < 1) {
                           $this->error('抱歉，您没有管理权限，返回登陆', __ROOT__ . "/Admin/Login/index");
                           exit();
                        }
                        $url = __ROOT__ . '/Admin/Index/index';
                       header('location: ' . $url);
                       exit();
                   } else {
                       echo '没找到用户';
                       $this->error('抱歉，您没有管理权限，返回登陆', __ROOT__ . "/Admin/Login/index");
                       exit();
                   }
               }
           }


       }
    }




    public function ajaxTestOpenId()
    {
        $openid = cover_userid_to_openid($this->corp_id, $this->secret, 'yzw');
        exit(to_json_string($openid));
    }


    public function ajaxAsyncUser()
    {
        set_time_limit(240);
        update_all_by_qy($this->corp_id, $this->secret, $this->tableName, $this->tableFields);
        ajaxMsg(0, "success");
    }

    /**
     * 主动推送
     */
    public function sendMessage($data)
    {
        $corpID = $this->corp_id;
        $appselect = $this->secret;
       // return $data;
        $accessToken = get_qy_access_token($corpID, $appselect);
        $options = array(
            'access_token' => $accessToken,
            'appid' => $corpID,
            'appsecret' => $appselect,
        );
        $_qy = new QYWechat($options);
        return $_qy->sendMessage($data);
    }


    public function index()
    {
        $this->display();
    }

    /**
     * 更新用户QYUSERID
     *这个方法请看清楚再使用		
     */
    public function updateQyUserId(){
        // 获取部门列表
        $departments = get_qy_departments($this->corp_id, $this->secret);
        // 按部门获取用户列表
        if ($departments) {
            $_user = M('User');
            foreach ($departments['department'] as $department) {
                $qy_users = get_qy_users_by_departmentid($this->corp_id, $this->secret, $department['id']);
                foreach ($qy_users['userlist'] as $qy_user) {
                    $user = $_user->field('uid,qyuserid,realname')->where(array(
                        'mobile' => $qy_user['mobile'],
                        'realname' => $qy_user['name'],
                    ))->select();

                    if (!empty($user)) {
                        // 更新用户 userId
                        $bool = $_user->where(array(
                            'uid' => $user[0]['uid']
                        ))->save(array('qyuserid' => $qy_user['userid']));

                        if (false !== $bool) {
                            $qyuserid['userid'] = $qy_user['userid'];
                            $qyuserid['mobile'] = $qy_user['mobile'];
                            $qyuserid['uid'] = $user[0]['uid'];
                            $qyuserid['qyuserid'] = $user[0]['qyuserid'];
                            $qyuserid['realname'] = $user[0]['realname'];
                            $logs[] = $qyuserid;
                        }
                    }
                    unset($qyuserid);
                }
            }
        }
	var_dump($qy_users);
	$arr = $qy_users['userlist'];
	foreach($arr as $k=>$v){
	$deluser[] = $v['userid'];
}        
	$arr_str = implode(",", $deluser);
	$lastsql = substr($arr_str,0,strlen($arr_str)-1);
	echo $lastsql;
//		M('user')->where(array('qyuserid'=>array('in',$deluser)))->delete();
$cellName = array(
            array('uid', 'UID', 0, 6, 'left'),
            array('realname', '姓名', 0, 12, 'left'),
            array('mobile', '手机号码', 0, 15, 'left'),
            array('qyuserid', '更新前', 0, 18, 'left'),
            array('userid', '更新后', 0, 18, 'LEFT'),
        );
        //$this->exportExcel('Update Details', '更新详情', $cellName, $logs);
        //ajaxMsg(0, 'success', $logs);
    }

    // 导出Excel
    protected function exportExcel($title, $cellTitle, $cellName, $data)
    {
        //引入核心文件
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();

        //定义配置
        $topNumber = 2;//表头有几行占用
        $xlsTitle = iconv('utf-8', 'gb2312', $title);//文件名称
        // $fileName = $title.date('_YmdHis');//文件名称
        $fileName = $xlsTitle.date('_YmdHis');//文件名称
        $cellKey = array(
                'A','B','C','D','E','F','G','H','I','J','K','L','M',
                'N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM',
                'AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'
        );

        //处理表头标题
        $objPHPExcel->getActiveSheet()->mergeCells('A1:'.$cellKey[count($cellName)-1].'1');//合并单元格（如果要拆分单元格是需要先合并再拆分的，否则程序会报错）
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $cellTitle);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        //处理表头
        foreach ($cellName as $k=>$v)
        {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellKey[$k].$topNumber, $v[1]);//设置表头数据
            $objPHPExcel->getActiveSheet()->freezePane($cellKey[$k].($topNumber+1));//冻结窗口
            $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k].$topNumber)->getFont()->setBold(true);//设置是否加粗
            $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k].$topNumber)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
            if($v[3] > 0)//大于0表示需要设置宽度
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($cellKey[$k])->setWidth($v[3]);//设置列宽度
            }
        }

        //处理数据
        foreach ($data as $k=>$v)
        {
            foreach ($cellName as $k1=>$v1)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($cellKey[$k1].($k+1+$topNumber), $v[$v1[0]]);
                if($v['end'] > 0)
                {
                    if($v1[2] == 1)//这里表示合并单元格
                    {
                        $objPHPExcel->getActiveSheet()->mergeCells($cellKey[$k1].$v['start'].':'.$cellKey[$k1].$v['end']);
                        $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k1].$v['start'])->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    }
                }
                if($v1[4] != "" && in_array($v1[4], array("LEFT","CENTER","RIGHT")))
                {
                    $v1[4] = eval('return PHPExcel_Style_Alignment::HORIZONTAL_'.$v1[4].';');
                    //这里也可以直接传常量定义的值，即left,center,right；小写的strtolower
                    $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k1].($k+1+$topNumber))->getAlignment()->setHorizontal($v1[4]);
                }
            }
        }
        //导出execl
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save("$fileName.xls");
        return true;
    }
}
