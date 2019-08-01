<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Api\Controller;
use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;

class PersonController extends BaseAuthController
{

    function _initialize()
    {
        parent::_initialize();

    }

    /**
     *
     */
    public function personInfo(){
        //个人信息
        $uid = I('id');
        if(!$uid){
            return ajaxMsg(false,'missing id',array(),true);
        }
        $info = D('UserView')->field('gender,uid,adm_post_name,role_name,organization_name,branch_id,branch_name,headimgurl,realname,score')
                ->where(array('User.uid'=>$uid))->find();
        if(!$info){
        return ajaxMsg(false,'wrong id',array(),true);
        }
        //判断选择哪一个标签
        if(!empty($info['adm_post_name'])){
            $info['sign'] = $info['adm_post_name'];
        }
        else{
            $info['sign'] = $info['role_name'];
        }
        //left
        //排名
        $rank = M('user')->where(array('status'=>1,'score'=>array('gt',$info['score'])))->count();
        $info['rank'] = $rank+1;
       //篇数
        $info['notes_num'] =M('notes')->where(array('uid'=>$uid,'status'=>0))->count();
        //学时
        $info['sum_studytime'] =M('notes')->where(array('uid'=>$uid,'status'=>0))->sum('study_time');
       //个人台帐总数
        $info['tzcount'] = M('taizhang')->where(array('uid'=>$uid,'status'=>0))->count();

        //right
        //任务总数
        $info['all_mission_count'] = M('user')->alias('U')->join('mission_user as MU on MU.uid = U.uid')
            ->where(array('U.status'=>1,'MU.status'=>array('gt',-1),'U.uid'=>$uid))->count();
        //任务未完成
        $info['nodone_mission_count'] =  M('mission_user')->alias('MS')->join('user as U on U.uid = MS.uid')
            ->where(array('U.status'=>1,'MS.status'=>0,'U.uid'=>$uid))->count();
        //任务已完成
        $info['finish_mission_count'] = M('mission_user')->alias('MS')->join('user as U on U.uid = MS.uid')
            ->where(array('U.status'=>1,'MS.status'=>1,'U.uid'=>$uid))->count();
        //逾期完成
        $info['overdue_mission_count'] = M('mission_user')->alias('MS')->join('user as U on U.uid = MS.uid')
            ->where(array('U.status'=>1,'MS.status'=>2,'U.uid'=>$uid))->count();

        //bottom
        //支部积分
        $info['branch_total'] =  M('User')->where(array('branch_id'=>$info['branch_id'],'status'=>1))->sum('score');//找出支部得分=全部人分数的总和

        //支部台帐
        $info['branch_tz'] =  D('Taizhang')->where(array('branch_id'=>$info['branch_id'],'type'=>4,'status'=>0))->count();
        //排名
        $branch_count =D()->query("select count(*) as count from (select sum(score) as total_score from user u where u.status=1  group by u.branch_id  HAVING total_score> ".$info['branch_total'].") c");
        $info['branch_count'] = $branch_count[0]['count']+1;


        ajaxMsg('ok','message_ok',$info,true);
    }

    /**
     * 个人页面积分构成 六个打通
     */
    public function personScoreCompose(){

        //以下为六个打通假数据
        $datatitle = ['绩效系统','工作创新指数','纪律审查信息管理系统','廉政风险防控平台','数字人事系统','智慧人事系统',
            '工作强度指数','工作奉献指数','学习创新平台','智慧党建平台' ];
        $datavalue = [335,310,234,135,458,450,450,335, 1350,450];
        foreach($datavalue as $k=>$v){
            $newdata[$k]['value'] = $v;
            $newdata[$k]['name'] = $datatitle[$k];
        }
        $data['series'] = $newdata;
        $data['legend'] = $datatitle;
        ajaxMsg('ok','message_ok',$data,true);
    }

    public function tzList(){
        $page = I('page',1);
        $limit = I('limit',10);
        $poid = I('id');
        if(!$poid){
            return ajaxMsg(false,'missing id',array(),true);
        }

        $totallist = M('taizhang')->field('tz.uid,tz.id,tz.cover,tz.pl_count,tz.sc_count,tz.dz_count,tz.title,tt.title as tag_name,u.realname,u.headimgurl')
                ->alias('tz')->join('user u ON u.uid=tz.uid','left')
                    ->join('taizhang_tags tt ON tz.tag_id = tt.id','left')
            ->where(array('tz.uid'=>$poid,'tz.status'=>0))->
            order('tz.create_time desc')->select();

        $total = count($totallist);
        $pagetotal = ceil($total/$limit);
        //分页
       // $list = makePage($page,$limit,$total,$totallist);

//        $pagemessage['page'] = $page;
//        $pagemessage['limit'] = $limit;
//        $pagemessage['total'] = $total;
//        $pagemessage['pagetotal'] = $pagetotal;
//        $data['pageMessage'] = $pagemessage; //保留
        //去掉html
        foreach($totallist as $k => $v){
            $totallist[$k]['title'] = strip_tags($v['title']);
        }

        $data['list'] = $totallist;
        ajaxMsg('ok','message_ok',$data,true);
    }

