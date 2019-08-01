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

class ShowController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
    }

    function index(){
        $type = I('type');
        $map['status'] = array('gt', -1);
        $map['type'] = $type;
        $list = D('Show')->where($map)->order(array('is_top'=>'desc','sort'=>'desc'))->select();
        if ($map['type'] ==1) {
            $this->assign('title', '党员之家');
        }else{
            $this->assign('title', '品牌创建');
        }
        $this->assign('list', $list);
        $this->display();
    }
	

	function issue(){
		$user = user();
		$this->assign('user',$user);
		$this->display();
	}

    function show(){
        $map['status'] = array('gt', -1);
        $list = D('Show')->where($map)->order(array('is_top'=>'desc','sort'=>'desc'))->select();
        $this->assign('list', $list);
        $this->display();
    }
	


    function ajaxGetList(){
        $pageIndex = I("pageIndex");
//        $lastItem = intval($lastItem);
        $start = ($pageIndex-1)*15; // 检索数据起始排序
        $end = $pageIndex * 15; // 检索数据末尾排序
//        $list = D()->query("select * from ( select top 15 * from show order by is_top desc, sort desc) s except
//                                select * from ( select top 1 * from show order by is_top desc, sort desc) s");
        $list = D('Show')->where("status > -1")->select();
        ajaxMsg(0,'list = ' + to_json_string($list), $list);
    }

    public function ajax_correct(){
        $uid = uid();
        $id = I('id');
        $correct = D('ReviewCorrect')->where(array('review_id'=>$id,'uid'=>$uid))->find();
        if($correct){//取消收藏
            D('ReviewCorrect')->where(array('review_id'=>$id,'uid'=>$uid))->delete();
            ajaxMsg(0,'已取消收藏',array('is_correct'=>0,'count'=>D('ReviewCorrect')->where(array('review_id'=>$id))->count()));
        }else{
            D('PartyClassCorrect')->add(array('class_id'=>$id,'uid'=>$uid,'create_time'=>time()));
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