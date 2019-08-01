<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
    namespace Mail\Controller;
use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;

class IndexController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
        $this->setHeader('书记信箱');
    }


    //信箱列表
    public function index()
    {
        $this->check_wx_redirect('mail_index');//判断是否重定向登录
        $type = I('type', 0);
        $uid = uid();
        if($type == 1){//发件箱
            $list = D('MailOutboxView')->where("MailOutbox.uid=$uid and MailOutbox.status>-1")->order('MailOutbox.create_time desc')->select();
        }else{//收件箱
            $list = D('MailInboxView')->where("MailInbox.uid=$uid and MailOutbox.status>-1")->order('MailInbox.create_time desc')->select();
        }
        $this->assign('list',$list);
//        exit( to_json_string($list));
        $header_left['url'] = __ROOT__."/Home/Service/index";
        $this->assign('header_left', $header_left);
        $this->assign('type',$type);
        $this->setTitle('书记信箱');
        $this->display();
    }


    public function writeLetter(){
        $this->check_wx_redirect('mail_writeLetter');//判断是否重定向登录
        $sjlist = D('UserView')->where('post_id=1')->select();
        $this->assign('sjlist',$sjlist);
        $this->display();
    }

    public function ajaxSend(){
        $this->check_wx_redirect('mail_index');//判断是否重定向登录
        $rUid = I('rUid');
        $title = I('title');
        $content = I('content');
        $type = I('type',0);

        if(!$title)
            ajaxMsg(1,'请先填写标题');
        if(!$rUid)
            ajaxMsg(1,'请先选择收件人');
        if(!$content)
            ajaxMsg(1,'请先信件内容');

        $receiver = D('User')->find($rUid);

        $user = user();
        $time = time();
        $out = array(
            'title'=>$title,
            'receiver_uids'=>$rUid,
            'uid'=>uid(),
            'cover'=>$user['headimgurl'],
            'receiver_names'=>$receiver['realname'],
            'status'=>1,
            'msg'=>str_replace(array("\r\n", "\r", "\n"),"<br>", $content),
            'type'=>$type,
            'send_count'=>1,
            'create_time'=>$time
        );
        $oid = D('MailOutbox')->add($out);
        $in = array(
            'uid'=>$rUid,
            'outbox_id'=>$oid,
            'read_status'=>0,
            'create_time'=>$time
        );
        D('MailInbox')->add($in);

        ajaxMsg(0, '发送成功');
    }

    public function replyLetter(){
        $id=I('id');
        $id = $id?$id:I('state');
        $this->check_wx_redirect('mail_replyLetter',$id);//判断是否重定向登录
        $rUid = I('rUid');
        $last = D('MailOutbox')->find($id);
//        exit(to_json_string($last));
        if($last['org_id']){
            $org = D('MailOutbox')->find($last['org_id']);
            $title = "回复: ". $org['title'];
//            $cover = $org['cover'];
        }else {
            $title = "回复: ". $last['title'];
//            $cover = $last['cover'];
        }
        $receiver = D('User')->find($rUid);
        $this->assign('receiver',$receiver);
        $last['msg'] = "\r\n\r\n\r\n-----原始邮件-----\r\n".str_replace(array("<br>"),"\r\n", $last['msg']);
        $this->assign('reply_title',$title);
        $this->assign('detail',$last);
        $this->display();
    }

    public function ajaxReply(){

        $rUid = I('rUid');
        $this->check_wx_redirect('mail_index');//判断是否重定向登录
        $content = I('content');
//        $title = I('title');
        if(!$content)
            ajaxMsg(1,'请先信件内容');
        $content = str_replace(array("\r\n", "\r", "\n"),"<br>", $content);

        $id = I('id');
        $last = D('MailOutbox')->find($id);
        $org_id = $last['org_id'];
        $user = user();
        if($last['org_id']){
            $org = D('MailOutbox')->find($last['org_id']);
            $title = "回复: ". $org['title'] . "";
            $cover = $user['headimgurl'];
        }else {
            $title = "回复: ". $last['title'] . "";
            $cover = $user['headimgurl'];
            $org_id = $id;
        }

        $receiver = D('User')->find($rUid);

        $time = time();
        $out = array(
            'title'=>$title,
            'receiver_uids'=>$rUid,
            'uid'=>uid(),
            'cover'=> $cover,
            'receiver_names'=> $receiver['realname'],
            'status'=>1,
            'msg'=>$content,
            'type'=>$last['type'],
            'send_count'=>1,
            'org_id'=>$org_id,
            'create_time'=>$time
        );
        $oid = D('MailOutbox')->add($out);
        $in = array(
            'uid'=>$rUid,
            'outbox_id'=>$oid,
            'read_status'=>0,
            'create_time'=>$time
        );
        D('MailInbox')->add($in);

        ajaxMsg(0, '发送成功');
    }

    public function sendDetail()
    {
        $id = I('id');
        $id = $id?$id:I('state');
        $this->check_wx_redirect('mail_sendDetail',$id);//判断是否重定向登录
        $detail = D('MailOutboxView')->where("MailOutbox.id=$id")->find();
        if($detail['status'] == 1)
        $this->assign('detail',$detail);
        $this->setHeader($detail['title']);
        $this->setTitle($detail['title']);
        $this->display();
    }


    public function detail()
    {
        $id = I('id');
        $id = $id?$id:I('state');
        $this->check_wx_redirect('mail_detail',$id);//判断是否重定向登录
        D('MailInbox')->where(array('id'=>$id))->save(array('read_status'=>'1'));
        $detail = D('MailInboxView')->where("MailOutbox.id=$id")->find();
        if($detail['status'] == 1)
        $this->assign('detail',$detail);
        $this->setHeader($detail['title']);
        $this->setTitle($detail['title']);
        $this->display();
    }




    public function pc_index()
    {
        $this->display();
    }
}