    //学习笔记
    public function learnNotes(){
        $uid = I('id');
        if(!$uid){
            return ajaxMsg(false,'missing id',array(),true);
        }
        $list = [];
        $currentMonth = (int)date('n');
        $y=date("Y");

         //篇数
        $info['notes_num'] =M('notes')->where(array('uid'=>$uid,'status'=>0))->count();
        //学时
        $info['sum_studytime'] =M('notes')->where(array('uid'=>$uid,'status'=>0))->sum('study_time');

        //统计年累计时长
        $yearTime = "";
        for($i=$currentMonth; $i>0; $i--){

            $monthTime = strtotime($y."-".$i."-1");
            $sql = "select count(*) as note_count,sum(study_time) as sum_time from notes where status > -1 and uid = ".$uid."  and month(FROM_UNIXTIME(create_time)) = month(FROM_UNIXTIME($monthTime)) and year(FROM_UNIXTIME(create_time)) = year(curdate());";
            $numNote = D()->query($sql);
//            echo  "aaaa = ".to_json_string($numNote);
            $temp = array(
                'month'=>$i,
                'note_count'=>$numNote[0]['note_count'] == null ? 0 : $numNote[0]['note_count'],
                'sum_time'=>$numNote[0]['sum_time']
            );
            $yearTime = $yearTime + $numNote[0]['sum_time'];

            if($temp['note_count']){
                array_push($list,$temp);
            }
        }

        $data['info'] = $info;
        $data['list'] = $list;
        ajaxMsg('ok','message_ok',$data,true);

    }

    //总任务列表
    public function allMission(){
        $uid = I('id');
        if(!$uid){
            return ajaxMsg(false,'missing id',array(),true);
        }

        $info['all_mission_count'] = M('user')->alias('U')->join('mission_user as MU on MU.uid = U.uid')
            ->where(array('U.status'=>1,'MU.status'=>array('gt',-1),'U.uid'=>$uid))->select();

        // var_dump(D()->getLastSql());

    }

    //任务列表
    public function detailMission(){
        $uid = I('id');
        if(!$uid){
            return ajaxMsg(false,'missing id',array(),true);
        }

        //未完成
        $info['nodone_mission'] = M('mission_user')->field('MU.id as id,title,content,s_time,e_time')->alias('MU')->join('mission as M on M.id = MU.mission_id')->order('e_time asc')
            ->where(array('MU.status'=>0,'MU.uid'=>$uid))->select();

        foreach($info['nodone_mission'] as $k=>$v){
            $info['nodone_mission'][$k]['s_time'] = date('Y-m-d',$v['s_time']);
            $info['nodone_mission'][$k]['e_time'] = date('Y-m-d',$v['e_time']);
        }

        //完成
        $info['finish_mission'] = M('mission_user')->field('MU.id as id,title,content,s_time,e_time')->alias('MU')->join('mission as M on M.id = MU.mission_id')->order('e_time asc')
            ->where(array('MU.status'=>1,'MU.uid'=>$uid))->select();

        foreach($info['finish_mission'] as $k=>$v){
            $info['finish_mission'][$k]['s_time'] = date('Y-m-d',$v['s_time']);
            $info['finish_mission'][$k]['e_time'] = date('Y-m-d',$v['e_time']);
        }

        //逾期完成
        $info['gone_mission'] = M('mission_user')->field('MU.id as id,title,content,s_time,e_time')->alias('MU')->join('mission as M on M.id = MU.mission_id')->order('e_time asc')
            ->where(array('MU.status'=>2,'MU.uid'=>$uid))->select();

        foreach($info['gone_mission'] as $k=>$v){
            $info['gone_mission'][$k]['s_time'] = date('Y-m-d',$v['s_time']);
            $info['gone_mission'][$k]['e_time'] = date('Y-m-d',$v['e_time']);
        }

        $data = $info;
        ajaxMsg('ok','message_ok',$data,true);

    }

}


