<?php
namespace Home\Controller;

use Common\Controller\BaseController;
use Think\Controller;
use Weixin\Util\PayUtils;

/**
 * 党费管理
 * Class CostController
 * @package Home\Controller
 */
set_time_limit(240);
class CostController extends BaseController
{
    public function index()
    {
        $this->display();
    }

    public function branchs()
    {
        $this->check_wx_redirect('cost_branchs');//判断是否重定向登录

        $list = D('PartyBranchView')->group("PartyBranch.id")->order('PartyBranch.id asc')->select();



        $partybranchId = C('ROLE_PARTY_ID');
        $counts = D('User')->where("role_id=$partybranchId")->field("count(*) as count,branch_id")->group("branch_id")->select();
        $total_pays = D('PartyFeeUserView')->field("sum(PayOrder.total_fee) as total_pay,PartyFee.branch_id as branch_id")->group("PartyFee.branch_id")->select();
        $total_has_pays = D('PartyFeeUserView')->where(array('PayOrder.status'=>1))->field("sum(PayOrder.total_fee) as total_pay,PartyFee.branch_id as branch_id")->group("PartyFee.branch_id")->select();
        $total_pay_counts = D('PartyFeeUserView')->field("count(*) as total_count,PartyFee.branch_id as branch_id")->group("PartyFee.branch_id")->select();
        $total_has_pay_counts = D('PartyFeeUserView')->where(array('PayOrder.status'=>1))->field("count(*) as total_count,PartyFee.branch_id as branch_id")->group("PartyFee.branch_id")->select();
        $sjId = C('POST_SJ_ID');
        $shujis =  D('User')->where("post_id=$sjId  and branch_hq_id is null")->field("realname as shuji_realname,branch_id")->group("branch_id")->select();

        $payMap = array();
        foreach($total_pays as $count){
            $payMap[$count['branch_id'].''] = $count['total_pay'];
        }
        $hasPayMap = array();
        foreach($total_has_pays as $count){
            $hasPayMap[$count['branch_id'].''] = $count['total_pay'];
        }
        $payCountMap = array();
        foreach($total_pay_counts as $count){
            $payCountMap[$count['branch_id'].''] = $count['total_count'];
        }
        $hasPayCountMap = array();
        foreach($total_has_pay_counts as $count){
            $hasPayCountMap[$count['branch_id'].''] = $count['total_count'];
        }

        $countMap = array();
        foreach($counts as $count){
            $countMap[$count['branch_id'].''] = $count['count'];
        }

        $sjMap = array();
        foreach($shujis as $count){
            $sjMap[$count['branch_id'].''] = $count['shuji_realname'];
        }


        foreach($list as $index=>$item) {
            $branchId = $item['id'];

            $total_pay = $payMap[$branchId.''];
            $item['total_pay'] = $total_pay?intval($total_pay*100)/100:0;
            $has_pay = $hasPayMap[$branchId.''];
            $item['has_pay'] = $has_pay?intval($has_pay*100)/100:0;
            $item['un_pay'] =  $item['total_pay'] - $item['has_pay'];


            $total_count = $payCountMap[$branchId.''];
            $item['total_count'] = $total_count?$total_count:0;
            $has_pay_count = $hasPayCountMap[$branchId.''];
            $item['has_pay_count'] = $has_pay_count?$has_pay_count:0;
            $item['un_pay_count'] =  $item['total_count'] - $item['has_pay_count'];


            $count = $countMap[$branchId.''];
            $item['party_count'] = $count?$count:0;

            $item['realname'] = $sjMap[$branchId.''];
            $list[$index] = $item;
        }
        $this->assign('list', $list);


        //统计信息

        $count = array();
        //珠海总缴费
        $count['total_pay'] = D('PartyFeeUserView')->sum("PayOrder.total_fee");
        $count['total_pay'] =  $count['total_pay']? intval($count['total_pay']*100)/100:0;
        $count['total_has_pay'] = D('PartyFeeUserView')->where(array('PayOrder.status'=>1))->sum("PayOrder.total_fee");
        $count['total_has_pay'] =  $count['total_has_pay']? intval($count['total_has_pay']*100)/100:0;
        $count['total_un_pay'] =  $count['total_pay'] - $count['total_has_pay'];

        $count['total_pay_count'] = D('PartyFeeUserView')->count();
        $count['total_has_pay_count'] = D('PartyFeeUserView')->where(array('PayOrder.status'=>1))->count();
        $count['total_un_pay_count'] =  $count['total_pay_count'] - $count['total_has_pay_count'];


        $this->assign('count',$count);

//        $header_left['url'] = __ROOT__.'/'.I('level');
//        $this->assign('header_left', $header_left);

        $this->setHeader('党费统计');
        $this->setTitle('党费统计');
        $this->display();
    }

    public function costs()
    {
        $nowYear = date('Y',time());
        $year = I('year',$nowYear);
        $branchId = I('id');
        $branchId = $branchId?$branchId:I('state');
        $this->check_wx_redirect('cost_costs',$branchId);//判断是否重定向登录

        $detail = D('PartyBranch')->find($branchId);
//        $list = D('PartyFeeView')->where(array('PartyFeeName.year'=>$year,'branch_id'=>$branchId))->select();
//        foreach($list as $index=>$item){
//            $item['has_pay'] = D('PartyFeeUserView')->where(array('PartyFee.id'=>$item['id'],'PayOrder.status'=>1))->sum("PayOrder.total_fee");
//            $item['un_pay_count'] = D('PartyFeeUserView')->where(array('PartyFee.id'=>$item['id'],'PayOrder.status'=>0))->count();
//            $item['has_pay'] =  $item['has_pay']? $item['has_pay']:0;
//            $list[$index] = $item;
//        }

//        exit(to_json_string($list));
//        $header_left['url'] = __ROOT__.'/Cost/branchs';
//        $this->assign('header_left', $header_left);
//        $this->assign('list', $list);
        $this->assign('year', $year);
        $this->assign('detail', $detail);
        $this->setHeader($detail['name'].'党费统计');
        $this->setTitle($detail['name'].'党费统计');
        $this->display();
    }


