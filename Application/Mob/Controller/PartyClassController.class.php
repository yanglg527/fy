<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
    namespace Mob\Controller;
use Common\Controller\BaseAuthController;
use Common\Util\VideoUploadUtil;

class PartyClassController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
    }
	
	  function ajaxUploadVideo()
    {
        
        ajaxMsg(0, "success", VideoUploadUtil::upload("experiment/"));
    }
	
	function ajaxAddClass(){
		$title = I('title');
		$speaker = I('speaker');
		$video = I('video');
		$cover = I('cover');
		$class_index = I('class_index');
		$branch_id = I('branch_id');
		$user = user();
		if(!$title){
			ajaxMsg(1, "请先填写标题");
		}
		if(!$video){
			ajaxMsg(1, "请上传视频");
		}
		if(!$branch_id){
			ajaxMsg(1, "请选择支部");
		}
		$array = array('class_index'=>$class_index,'title'=>$title,'speaker'=>$speaker,'video'=>$video,'cover'=>$cover,'uid'=>$user['uid'],'branch_id'=>$branch_id,'create_time'=>time());
		D('PartyClass')->add($array);
        if ($user) {
            update_user_score($user["uid"], 5, 5,'录入微党课');
        }
		ajaxMsg(0, "success");
	}
	
	
	function issue(){
		$user = user();
		$this->assign('user',$user);
		$branchs = D('PartyBranchSelectView')->order('id asc')->select();
        $branchs = json_encode($branchs);
		$this->assign('branchs',$branchs);
		$this->display();
	}
	
	function video(){
		$id = I('id');
        $id = $id?$id:I('state');
        $this->check_wx_redirect('party_class_video',$id);//判断是否重定向登录
        $user = user();
        
		if($id){
			$detail = D('PartyClass')->find($id);
			$this->assign('detail',$detail);
            if ($user && $detail) {
            $click_log=D('PartyClassClickLog')->where(array('uid'=>$user['uid'],'party_class_id'=>$detail['id']))->find();
            if (!$click_log) {
                update_user_score($user["uid"], 2, 5,'微党课阅读');
                D('PartyClassClickLog')->add(array('uid'=>$user['uid'],'party_class_id'=>$detail['id'],'score'=>2,'create_time'=>time()));
            }
            
            }

            $this->createPartyClassPlayLog(uid(), $id);
		}
        if(!$detail){
            $this->redirect(__ROOT__."/error3.html");
        }
		$this->display();
	}

    public function createPartyClassPlayLog($uid, $classId){
        $palyLog = array(
            'uid'=>$uid,
            'party_class_id'=>$classId,
            'create_time'=>time()
        );
        D('PartyClassPlayLog')->add($palyLog);
    }


    function ajaxGetList(){
        $lastItem = I("lastItem");
		$count = I('count',0);
        if($lastItem == 0){
            $lastItem = time();
        }
        $lastItem = intval($lastItem);
		$count = intval($count);
		
		
		
        $noteList = D()->query("select user.realname as user_realname,party_class.id,party_class.title,
party_class.cover,party_class.video,party_class.speaker,party_class.branch_id,party_class.create_time,party_branch.name as branch_name,
user.headimgurl as user_headimgurl,
(select count(*) from party_class_comment where party_class_comment.class_id = party_class.id) AS count_comment,
(select count(*) from party_class_dz where party_class_dz.class_id = party_class.id) AS count_dz,
(select count(*) from party_class_correct where party_class_correct.class_id = party_class.id) AS count_correct
from party_class LEFT JOIN party_branch 
         ON party_branch.id=party_class.branch_id LEFT JOIN user ON party_class.uid=user.uid
          where (party_class.create_time<$lastItem and party_class.status=1) order by party_class.create_time desc limit 10");
		if($count < 1){
			$count = D('PartyClass')->count();
		}else{
			$count = $count-count($noteList);
		}
        ajaxMsg(0,$count, $noteList);
    }

    function ajaxAddNote(){
        $noteContent = I("noteContent");
        $id = I("id");
        if($id > 0){
            $note = D("Notes")->find($id);
            $note['content'] = $noteContent;
            D("Notes")->save($note);
        }else{
            $note = array(
                "uid"=>uid(),
                "create_time"=>time(),
                "content"=>$noteContent
            );
            D("Notes")->add($note);
        }
        ajaxMsg(0,"");
    }

    function ajaxDeleteNote(){
        $id = I("id");
        D("Notes")->delete($id);
        ajaxMsg(0,"");
    }
    
    public function ajax_correct(){
        $uid = uid();
        $id = I('id');
        $correct = D('PartyClassCorrect')->where(array('class_id'=>$id,'uid'=>$uid))->find();
        if($correct){//取消收藏
            D('PartyClassCorrect')->where(array('class_id'=>$id,'uid'=>$uid))->delete();
            ajaxMsg(0,'已取消收藏',array('is_correct'=>0,'count'=>D('PartyClassCorrect')->where(array('class_id'=>$id))->count()));
        }else{
            D('PartyClassCorrect')->add(array('class_id'=>$id,'uid'=>$uid,'create_time'=>time()));
            ajaxMsg(0,'已收藏',array('is_correct'=>1,'count'=>D('PartyClassCorrect')->where(array('class_id'=>$id))->count()));
        }
    }
    public function ajax_comment(){
        $id = I('id');
        $content= $_POST['content'];
        D('PartyClassComment')->add(array('uid'=>uid(),'class_id'=>$id,'content'=>$content,'create_time'=>time()));
        ajaxMsg(0,"评论成功");
    }
    public function comment(){
        $id = I('id');
        $details =  D()->query("select user.realname as user_realname,party_class.id,party_class.title,
party_class.cover,party_class.video,party_class.speaker,party_class.branch_id,party_class.create_time,party_branch.name as branch_name,
user.headimgurl as user_headimgurl,
(select count(*) from party_class_comment where party_class_comment.class_id = party_class.id) AS count_comment,
(select count(*) from party_class_dz where party_class_dz.class_id = party_class.id) AS count_dz,
(select count(*) from party_class_correct where party_class_correct.class_id = party_class.id) AS count_correct
from party_class LEFT JOIN party_branch 
         ON party_branch.id=party_class.branch_id LEFT JOIN user ON party_class.uid=user.uid
          where (party_class.id<=$id) order by party_class.create_time desc limit 1");
        $this->assign('detail',$details[0]);
        $this->assign('comments',D('PartyClassCommentView')->where(array('PartyClassComment.class_id'=>$id))->order('PartyClassComment.create_time asc')->select());
        $this->display();
    }

    public function ajax_dz(){
        $uid = uid();
        $id = I('id');
        $correct = D('PartyClassDz')->where(array('class_id'=>$id,'uid'=>$uid))->find();
        if($correct){//取消收藏
            D('PartyClassDz')->where(array('class_id'=>$id,'uid'=>$uid))->delete();
            ajaxMsg(0,'已取消点赞',array('is_dz'=>0,'count'=>D('PartyClassDz')->where(array('class_id'=>$id))->count()));
        }else{
            D('PartyClassDz')->add(array('class_id'=>$id,'uid'=>$uid,'create_time'=>time()));
            ajaxMsg(0,'已点赞',array('is_dz'=>1,'count'=>D('PartyClassDz')->where(array('class_id'=>$id))->count()));
        }
    }

}