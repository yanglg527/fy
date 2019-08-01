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

class PartyController extends BaseAuthController
{

    function _initialize()
    {
        parent::_initialize();

    }

    /**
     * 党组数据
     */
    public function dzInfo(){
        //党组信息
        $poid = I('id',1);
        if(!$poid){
            return ajaxMsg(false,'missing id',array(),true);
        }
        $info = M('party_organization')->where(array('id'=>$poid))->field('id ,name,cover')->find();
        //党组书记
        $info['sj_name'] = M('user')->alias('U')->join('party_organization_sj as SJ on SJ.uid = U.uid')->where(array('SJ.organization_id'=>$poid))->getField('U.realname');
        //成员数
        $info['member_num'] = M('user')->where(array('organization_id'=>$poid,'status'=>1))->count();
        //积分总数
        $info['score'] = M('user')->where(array('organization_id'=>$poid,'status'=>1))->sum('score');
        //排名
        $rank = M()->query("select count(*) as count from (select sum(score) as total_score from user u  group by u.organization_id HAVING total_score>".$info['score'].") c");
        $info['rank'] = $rank[0];
        //篇数
        $notesnum =M()->query("select count(*) num from (select n.title,n.status from user u left join notes n on
              n.uid =u.uid and n.status=0 where u.status = 1 and u.organization_id = ".$poid.") as po_user_notes");
        $info['notes_num'] = $notesnum[0]['num'];
        //学时
        $studytime = M()->query("select sum(pun.study_time) sum_studytime from (select n.study_time, n.title,n.status from user u left join notes n on n.uid =u.uid and n.status=0 where u.status = 1 and u.organization_id = ".$poid.") as pun");
        $info['sum_studytime'] =$studytime[0]['sum_studytime'];
        //台帐
        $info['tzcount'] = M('taizhang')->where(array('taizhang.organization_id'=>$poid,'type'=>2,'status'=>0))->count();
        //任务总数
        $info['all_mission_count'] = M('user')->alias('U')->join('mission_user as MU on MU.uid = U.uid')
            ->where(array('U.status'=>1,'MU.status'=>array('gt',-1),'U.organization_id'=>$poid))->count();
        //任务未完成
        $info['nodone_mission_count'] =  M('mission_user')->alias('MS')->join('user as U on U.uid = MS.uid')
            ->where(array('U.status'=>1,'MS.status'=>0,'U.organization_id'=>$poid))->count();
        //任务已完成
        $info['finish_mission_count'] = M('mission_user')->alias('MS')->join('user as U on U.uid = MS.uid')
            ->where(array('U.status'=>1,'MS.status'=>1,'U.organization_id'=>$poid))->count();
        //逾期完成
        $info['overdue_mission_count'] = M('mission_user')->alias('MS')->join('user as U on U.uid = MS.uid')
            ->where(array('U.status'=>1,'MS.status'=>2,'U.organization_id'=>$poid))->count();

        ajaxMsg('ok','message_ok',$info,true);
    }
    /**
     * 党组里面的其他党组
     */
    public function otherDz(){
        $poid = I('id',1);
        if(!$poid){
            return ajaxMsg(false,'missing id',array(),true);
        }
       $info =  M('party_organization')->field('id,name,sort,cover')->
       where('id != '.$poid.' and id != 21 ')->order('sort desc')->select();//党组

        ajaxMsg('ok','message_ok',$info,true);
    }
    /**
     * 党组里面的成员
     */
    public function dzMember(){
        $oid = I('id',1);
        if(!$oid){
            return ajaxMsg(false,'missing id',array(),true);
        }
        $info =  M('user')->field('uid,realname,sort,headimgurl')->
        where(array('organization_id'=>$oid))->order('sort desc')->select();//成员
        ajaxMsg('ok','message_ok',$info,true);
    }

    /**
     * 党组积分构成 六个打通
     */
    public function dzScoreCompose(){

        //党组信息
//        $id = I('id',1);
//        if(!$id){
//            return ajaxMsg(false,'missing id',array(),true);
//        }
//        $total = D('UserScoreLogView')->where(array('User.organization_id'=>$id))->sum('UserScoreLog.score');
//        $count[0] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>1,'User.organization_id'=>$id))->sum('UserScoreLog.score');//学习交流
//        $count[1] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>2,'User.organization_id'=>$id))->sum('UserScoreLog.score');//党员发展
//        $count[2] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>3,'User.organization_id'=>$id))->sum('UserScoreLog.score');//群团组织
//        $count[3] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>4,'User.organization_id'=>$id))->sum('UserScoreLog.score');//四个机制
//        $count[4] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>5,'User.organization_id'=>$id))->sum('UserScoreLog.score');//党员服务
//        $count[5] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>6,'User.organization_id'=>$id))->sum('UserScoreLog.score');//平台签到
//        $count[6] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>7,'User.organization_id'=>$id))->sum('UserScoreLog.score');//我的通知
//        $count[7] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>8,'User.organization_id'=>$id))->sum('UserScoreLog.score');//绩效
//        foreach($count as $index=>$c){
//            $countPei[$index] = 0;
//            $count[$index] = $count[$index]?$count[$index]:0;
//            $countPei[$index] = $count[$index]<0?0:$count[$index];
//        } //数值
//           $scoretype =  D('UserScoreType')->order("id asc")->select();//名称
//        $data = array();//圆饼图的key value
//            foreach($countPei as $k=>$v){
//                $data[$k]['value'] = $countPei[$k];
//                $data[$k]['name'] = $scoretype[$k]['name'];
//            }
//        //数据标题
//
//
//            var_dump($data);
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

    /**
     * 党组台账列表
     */
    public function tzList(){
        $page = I('page',1);
        $limit = I('limit',10);
        $poid = I('id',1);
        if(!$poid){
            return ajaxMsg(false,'missing id',array(),true);
        }

        $totallist = M('taizhang')->field('tz.uid,tz.id,tz.cover,tz.pl_count,tz.sc_count,tz.dz_count,tz.title,tt.title as tag_name,u.realname,u.headimgurl')
            ->alias('tz')->join('user u ON u.uid=tz.uid','left')
            ->join('taizhang_tags tt ON tz.tag_id = tt.id','left')
            ->where(array('tz.uid'=>$poid,'tz.type'=>2,'tz.status'=>0))->
            order('tz.create_time desc')->select();
//        $total = count($totallist);
//        $pagetotal = ceil($total/$limit);
//        //分页
//        $list = makePage($page,$limit,$total,$totallist);
//
//        $pagemessage['page'] = $page;
//        $pagemessage['limit'] = $limit;
//        $pagemessage['total'] = $total;
//        $pagemessage['pagetotal'] = $pagetotal;
//        $data['pageMessage'] = $pagemessage; //保留
        $data['list'] = $totallist;
        ajaxMsg('ok','message_ok',$data,true);
    }
}


