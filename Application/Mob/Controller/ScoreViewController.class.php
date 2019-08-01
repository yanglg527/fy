<?php

/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Mob\Controller;

use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;

class ScoreViewController extends BaseAuthController
{

    function _initialize()
    {
        parent::_initialize();
        $user = user();
        $this->assign('user', $user);
    }


    //信箱列表
    public function base()
    {
        $this->display();
    }
    public function study()
    {
        $this->assign('title', '学习交流积分明细');
        $headscore = 0;
        # code...
        $tag_name = D('tags')->select();
        $user = user();
      
        $article_log = D('ArticleClickLog')->where(array('uid' => $user['uid']))->select();
        foreach ($article_log as $v) {
            $article_arr[] = $v['article_id'];
            //统计总积分
            $headscore = $v['score']+$headscore;
        }

        $article_tag_total = D('articles_tags')->where(array('article_id' => array('in', $article_arr)))->select();
     //   var_dump(count($article_tag_total));
        $article_tag = D('articles_tags')->where(array('article_id' => array('in', $article_arr)))->group('tag_id')->select();
        foreach ($article_tag as $key => $value) {
            $str = '';
            foreach ($article_tag_total as $k => $v) {
                if ($v['tag_id'] == $value['tag_id']) {
                    $article_tag[$key]['count']++;
                    $str = $str . $v['article_id'] . ',';
                }

            }
            $article_tag[$key]['article_str'] = $str;
        }
        foreach ($tag_name as $k => $tv) {
            $tag_name[$k]['count'] = 0;
            $tag_name[$k]['total'] = 0;
            $tag_name[$k]['article'] = $article_str;
            foreach ($article_tag as $v) {
                if ($tv['id'] == $v['tag_id']) {
                    $tag_name[$k]['count'] = $v['count'];
                    
                   // $tag_name[$k]['total'] = $v['count'] * 2;
                    $article_arr = rtrim($v['article_str'],',');
                    $tag_name[$k]['article_str'] = $article_arr;
                    //统计当前类型总积分
                    $tag_name[$k]['total'] = D('ArticleClickLog')->where(array('uid' => $user['uid'],'article_id'=>array('in',$article_arr)))->sum('score');   
                    break;
                }
            }
        }
       
        $this->assign('head_score',$headscore);
        $this->assign('tag_name', $tag_name);
        $this->display();
    }
    public function studyTag()
    {
        # code...
        $tag_id = I('tag_id');
        $this->assign('title', D('tags')->where(array('id' => $tag_id))->getField('name'));
        $headscore = 0;
        $list = array();
        $article_arr = I('article');
        $user = user();
        $uid = $user['uid'];
        
        if ($article_arr) {
         
            $list = D('ArticlesClick')->where("Articles.id in ($article_arr) and ArticleClickLog.uid = $uid ")->order('ArticleClickLog.create_time desc')->select();
        //    var_dump($list);
           
            foreach ($list as $k => $v) {
            
                //截取content前几个字
                $list[$k]['content'] = mb_substr(strip_tags($v['content']), 0, 20, 'utf-8') . '...';
                $headscore = $v['score'] + $headscore;
            }
        }

        $this->assign('head_score',$headscore);
        $this->assign('list', $list);

        $this->display();
    }
    /**
     * 签到页面
     */
    public function signIn(){

        $this->assign('title','签到');
        $user = user();
     
        $list = D('SignInLog')->where(array('uid'=>$user['uid']))->order('create_time desc')->select();
        $score = D('UserScoreLog')->where(array('uid'=>$user['uid'],'type'=>7))->find();
      
        $this->assign('score',$score['score']);
        $this->assign('list',$list);
        $this->display();
    }
    /**
     * 签到页面
     */
    public function memberDevelop(){

        $this->assign('title','党员发展信息');
        $user = user();
        $id = $user['uid'];
        $this->assign('id',$id);
        $tag1 = D('UserOfficialApply') -> where(array('uid' => $id)) -> find();
		$tag2 = D('UserReportView') -> where(array('UserReport.uid' => $id, 'UserReport.status' => array('gt',-1))) -> order('UserReport.create_time desc') -> select();
		$tag3 = D('UserExamView') -> where(array('UserExam.uid' => $id, 'UserExam.status' => array('gt',-1))) -> order('UserExam.create_time desc') -> select();
		$tag4 = D('UserAdviceView') -> where(array('UserAdvice.uid' => $id, 'UserAdvice.status' => array('gt',-1),)) -> order('UserAdvice.create_time desc') -> select();

        $tag1msg['count'] = count($tag1);
        $tag2msg['count'] = count($tag2);
        $tag3msg['count'] = count($tag3);
        $tag4msg['count'] = count($tag4);

        $tag1msg['score'] = 0;
        $tag2msg['score'] = 0;
        $tag3msg['score'] = 0;
        $tag4msg['score'] = 0;

		$this -> assign('tag1msg', $tag1msg);
		$this -> assign('tag2msg', $tag2msg);
		$this -> assign('tag3msg',$tag3msg);
		$this -> assign('tag4msg', $tag4msg);
        $this->assign('list',$list);
        $this->display();
    }

    public function memberService()
    {
        # code...
        $user = user();
        $uid = $user['uid'];
        $speak['count'] = D('speaking')->where(array('uid'=>$uid,'status'=>1))->count();
        $help['count'] = D('help')->where(array('uid'=>$uid,'status'=>1))->count();
        $speak['score'] =  0;
        $help['score'] = 0;
        
        $this -> assign('speak', $speak);
        $this -> assign('help', $help);
        $this->assign('head_score',$score=0);
        $this->assign('title','党员服务');
        $this->display();
    }
    /**
     * 我向组织说句话
     */
    public function speakTo()
    {
        # code...
        $user = user();
        $uid = $user['uid'];
        $this->assign('head_score',$score=0);
        $list = D() -> query("select user.realname as user_realname,user.headimgurl,speaking.content,speaking.id,speaking.create_time,
        (select count(*) from speaking_comment where speaking_comment.speaking_id = speaking.id) AS comment_count,
        (select count(*) from speaking_agree where speaking_agree.speaking_id = speaking.id) AS agree_count 
        from speaking LEFT JOIN user ON speaking.uid=user.uid 
        where (speaking.uid = $uid and speaking.status = 1) order by speaking.create_time desc ");
        foreach($list as $k=>$v){
            $list[$k]['score'] = 0;
        }
        $this->assign('title','我向组织说句话');
        $this->assign('list',$list);
        $this->display();
    }
}