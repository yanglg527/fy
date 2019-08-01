<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Home\Controller;
use Common\Controller\BaseController;
use Org\Util\Date;

class IndexController extends BaseController {

    function _initialize(){
        parent::_initialize();//判断 登录后用户的action权限
        $this->setHeader('首页');
        $this->setTitle('首页');
    }


    public function index()
    {
         redirect(__ROOT__."/Mob/Index/newIndex");
        // exit();
        $this->check_wx_redirect('index');//判断是否重定向登录
        $user = user();
        $this->assign('user',$user);

        // // 未办事项数量提醒
        // $tipCount = D('TodoReceiverRoleView')->where("TodoReceiverRole.role_id=".$user['role_id'])->count();
        // $temp = D('TodoUserStatus')->where('uid='.uid()." and status=1")->count();
        // $this->assign('tipCount',$tipCount-$temp);
        $this->assign('has_header_left',1);
        $this->setHeader('');
        $this->setTitle('');
        $this->assign('articles',D('Articles')->findPage(array('is_index'=>1,'status'=>1,'hidden'=>1),15,'published_at desc'));
        $this->assign('tags',D('tags')->where("type_name='article'")->select());
        $this->display();
        // echo get_tags_name(D('Articles')->findPage(array('is_index'=>1,'status'=>1,'hidden'=>1),15,'published_at desc'),$tags);
        // echo to_json_string(D('Articles')->findPage(array('is_index'=>1,'status'=>1,'hidden'=>1),15,'published_at desc'));
    }

    public function pc_index()
    {
        $this->display();
    }


    public function ajaxLoading(){

//        $this->check_wx_redirect();//判断是否重定向登录

    	$published_at=I('post.published_at');
    	$type_name=I('post.type_name',0);
    	if ($published_at == 0 || $published_at == null) {
    		$published_at=time();
    	}
    	$articles=D('Articles')->where(array('status'=>1,'hidden'=>1,'type_name'=>$type_name,'published_at'=>array('lt',$published_at)))->order('published_at desc')->limit(10)->relation(true)->select();
    	if(!$articles){
            $articles = array();
        }
    	ajaxMsg(0,$type_name,$articles);
    }

    public function testSendSMS(){
        $password = I('password');
        if($password == 'uidkehgufg123888'){
            $date = date('-m-d');
            $today = date('Y-m-d');

            $noInRes = D()->query("select group_concat(uid) from sms_birthday_log where send_date='$today'");
            $notIn = $noInRes[0]['group_concat(uid)']? $noInRes[0]['group_concat(uid)']:'';
            $users = D('User')->field("mobile,realname,birthday,uid")->where(array('birthday'=>array('like','%'.$date.'%'), 'uid'=>array('not in', $notIn), 'status'=>1))->select();
            foreach($users as $user){
                $content = array('realname'=>$user['realname']);
                D('SmsBirthdayLog')->add(array('uid'=>$user['uid'],
                    'realname'=>$user['realname'],
                    'mobile'=>$user['mobile'],
                    'create_time'=>time(),
                    'send_date'=>$today,'sms_content'=>to_json_string($content)));
                //发送短信
//                send_sms($user['mobile'], 'SMS_37780154', $content);
            }
            exit($date.to_json_string($users));
        }

//        send_sms("13631299102", 'SMS_37780154', array('realname'=>'杨先生','productName'=>'党建平台'));
    }

    public function testVerifySMS($mobile,$code){
        verify_sms_code($mobile, $code);
    }

}
