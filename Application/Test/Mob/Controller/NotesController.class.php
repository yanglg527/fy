<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
    namespace Mob\Controller;
use Common\Controller\BaseAuthController;

class NotesController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
    }

    function learn(){
        $this->display();
    }

    function notes(){
        $this->display();
    }
    function study_time(){
        $uid = I("uid") > 0 ?  I("uid") : uid();
        $this->assign('uid', $uid);
        $currentMonth = (int)date('n');
        $yearTime = '';
        $y=date("Y");
        for($i=$currentMonth; $i>0; $i--) {
            $monthTime = strtotime($y."-".$i."-1");
            $sql = "select count(*) as note_count,sum(study_time) as sum_time from notes where status > -1 and uid = " . $uid . "  and month(FROM_UNIXTIME(create_time)) = month(FROM_UNIXTIME($monthTime)) and year(FROM_UNIXTIME(create_time)) = year(curdate());";
            $numNote = M()->query($sql);
            $yearTime = $yearTime + $numNote[0]['sum_time'];
        }
        $this->assign('yearTime',$yearTime);
        $this->display();
    }
    function note_month(){
        $uid = I("uid") > 0 ?  I("uid") : uid();
        $this->assign('uid', $uid);
        $list = [];
         $y=date("Y");
            $currentMonth = (int)date('n');
            $getyear = I('year');
         if($getyear){
            $y=I('year');
             $currentMonth = 12;


         }
        $this->assign('year', $y);
        $this->assign('month', $currentMonth);

        //统计年累计时长
        $yearTime = "";
        for($i=$currentMonth; $i>0; $i--){
//            $startDate = $y."-".$i."-1";
//            $endDate = date('Y-m-d', strtotime("$startDate +1 month 0 day"));
//            $startDate = strtotime($startDate);
//            $endDate = strtotime($endDate);
//                        echo "startTime = ".$startDate."  endTime = ".$endDate;
//            $sql = "select count(*) as note_count from notes where status > -1 and uid = $uid and create_time >= $startDate and create_time < $endDate";

            $monthTime = strtotime($y."-".$i."-1");
 
             $sql = "select count(*) as note_count,sum(study_time) as sum_time from notes where status > -1 and uid = ".$uid."  and month(FROM_UNIXTIME(create_time)) = month(FROM_UNIXTIME($monthTime)) and year(FROM_UNIXTIME(create_time)) = year(curdate());";    
           if($getyear){
              $sql = "select count(*) as note_count,sum(study_time) as sum_time from notes where status > -1 and uid = ".$uid."  and month(FROM_UNIXTIME(create_time)) = month(FROM_UNIXTIME($monthTime)) and year(FROM_UNIXTIME(create_time)) = 2017;";     
            
            }
             
            // file_put_contents('./year.txt',$sql);
            $numNote = D()->query($sql);
//            echo  "aaaa = ".to_json_string($numNote);
            $temp = array(
                'month'=>$i,
                'note_count'=>$numNote[0]['note_count'] == null ? 0 : $numNote[0]['note_count'],
                'sum_time'=>$numNote[0]['sum_time']
            );
            $yearTime = $yearTime + $numNote[0]['sum_time'];

            array_push($list,$temp);
        }
        $this->assign('yearTime',$yearTime);
//        echo  "aaaa = ".to_json_string($list);
//        $sql = "select count(*) as note_number from notes where status > -1 and uid = ". uid()." group by date_format(create_time, '%Y-%m');";
        $this->assign('list', $list);
        //统计2017一年总时长
        $lastyear_sql = "select count(*) as note_count,sum(study_time) as sum_time from notes where status > -1 and uid = '".$uid."'  and month(FROM_UNIXTIME(create_time)) in (7,8,9,10,11,12) and year(FROM_UNIXTIME(create_time)) = 2017" ;
        $lastyear = D()->query($lastyear_sql);

        $this->assign('lastyear', $lastyear);
        $this->display();
    }

    public function note_branch() {
//        $id = I('id');
//        $detail = D('Event') ->where(array('id'=>$id))-> find();
//        $this -> assign('detail', $detail);
        $this -> assign('list', D('UserView') -> where(array('uid'=>array("neq",uid()), 'status' => 1)) -> order("branch_id asc") -> select());
        $this -> display();
    }

    function note_list(){
        // 获得当前笔记所属月的第一天和下个月的第一天
        $startDate = date('Y-m-01', time());
        $endDate = date('Y-m-d', strtotime("$startDate +1 month 0 day"));
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        $monthNoteList = D('Notes')->where("status > -1 and uid = ".uid()." and create_time > ".$startDate." and create_time < ".$endDate)->order("create_time desc")->select();
//                echo "list = ".to_json_string($monthNoteList);
        $this->assign("list", $monthNoteList);
        $this->display();
    }

    function notes_book(){
        $id = I("id");

        $uid = I("uid") > 0 ?  I("uid") : uid();
        if($id > 0){
            $this->assign("selectedId", $id);
            $note = D('Notes')->find($id);

            if($note){

                // 获得当前笔记所属月的第一天和下个月的第一天
                $startDate = date('Y-m-01', $note['create_time']);
                $endDate = date('Y-m-d', strtotime("$startDate +1 month 0 day"));
                $startDate = strtotime($startDate);
                $endDate = strtotime($endDate);
//                echo "startTime = ".$startDate."  endTime = ".$endDate;
                $monthNoteList = D('NotesView')->where("Notes.status > -1 and Notes.uid = ".$note['uid']." and Notes.create_time > ".$startDate." and Notes.create_time < ".$endDate)->order("Notes.create_time desc")->select();
//                echo "list = ".to_json_string($monthNoteList);

            }
        }else{
//            exit("aaaaaa");
            $month = I("month");
            $y = I('year');
            if($month > 0){
//                $y=date("Y");
                // 获得当前笔记所属月的第一天和下个月的第一天
//                $monthTime = strtotime($y."-".$i."-1");
                $startDate = $y."-".$month."-1";
                $endDate = date('Y-m-d', strtotime("$startDate +1 month 0 day"));
                $startDate = strtotime($startDate);
                $endDate = strtotime($endDate);
//                echo "startTime = ".$startDate."  endTime = ".$endDate;
                $monthNoteList = D('NotesView')->where("Notes.status > -1 and Notes.uid = ".$uid." and Notes.create_time >
                ".$startDate." and Notes.create_time < ".$endDate)->order("Notes.create_time desc")->select();
            }
        }
        //D()->getLastSql()
       // var_dump($monthNoteList);
        $this->assign("list", $monthNoteList);
        $this->display();
    }


    function notes_edit(){
        $id = I("id");
        $aid = I("aid");
        if($id && $id > 0){
            $note = D("Notes")->find($id);
            if($note['review_id'] > 0){
                $weixinde = D('Review')->find($note['review_id']);

                if($weixinde){
                    $note['weixinde'] = $weixinde['content'];
                }
            }
        }else{
            $note = array(
                'address'=>"珠海国税",
                'study_time'=>0.5
            );
            if($aid && $aid > 0){
                $article = D("Articles")->find($aid);
                if($article){
                    $note['article_id'] = $article['id'];
                    $note['content'] = $article['title'];
                }
            }
        }
        $this->assign("detail", $note);
        $this->display();
    }

    function notes_detail(){
        $id = I("id");
        $note = D("Notes")->find($id);
        $this->assign("detail", $note);
        if(!$note){
            $this->redirect(__ROOT__."/error3.html");
        }
        $this->display();
    }

    function ajaxGetNoteList(){

        $lastItem = I("lastItem");
        if($lastItem == 0){
            $lastItem = time();
        }
        $lastItem = $lastItem?$lastItem:I('state');
        $this->check_wx_redirect('notes_article',$lastItem);//判断是否重定向登录
        $user = user();
        $noteList = D("Notes")->where(array("status"=>0,"uid"=>$user['uid'], "create_time"=>array("lt", $lastItem)))->order("create_time desc")->limit(20)->select();
        $this->assign("noteList", $noteList);
        ajaxMsg(0,to_json_string($noteList), $noteList);
    }

    function ajaxAddNote(){
        $studyTitle = $_POST['studyTitle'];
        $studyBackground = $_POST['studyBackground'];
        $studyCitation = $_POST['studyCitation'];
        $studyAddress = $_POST['studyAddress'];
        $studyTime = $_POST['studyTime'];
        $studyType = $_POST['studyType'];
        $studyTeacher = $_POST['studyTeacher'];
        $xindeContent = $_POST['xindeContent'];
        $noteContent = $_POST['content'];
        file_put_contents('./test.txt',var_export($noteContent,true));
        $articleId =  $_POST['articleId'];
        $id = $_POST['id'];
        if($id > 0){
//            ajaxMsg(1, "aaaa = " . $xindeContent);
            $note = D("Notes")->find($id);
            $note['title'] = $studyTitle;
            $note['background'] = $studyBackground;
            $note['citation'] = $studyCitation;
            $note['address'] = $studyAddress;
            if($note['study_time'] - $studyTime != 0){
                updateStudyTime($studyTime-$note['study_time']);
                $note['study_time'] = $studyTime;
            }
            $note['type'] = $studyType;
            if($studyType == 1){
                $note['teacher'] = $studyTeacher;
            }
            $note['content'] = $noteContent;

            if($note['review_id'] > 0){
                $weixinde = D('Review')->find($note['review_id']);
                if($weixinde){
                    $weixinde['content'] = $xindeContent;
//                    ajaxMsg(1, $xindeContent);
                    D('Review')->where("id = ".$note['review_id'])->save($weixinde);
                }
            }else if($xindeContent != null){
                $user = user();
                $weixinde = array(
                    "create_time"=>time(),
                    "title"=>$studyTitle,
                    "branch_id"=>$user['branch_id'],
                    "uid"=>$user['uid'],
                    "content"=>$xindeContent
                );
                $reviewId = D('Review')->add($weixinde);
                $note['review_id'] = $reviewId;
            }

            D("Notes")->where("id = ".$id)->save($note);
        }else{
            $user = user();
            $note = array(
                "uid"=>uid(),
                "branch_id"=>$user['branch_id'],
                "create_time"=>time(),
            );

            if($xindeContent != null){
                $weixinde = array(
                    "create_time"=>time(),
                    "title"=>$studyTitle,
                    "branch_id"=>$user['branch_id'],
                    "uid"=>$user['uid'],
                    "content"=>$xindeContent
                );
                $reviewId = D('Review')->add($weixinde);
                $note['review_id'] = $reviewId;
            }
            $note['article_id'] = $articleId;
            $note['title'] = $studyTitle;
            $note['background'] = $studyBackground;
            $note['citation'] = $studyCitation;
            $note['address'] = $studyAddress;
            $note['study_time'] = $studyTime;
            $note['type'] = $studyType;
            if($studyType == 1){
                $note['teacher'] = $studyTeacher;
            }
            $note['content'] = $noteContent;

            D("Notes")->add($note);

            updateStudyTime($studyTime);
            if (uid()) {
                update_user_score(uid(), 2, 5,'录入学习笔记');
            }
//            ajaxMsg(1, "bbb = " . $xindeContent);
        }
        ajaxMsg(0,"");
    }

    function ajaxAddStudyTime(){
        $studyTime = I('studyTime');
//        ajaxMsg(1,"studyTime = ".$studyTime);
        $studyTimeSum = updateStudyTime($studyTime);
        ajaxMsg(0,"", $studyTimeSum);
    }

    function ajaxDeleteNote(){
        $id = I("id");
        D("Notes")->delete($id);
        ajaxMsg(0,"");
    }

    public function articles(){
        $tag_id=I('get.id');
        $tag_id=$tag_id?$tag_id:0;
        $tag = D('Tags')->where(array("id"=>$tag_id))->find();
        $this->assign("head_title", $tag['name']);
        $this->assign("tag_id", $tag_id);
        $this->display();
    }

    public function leadership(){
        $this->assign("head_title", '重要领导讲话');
        $this->display();
    }
    public function article(){
        $id=I('get.id');
        $id = $id?$id:I('state');
        $this->check_wx_redirect('notes_article',$id);//判断是否重定向登录
        $user = user();
        $article=D('Articles')->where(array("id"=>$id,"hidden"=>1,"status"=>1))->find();
        if ($user && $article) {
            $click_log=D('ArticleClickLog')->where(array('uid'=>$user['uid'],'article_id'=>$article['id']))->find();
            if (!$click_log) {
                $score = 1;
            //    update_user_score($user["uid"], 2, 5,'文章阅读'); //加分
                D('ArticleClickLog')->add(array('uid'=>$user['uid'],'article_id'=>$article['id'],'score'=>$score,'create_time'=>time()));
                $check_read = update_at_read_log($user['uid'],2,$article['id'],$score);
                if($check_read){
                    update_user_score($user["uid"], $score, 1,'阅读台账得分');
                }
            }
            
        }
        if ($article) {
            $this->assign("article", $article);
            $this->display();
        }else{
            $this->redirect('error');
        }
        
    }

    public function ajaxArticleLoading(){
        $user = user();
        $tag_id=I('post.tag_id');
        $publish_time=I('post.publish_time');
        $where["tag_id"]=$tag_id;
        $where["hidden"]=1;
        $where["status"]=1;
        if ($publish_time > 0) {
            $where["published_at"]=array('lt',$publish_time);
        }
        $articles=D('ArticlesView')->where($where)->order('published_at desc')->limit(10)->select();
        foreach($articles as $index=>$article){
            $articles[$index]['readed'] = 0;
            $articles[$index]['content'] = sub_str_text($article['content']);
            $click_log=D('ArticleClickLog')->where(array('uid'=>$user['uid'],'article_id'=>$article['id']))->find();
            if ($click_log) {
                $articles[$index]['readed'] = 1;
            }
        }
        // echo to_json_string($articles);
        ajaxMsg(0,'',$articles);
    }
}