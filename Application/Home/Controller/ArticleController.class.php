<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Home\Controller;
use Common\Controller\BaseController;

class ArticleController extends BaseController {

    function _initialize(){
        parent::_initialize();
        $this->setHeader('');
    }

    public function index()
    {

    	$this->setHeader('');
        // $this->setTitle('');
        $id=I('get.id');

        $id = $id?$id:I('state');
        $this->check_wx_redirect('articles_index',$id);//判断是否重定向登录

        $atricle=D('Articles')->where(array('id'=>$id,'status'=>1,'hidden'=>1))->relation(true)->find();
        $branch=D('PartyBranch')->where(array('id'=>$atricle['branch_id']))->find();
        $is_agree=D('ArticleAgree')->where(array('uid'=>uid(),'article_id'=>$atricle['id']))->find();
        $comments=D('ArticleCommentView')->where(array('article_id'=>$atricle['id'],'create_at'=>array('lt',time())))->order('create_at desc')->limit(10)->select();
        $this->assign('comments',$comments);
        $this->assign('is_agree',$is_agree);
        $this->assign('branch',$branch['name']);
        $this->assign('article',$atricle);
        // echo to_json_string($atricle);
        $this->display();
    }

    public function articles()
    {
        $id=I('get.id');
        $id = $id?$id:I('state');
        $this->check_wx_redirect('articles',$id);//判断是否重定向登录
        $vegetables = explode(", ", I('get.id'));  
        $this->assign("id",$id);
        $tag=D('Tags')->where(array('id'=>$id))->find();
        $this->setHeader($tag['name']);
        $this->setTitle($tag['name']);
        $header_left['url']=$this->get_url($id);
        $header_rink['url']=__ROOT__.'/Home/Article/add?id='.$id;
        $header_rink['icon']='am-icon-pencil-square-o';
        $header_rink['text']='发布';
        $second_tags = array();
        if ($id == 15) {
            $second_tags = D('Tags')->where(array('type_name'=>'shyk_article'))->select();
        }elseif ($id == 17) {
            $second_tags = D('Tags')->where(array('type_name'=>'mzshh_article'))->select();
        }
        $this->assign("second_tags",$second_tags);
        $this->assign("second_tags_str",to_json_string($second_tags));
        $this->assign('header_rink',$header_rink);
        $this->assign('header_left',$header_left);
        $this->assign('branchs',D('PartyBranch')->select());
        // $this->assign('articles',D('ArticlesView')->findPage(array('tag_id'=>$id,'status'=>1,'hidden'=>1),15,'Articles.published_at desc'));
        // echo to_json_string($second_tags);
        $this->display();
    }

    public function qt_articles()
    {
        $this->check_wx_redirect('articles_qt');//判断是否重定向登录

        $this->setHeader('群团组织');
        $this->setTitle('群团组织');
        $header_rink['url']=__ROOT__.'/Home/Article/add?id=4';
        $header_rink['icon']='am-icon-pencil-square-o';
        $header_rink['text']='发布';
        $header_left['url']=__ROOT__.'/Home/Index/index';

        //群团组织
        $qts = D('PartySociey')->select();
        $this->assign('qts',$qts);
        $this->assign('header_rink',$header_rink);
        $this->assign('header_left',$header_left);
        // $this->assign('articles',D('ArticlesView')->findPage(array('tag_id'=>$id,'status'=>1,'hidden'=>1),15,'Articles.published_at desc'));
        // echo to_json_string(D('ArticlesView')->findPage('tag_id=4 AND status=1 AND hidden=1',15,'Articles.published_at desc'));
        $this->display();
    }

    public function ajaxArticleLoading(){
        $id=I('post.id');
        $this->check_wx_redirect();//判断是否重定向登录

        $branch_id=I('post.branch_id');
        $second_tag=I('post.second_tag');
        $published_at=I('post.published_at');
        if ($published_at == 0 || $published_at == null) {
            $published_at=time();
        }
        $condition['tag_id'] = $id;
        $condition['status'] = 1;
        $condition['hidden'] = 1;
        $condition['published_at'] = array('lt',$published_at);
        if ($branch_id != '0' && ($id == 15 || $id == 16 || $id == 17)) {
            $condition['branch_id'] = $branch_id;
        }
        if ($second_tag != '0' && ($id == 4 || $id == 15 || $id == 17)) {
            $condition['second_tag_id'] = $second_tag;
        }
        // $articles=D('Articles')->where(array('tag_id'=>$id,'status'=>1,'hidden'=>1,'type_name'=>$type_name,'published_at'=>array('lt',$published_at)))->order('published_at desc')->limit(5)->relation(true)->select();
        $articles=D('ArticlesView')->findPage($condition,15,'Articles.published_at desc');
        // $articles=D('ArticlesView')->findPage(array('second_tag'=>$condition['second_tag']),15,'Articles.published_at desc');
        ajaxMsg(0,$condition['second_tag'],$articles['list']);
    }

    public function ajaxQtArticleLoading(){
        $this->check_wx_redirect();//判断是否重定向登录
        $second_tag=I('post.second_tag');
        $published_at=I('post.published_at');
        if ($published_at == 0 || $published_at == null) {
            $published_at=time();
        }
        $articles=D('ArticlesView')->findPage(array('tag_id'=>4,'status'=>1,'second_tag'=>$second_tag,'hidden'=>1,'published_at'=>array('lt',$published_at)),15,'Articles.published_at desc');
        ajaxMsg(0,'',$articles['list']);
    }


