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

class DevelopController extends BaseAuthController
{

    function _initialize()
    {
        parent::_initialize();

    }

    /**
     * 党员发展(左边信息)
     */
    public function developMessage(){
        $page =I('page',1);
        //1:群众 2:积极分子 3:重点发展对象 4:预备党员
        $leftrankby = I('leftRankBy',1); 
        $limit = I('limit',10);

        //左边支部总信息
        $lefttop =array();
        //左边排序数组
        $leftlist = array();
        //左边排序数组分页情况
        $pagemessage = array();
        $pagemessage['page'] = (int)$page;
        $pagemessage['limit'] = $limit;
        //群众
        $lefttop[0]['name'] = '群众';
        $res1 = D()->query('select count(*) as num from user u right JOIN party_branch pb on pb.id = u.branch_id AND pb.id !=318 where u.role_id = 5 and u.status = 1');
        $lefttop[0]['count'] = $res1[0]['num'];
        //积极分子
        $lefttop[1]['name'] = '积极分子';
        $res2 = D()->query('select count(*) as num from user u right JOIN party_branch pb on pb.id = u.branch_id AND pb.id !=318 where u.role_id = 4 and u.status = 1');
        $lefttop[1]['count'] = $res2[0]['num'];
        //重点发展对象
        $lefttop[2]['name'] = '重点发展对象';
        $res3 = D()->query('select count(*) as num from user u right JOIN party_branch pb on pb.id = u.branch_id AND pb.id !=318 where u.role_id = 3 and u.status = 1');
        $lefttop[2]['count'] = $res3[0]['num'];
        //预备党员
        $lefttop[3]['name'] = '预备党员';
        $res4 = D()->query('select count(*) as num from user u right JOIN party_branch pb on pb.id = u.branch_id AND pb.id !=318 where u.role_id = 2 and u.status = 1');
        $lefttop[3]['count'] = $res4[0]['num'];

        //左边数组:群众
        if($leftrankby == 1){
            $title = ['序号','支部','群众'];
            $totallist = D()->query('select pb.id,pb. NAME,pb.sort,(select count(*) from user u where u.branch_id = pb.id AND u.role_id = 5 AND u. STATUS = 1) AS m_count FROM party_branch pb WHERE pb.id != 318 ORDER BY pb.sort DESC');
            //总条数
            $total = count($totallist);
            $pagetotal = ceil($total/$limit);

            //分页
            $leftlist = makePage($page,$limit,$total,$totallist);

        }
        //左边数组:积极分子
        else if($leftrankby == 2){
            $title = ['排名','支部','积极分子'];
            $totallist = D()->query('select pb.id,pb. NAME,pb.sort,(select count(*) from user u where u.branch_id = pb.id AND u.role_id = 4 AND u. STATUS = 1) AS m_count FROM party_branch pb WHERE pb.id != 318 ORDER BY pb.sort DESC');
            //总条数
            $total = count($totallist);
            $pagetotal = ceil($total/$limit);

            //分页
            $leftlist = makePage($page,$limit,$total,$totallist);
        }
        //左边数组:重点发展对象
        else if($leftrankby == 3){
            $title = ['排名','支部','重点发展对象'];
            $totallist = D()->query('select pb.id,pb. NAME,pb.sort,(select count(*) from user u where u.branch_id = pb.id AND u.role_id = 3 AND u. STATUS = 1) AS m_count FROM party_branch pb WHERE pb.id != 318 ORDER BY pb.sort DESC');
            //总条数
            $total = count($totallist);
            $pagetotal = ceil($total/$limit);

            //分页
            $leftlist = makePage($page,$limit,$total,$totallist);
        }
        //左边数组:预备党员
        else if($leftrankby == 4){
            $title = ['排名','支部','预备党员'];
            $totallist = D()->query('select pb.id,pb. NAME,pb.sort,(select count(*) from user u where u.branch_id = pb.id AND u.role_id = 2 AND u. STATUS = 1) AS m_count FROM party_branch pb WHERE pb.id != 318 ORDER BY pb.sort DESC');
            //总条数
            $total = count($totallist);
            $pagetotal = ceil($total/$limit);

            //分页
            $leftlist = makePage($page,$limit,$total,$totallist);
        }

        //汇总
        $data = array();
        $pagemessage['total'] = $total;
        $pagemessage['pagetotal'] = $pagetotal;
        $data['leftTop'] = $lefttop;
        $data['leftList'] = $leftlist;
        $data['pageMessage'] = $pagemessage;
        $data['listTitle'] = $title;

        ajaxMsg('ok','message_ok',$data,true);
    }

    /**
     * 党员发展,单独每个支部的信息(右边信息)
     */
    public function detailMessage(){
        //支部信息
        $poid = I('id',48);
        if(!$poid){
            return ajaxMsg(false,'missing id',array(),true);
        }
        //分页
        $page =I('page',1);
        //1:群众 2:积极分子 3:重点发展对象 4:预备党员
        $leftrankby = I('leftRankBy',1); 
        $limit = I('limit',10);

        //左边支部总信息
        $righttop =array();
        //左边排序数组
        $rightlist = array();
        //左边排序数组分页情况
        $pagemessage = array();
        $pagemessage['page'] = (int)$page;
        $pagemessage['limit'] = $limit;
        //群众
        $righttop[0]['name'] = '群众';
        $righttop[0]['count'] = M('user')->where(array('branch_id'=>$poid,'status'=>1,'role_id'=>5))->count();
        //积极分子
        $righttop[1]['name'] = '积极分子';
        $righttop[1]['count'] = M('user')->where(array('branch_id'=>$poid,'status'=>1,'role_id'=>4))->count();
        //重点发展对象
        $righttop[2]['name'] = '重点发展对象';
        $righttop[2]['count'] = M('user')->where(array('branch_id'=>$poid,'status'=>1,'role_id'=>3))->count();
        //预备党员
        $righttop[3]['name'] = '预备党员';
        $righttop[3]['count'] = M('user')->where(array('branch_id'=>$poid,'status'=>1,'role_id'=>2))->count();

        $title = ['序号','支部','积分'];
        //右边数组:群众
        if($leftrankby == 1){ 
            $totallist = M('user')->field('uid,realname as name,score')->where(array('branch_id'=>$poid,'status'=>1,'role_id'=>5))->order('sort desc')->select();
        }
        //右边数组:积极分子
        else if($leftrankby == 2){
            $totallist = M('user')->field('uid,realname as name,score')->where(array('branch_id'=>$poid,'status'=>1,'role_id'=>4))->order('sort desc')->select();
        }
        //右边数组:重点发展对性爱那个
        else if($leftrankby == 3){
            $totallist = M('user')->field('uid,realname as name,score')->where(array('branch_id'=>$poid,'status'=>1,'role_id'=>3))->order('sort desc')->select();
        }
        //右边数组:预备党员
        else if($leftrankby == 4){
            $totallist = M('user')->field('uid,realname as name,score')->where(array('branch_id'=>$poid,'status'=>1,'role_id'=>2))->order('sort desc')->select();
        }

        //总条数
        $total = count($totallist);
        $pagetotal = ceil($total/$limit);

        //分页
        $rightlist = makePage($page,$limit,$total,$totallist);

        //汇总
        $data = array();
        $pagemessage['total'] = $total;
        $pagemessage['pagetotal'] = $pagetotal;
        $data['rightTop'] = $righttop;
        $data['rightList'] = $rightlist;
        $data['pageMessage'] = $pagemessage;
        $data['listTitle'] = $title;

        ajaxMsg('ok','message_ok',$data,true);
    }

}


