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

class BranchController extends BaseAuthController
{

    function _initialize()
    {
        parent::_initialize();

    }

    /**
     * 党组数据
     *
     */
    public function branchInfo(){
        //个人信息
        $branchId = I('id');
        if(!$branchId){
            return ajaxMsg(false,'missing id',array(),true);
        }
        $info = M('party_branch')->field('id,name')->where(array('id'=>$branchId))->find();
        //成员数
        $info['member_num'] = M('user')->where(array('branch_id'=>$branchId,'status'=>1))->count();
        //排名
        $rank = M('user')->where(array('status'=>1,'score'=>array('gt',$info['score'])))->count();
        $info['rank'] = $rank+1;
        //积分总数
        $info['score'] = M('user')->where(array('branch_id'=>$branchId,'status'=>1))->sum('score');
        //支部台帐
        $info['branch_tz'] =  D('Taizhang')->where(array('branch_id'=>$branchId,'type'=>4,'status'=>0))->count();
        //篇数
        $notesnum =M()->query("select count(*) num from (select n.title,n.status from user u left join notes n on
              n.uid =u.uid and n.status=0 where u.status = 1 and u.branch_id = ".$branchId.") as b_user_notes");
        $info['notes_num'] = $notesnum[0]['num'];
        //学时
        $studytime = M()->query("select sum(bun.study_time) sum_studytime from (select n.study_time, n.title,n.status
    from user u left join notes n on n.uid =u.uid and n.status=0 where u.status = 1 and u.branch_id = ".$branchId.") as bun");
        $info['sum_studytime'] =$studytime[0]['sum_studytime'];

        //任务总数
        $info['all_mission_count'] = M('user')->alias('U')->join('mission_user as MU on MU.uid = U.uid')
            ->where(array('U.status'=>1,'MU.status'=>array('gt',-1),'U.branch_id'=>$branchId))->count();
        //任务未完成
        $info['nodone_mission_count'] =  M('mission_user')->alias('MS')->join('user as U on U.uid = MS.uid')
            ->where(array('U.status'=>1,'MS.status'=>0,'U.branch_id'=>$branchId))->count();
        //任务已完成
        $info['finish_mission_count'] = M('mission_user')->alias('MS')->join('user as U on U.uid = MS.uid')
            ->where(array('U.status'=>1,'MS.status'=>1,'U.branch_id'=>$branchId))->count();
        //逾期完成
        $info['overdue_mission_count'] = M('mission_user')->alias('MS')->join('user as U on U.uid = MS.uid')
            ->where(array('U.status'=>1,'MS.status'=>2,'U.branch_id'=>$branchId))->count();
        ajaxMsg('ok','message_ok',$info,true);
    }


    /**
     * 支部里面的其他支部
     * 支部要分页
     */
    public function otherBranch(){
        $bid = I('id');
        $page = I('page',1);
        $limit = I('limit',10);
        if(!$bid){
            return ajaxMsg(false,'missing id',array(),true);
        }
        $info =  M('party_branch')->field('id,name,sort')->
        where('id != '.$bid.' and id != 318 ')->order('sort desc')->select();//支部
        //分页
        $leftlist = makePage($page,$limit,count($info),$info);
        ajaxMsg('ok','message_ok',$leftlist,true);
    }
    /**
     * 支部里面的成员，要分页
     */
    public function zbMember(){
        $oid = I('id',1);
        $page = I('page',1);
        $limit = I('limit',10);
        if(!$oid){
            return ajaxMsg(false,'missing id',array(),true);
        }
        $info =  M('user')->field('uid,realname,sort')->
        where(array('branch_id'=>$oid,'status'=>1))->order('sort desc')->select();//成员
        //分页
        $leftlist = makePage($page,$limit,count($info),$info);
        ajaxMsg('ok','message_ok',$leftlist,true);
    }
    /**
     * 支部积分构成 六个打通
     */
    public function branchScoreCompose(){

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
            ->where(array('tz.uid'=>$poid,'tz.type'=>4,'tz.status'=>0))->
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


