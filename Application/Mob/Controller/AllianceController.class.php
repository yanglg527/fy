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

class AllianceController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
    }
	
	function index(){
		$this->assign("head_title", "群团组织");
		$this->display();
	}
	
	function ajaxAdd(){
		$title = I('title');
		$content = $_POST['content'];
		$type = I('type','labor');
		$user = user();
		$branch_id = $user['branch_id'];
		if(!$title){
			ajaxMsg(1, "请先填写标题");
		}
		if(!$content){
			ajaxMsg(1, "请填写内容");
		}
		$array = array('title'=>$title,'content'=>$content,'type'=>$type,'uid'=>$user['uid'],'branch_id'=>$branch_id,'create_time'=>time());
		D('AllianceArticle')->add($array);
		ajaxMsg(0, "success");
	}
	function edit(){
		 $this->assign('type',I('type','labor'));
		$this->display();
	}
	
	function detail(){
		$id = I('id');
// 		$noteList = D()->query("select user.realname as user_realname,alliance_article.id,alliance_article.title,
// alliance_article.content,alliance_article.branch_id,party_branch.name as branch_name,
// user.headimgurl as user_headimgurl
// from alliance_article LEFT JOIN party_branch 
//          ON party_branch.id=alliance_article.branch_id LEFT JOIN user ON alliance_article.uid=user.uid
//           where (alliance_article.id=$id) order by alliance_article.create_time desc limit 1");
		$id = $id?$id:I('state');
        $this->check_wx_redirect('alliance_article_detail',$id);//判断是否重定向登录
        $user = user();
        $article=D('AllianceArticle')->where(array('id'=>$id,'status'=>array('gt',-1)))->find();
        if ($user && $article) {
            $click_log=D('AllianceArticleClickLog')->where(array('uid'=>$user['uid'],'alliance_article_id'=>$article['id']))->find();
            if (!$click_log) {
                update_user_score($user["uid"], 2, 2,'群团组织文章阅读');
                D('AllianceArticleClickLog')->add(array('uid'=>$user['uid'],'alliance_article_id'=>$article['id'],'score'=>2,'create_time'=>time()));
            }
            
        }
		
		  $this->assign('detail',$article);
		$this->display();
	}
	

    function ajaxLoadData(){
        $lastItem = I("lastItem",0);
        $lastItem = intval($lastItem);
		$type = I('type','labor');
        $noteList = D()->query("select user.realname as user_realname,alliance_article.id,alliance_article.title,
alliance_article.content,alliance_article.branch_id,party_branch.name as branch_name,
user.headimgurl as user_headimgurl
from alliance_article LEFT JOIN party_branch 
         ON party_branch.id=alliance_article.branch_id LEFT JOIN user ON alliance_article.uid=user.uid
          where (alliance_article.type='$type' and alliance_article.status>-1) order by alliance_article.is_top desc, alliance_article.create_time desc limit $lastItem,15");
		  
        ajaxMsg(0,$lastItem + 15, $noteList);
    }


}