    public function add(){
        $id=I('get.id');
        $id = $id?$id:I('state');
        $this->check_wx_redirect('articles_add',$id);//判断是否重定向登录

        $tag=D('Tags')->where(array('id'=>$id))->find();
        if ($id == 4) {
            $this->assign("second_tags",D('Tags')->where(array('type_name'=>'qt_article'))->select());
        }elseif ($id == 15) {
            $this->assign("second_tags",D('Tags')->where(array('type_name'=>'shyk_article'))->select());
        }elseif ($id == 17) {
            $this->assign("second_tags",D('Tags')->where(array('type_name'=>'mzshh_article'))->select());
        }
        $this->assign("tag_id",$id);
        $this->assign("tag",$tag);
        $this->setHeader($tag['name']);
        $this->setTitle($tag['name']);
        $this->display();
    }

    // public function qt_add(){
    //     $tag=D('Tags')->where('id=4 OR id=5')->select();
    //     $this->assign("tag",$tag);
    //     // $this->display();
    //     echo to_json_string($tag);
    // }


    public function ajaxSaveArticle(){
        $this->check_wx_redirect();//判断是否重定向登录
        $title = I('post.title');
        $is_index = I('post.is_index');
        $tag_id = I('post.tag_id');
        $second_tag = I('post.second_tag');
        $content = $_POST['content'];
        $meeting_at = strtotime(I('post.meeting_at'));
        $video_path = I('post.video_path');
        if(!$content){
            ajaxMsg(1, "请填写发布内容");
        }
        if(!$title){
            ajaxMsg(1, "请填写发布标题");
        }

        $id = I('id');
        if($id){ // 查看编辑
//            ajaxMsg(1,to_json_string($_POST));
            D('Articles')->where(array('id'=>$id))->save(array('content'=>$content,'title'=>$title));
        }else{ // 新增
            $user = user();
            $id = D('Articles')->add(array('uid'=>$user['uid'],'content'=>$content,'title'=>$title,'create_at'=>time(),'published_at'=>time(),'is_index'=>$is_index,'branch_id'=>$user['branch_id'],'meeting_at'=>$meeting_at,'video_path'=>$video_path));
            // ajaxMsg(1,$id);
            D('ArticlesTags')->add(array('tag_id'=>$tag_id,'article_id'=>$id,'second_tag_id'=>$second_tag,));
        }

        ajaxMsg(0,$id);
    }

    public function ajaxAgree(){
        $this->check_wx_redirect();//判断是否重定向登录
        $id=I('post.id');
        $article=D('Articles')->where(array('id'=>$id))->find();
        if ($article) {
            $articleAgreeModal = D('ArticleAgree');
            $is_agree=$articleAgreeModal->where(array('uid'=>uid(),'article_id'=>$id))->find();
            if ($is_agree) {
                ajaxMsg(1,'您已点赞过了');
            }else{
                $data['uid']=uid();
                $data['article_id']=$id;
                $data['create_at']=time();
                $agree = $articleAgreeModal -> create($data);
                $articleAgreeModal->add($agree);
                $article['agree_count'] = $article['agree_count'] + 1;
                D('Articles')->save($article);
                ajaxMsg(0,'');
            }
            
        }else{
            ajaxMsg(1,'文章不存在');
        }
        
    }


    public function ajaxComment(){
        $this->check_wx_redirect();//判断是否重定向登录
        if (IS_AJAX) {
            $id=I('post.id');
            $article=D('Articles')->where(array('id'=>$id))->find();
            if ($article) {
                $content=I('post.content');
                $articleCommentModal = D('ArticleComment');
                $data['uid']=uid();
                $data['article_id']=$id;
                $data['comment']=$content;
                $data['create_at']=time();
                $comment_id = $articleCommentModal->add($data);
                $article['comment_count'] = $article['comment_count'] + 1;
                D('Articles')->save($article);
                $comment=D('ArticleCommentView')->where(array('id'=>$comment_id))->find();
                ajaxMsg(0,'',$comment);
            }
            else{
            ajaxMsg(1,'文章不存在');
            }
        }
    }

    public function ajaxLoadingComments(){
        $this->check_wx_redirect();//判断是否重定向登录
        $id=I('post.id');
        $create_at=I('post.create_at');
        $comments=D('ArticleCommentView')->where(array('article_id'=>$id,'create_at'=>array('lt',$create_at)))->order('create_at desc')->limit(10)->select();
        ajaxMsg(0,count($comments),$comments);
    }

    public function get_url($tag_id){
        if ($tag_id == 2 || $tag_id == 3) {
            return __ROOT__.'/Home/Work/index';
        }elseif ($tag_id == 5) {
            return __ROOT__.'/Home/Service/index';
        }elseif ($tag_id == 1 || $tag_id == 6 || $tag_id == 7 || $tag_id == 8 || $tag_id == 9 || $tag_id == 10 || $tag_id == 11 || $tag_id == 13 || $tag_id == 14) {
            return __ROOT__.'/Home/Study/index';
        }elseif ($tag_id == 15 || $tag_id == 16 || $tag_id == 17 || $tag_id == 18 || $tag_id == 12) {
            return __ROOT__.'/Home/Activity/index';
        }
    }

}





