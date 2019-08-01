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

class ReviewController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
    }
	

	function ajaxAdd(){
		$title = I('title');
		$content = $_POST['content'];
		$user = user();
		if(!$title){
			ajaxMsg(1, "请先填写标题");
		}
		if(!$content){
			ajaxMsg(1, "请填写心得体会");
		}
        $branch_id = $user['branch_id'];
		$array = array('content'=>$content,'title'=>$title,'uid'=>$user['uid'],'branch_id'=>$branch_id,'create_time'=>time());
		D('Review')->add($array);
        if ($user) {
            update_user_score($user["uid"], 2, 5,'录入微心得');
        }
		ajaxMsg(0, "success");
	}
	
	
	function issue(){
		$user = user();
		$this->assign('user',$user);
		$this->display();
	}
	


    function ajaxGetList(){
        $lastItem = I("lastItem");
        if($lastItem == 0){
            $lastItem = time();
        }
        $lastItem = intval($lastItem);
        $noteList = D()->query("select user.realname as user_realname,review.id,review.title,
review.content,review.branch_id,review.create_time,party_branch.name as branch_name,
user.headimgurl as user_headimgurl,
(select count(*) from review_comment where review_comment.review_id = review.id) AS count_comment,
(select count(*) from review_dz where review_dz.review_id = review.id) AS count_dz,
(select count(*) from review_correct where review_correct.review_id = review.id) AS count_correct
from review LEFT JOIN party_branch 
         ON party_branch.id=review.branch_id LEFT JOIN user ON review.uid=user.uid
          where (review.create_time<$lastItem) order by review.create_time desc limit 10");
        ajaxMsg(0,'', $noteList);
    }

    public function ajax_correct(){
        $uid = uid();
        $id = I('id');
        $correct = D('ReviewCorrect')->where(array('review_id'=>$id,'uid'=>$uid))->find();
        if($correct){//取消收藏
            D('ReviewCorrect')->where(array('review_id'=>$id,'uid'=>$uid))->delete();
            ajaxMsg(0,'已取消收藏',array('is_correct'=>0,'count'=>D('ReviewCorrect')->where(array('review_id'=>$id))->count()));
        }else{
            D('ReviewCorrect')->add(array('review_id'=>$id,'uid'=>$uid,'create_time'=>time()));
            ajaxMsg(0,'已收藏',array('is_correct'=>1,'count'=>D('ReviewCorrect')->where(array('review_id'=>$id))->count()));
        }
    }
    public function ajax_comment(){
        $id = I('id');
        $content= $_POST['content'];
        D('ReviewComment')->add(array('uid'=>uid(),'review_id'=>$id,'content'=>$content,'create_time'=>time()));
        ajaxMsg(0,"评论成功");
    }
    public function comment(){
        $id = I('id');
        $details =  D()->query("select user.realname as user_realname,review.id,review.title,
review.content,review.branch_id,review.create_time,party_branch.name as branch_name,
user.headimgurl as user_headimgurl,
(select count(*) from review_comment where review_comment.review_id = review.id) AS count_comment,
(select count(*) from review_dz where review_dz.review_id = review.id) AS count_dz,
(select count(*) from review_correct where review_correct.review_id = review.id) AS count_correct
from review LEFT JOIN party_branch 
         ON party_branch.id=review.branch_id LEFT JOIN user ON review.uid=user.uid
          where (review.id=$id) order by review.create_time desc limit 1");
        $this->assign('detail',$details[0]);
        $this->assign('comments',D('ReviewCommentView')->where(array('ReviewComment.review_id'=>$id))->order('ReviewComment.create_time asc')->select());
        $this->display();
    }

    public function ajax_dz(){
        $uid = uid();
        $id = $_POST['id'];
        $correct = D('ReviewDz')->where(array('review_id'=>$id,'uid'=>$uid))->find();
        if($correct){//取消收藏
            D('ReviewDz')->where(array('review_id'=>$id,'uid'=>$uid))->delete();
            ajaxMsg(0,'已取消点赞',array('is_dz'=>0,'count'=>D('ReviewDz')->where(array('review_id'=>$id))->count()));
        }else{
            D('ReviewDz')->add(array('review_id'=>$id,'uid'=>$uid,'create_time'=>time()));
            ajaxMsg(0,'已点赞',array('is_dz'=>1,'count'=>D('ReviewDz')->where(array('review_id'=>$id))->count()));
        }
    }

}