    public function ajaxLoadingCost()
    {
        $nowYear = date('Y',time());
        $year = I('year',$nowYear);
        $branchId = I('id');
        $this->check_wx_redirect();//判断是否重定向登录

        $detail = D('PartyBranch')->find($branchId);
        $list = D('PartyFeeView')->where(array('PartyFeeName.year'=>$year,'branch_id'=>$branchId))->select();
        foreach($list as $index=>$item){
            $item['has_pay'] = D('PartyFeeUserView')->where(array('PartyFee.id'=>$item['id'],'PayOrder.status'=>1))->sum("PayOrder.total_fee");
            $item['un_pay_count'] = D('PartyFeeUserView')->where(array('PartyFee.id'=>$item['id'],'PayOrder.status'=>0))->count();
            $item['has_pay'] =  $item['has_pay']? $item['has_pay']:0;
            $list[$index] = $item;
        }
        ajaxMsg(0,'success',array('list'=>$list,'year'=>$year));
    }

    public function lists()
    {
        $this->check_wx_redirect('cost_lists');//判断是否重定向登录
        $list = D('PartyFeeUserView')->where(array('PartyFeeUser.uid'=>uid()))->order("PayOrder.status asc, PartyFeeName.year asc")->select();
        $this->setHeader('党费缴纳');
        $this->setTitle('党费缴纳');
        $this->assign('list',$list);
        $this->display();
    }

    public function detail()
    {
        $id = I('id');
        $id = $id?$id:I('state');
        $this->check_wx_redirect('cost_detail',$id);//判断是否重定向登录
        $detail = D('PartyFeeView')->find($id);
        $branchId = $detail['branch_id'];
        //已付款
        $detail['has_pay'] = D('PartyFeeUserView')->where(array('PartyFee.id'=>$id,'PayOrder.status'=>1))->sum("PayOrder.total_fee");
        $detail['has_pay_count'] = D('PartyFeeUserView')->where(array('PartyFee.id'=>$id,'PayOrder.status'=>1))->count();
        $detail['has_pay'] =  $detail['has_pay']? $detail['has_pay']:0;
        //未付款人数
        $detail['un_pay_count'] = D('PartyFeeUserView')->where(array('PartyFee.id'=>$id,'PayOrder.status'=>0))->count();
        $detail['un_pay'] = D('PartyFeeUserView')->where(array('PartyFee.id'=>$id,'PayOrder.status'=>0))->sum("PayOrder.total_fee");
        $detail['un_pay'] =  $detail['un_pay']? $detail['un_pay']:0;
        //总款
        $detail['total_pay'] =  $detail['un_pay'] + $detail['has_pay'];
        $detail['total_count'] =  $detail['un_pay_count'] + $detail['has_pay_count'];

        $list = D('PartyFeeUserView')->where(array('fee_id'=>$detail['id']))->order("PayOrder.status desc,User.post_id desc")->select();


        $this->assign('list', $list);
        $this->assign('detail', $detail);
        $this->setHeader($detail['branch_name'].$detail['fee_name']);
        $this->setTitle($detail['branch_name'].$detail['fee_name']);
        $this->display();
    }

    //定时触发接口党费自动每年发布，每个季度发布一次。
    public function ajaxChange(){

        $this->check_wx_redirect();//判断是否重定向登录
        //判断当前时间的季度、年份
        $season = ceil((date('n'))/3);
        $year = ceil(date('Y'));
        //本季度最后一天
        $end = date('Y-m-d', mktime(23,59,59,$season*3,date('t',mktime(0, 0 , 0,$season*3,1,date("Y"))),date('Y')));
        $end = date('Y-m-d', strtotime("$end +1 month"));

        $season_name = array('一','二','三','四');

        //检索数据库中 年份 和 季度 是否存在
        $count = D('PartyFee')->where(array("season"=>$season,"year"=>$year))->count();
        if($count > 0){//如果存在，忽略
            ajaxMsg(0,"--");
        }else{ //如果不存在 新建记录
            $branchs = D('PartyBranch')->select();
            $coefficient = 0.08;
            foreach($branchs as $branch){
                //添加支部季度记录
                $fee_id = D('PartyFee')->add(array(
                    'branch_id'=>$branch['id'],
                    'create_time'=>time(),
                    'coefficient'=> $coefficient,
                    'name'=> $year.'年第'.$season_name[$season-1].'季度党费缴纳',
                    'end_date'=>$end,
                    'year'=>$year,
                    'season'=>$season));
                $branch_id = $branch['id'];
                $partId = C('ROLE_PARTY_ID');
                $partys = D('User')->where("status=1 and branch_id=$branch_id and role_id=$partId")->select();
                foreach($partys as $user){

                    $fee = $user['wage']*$coefficient;
                    //每个党员生成一条数据
                    $order = PayUtils::make_order($user['uid'], $fee, $year.'年第'.$season.'季度党费缴纳','党费缴纳', $fee_id);
                    $fee_user_id = D('PartyFeeUser')->add(array(
                        'fee_id'=>$fee_id,
                        'fee'=>$fee,
                        'uid'=>$user['uid'],
                        'create_time'=>time(),
                        'wage'=>$user['wage'],
                        'order_id'=>$order['id']));
                    PayUtils::order_save_resource($order['id'],'COST',$fee_user_id);
                }
            }

            ajaxMsg(0,"--");
        }

    }

}