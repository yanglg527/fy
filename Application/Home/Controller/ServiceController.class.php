<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Home\Controller;
use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;
use Weixin\Util\PayUtils;

class ServiceController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
        $this->setHeader('党员服务');
        $this->setTitle('党员服务');
    }

    public function index()
    {

        $header_left['url'] = __ROOT__."/Home/Index/index";
        $this->assign('header_left', $header_left);
        $this->check_wx_redirect("service_index");
        $this->setHeader('党员服务');
        $this->setTitle('党员服务');
        $this->display();
    }

    public function donation(){
        $this->check_wx_redirect("service_donation");
        $this->setHeader('扶贫捐款');
        $this->setTitle('扶贫捐款');
        $this->display();
    }

    public function donationPay(){
        $id = I('id');
        $id = $id?$id:I('state');
        $this->check_wx_redirect('service_donationPay',$id);//判断是否重定向登录
        $detail = D('Donation')->find($id);
        $this->assign('detail', $detail);
        $this->setHeader('扶贫捐款');
        $this->setTitle('扶贫捐款');
        $this->display();
    }

    public function ajaxDonation(){
        $id = I('id');
        $this->check_wx_redirect();//判断是否重定向登录
        $remark = I('remark');
        $fee = I('fee');
        $detail = D('Donation')->find($id);

        $fee = intval($fee*100);
        $fee = $fee/100;
        if($fee <= 0){
            ajaxMsg(1,"请先填写你的捐款金额");
        }
        $total = D('DonationLogView')->where(array('DonationLog.donation_id'=>$id,'PayOrder.status'=>1))->sum('DonationLog.fee');
        $total = $total?$total:0;
        if($total == $detail['target']){
            ajaxMsg(1,"已达到目标捐款金额了");
        }
        if($fee + $total > $detail['target']){
            ajaxMsg(1,"你的捐款金额已超出目标捐款金额了");
        }

        if($detail['status'] == 1){
            $order = PayUtils::make_order(uid(),$fee,$detail['title'],"扶贫捐款");
            if($order){
                $array = array(
                    'donation_id'=> $detail['id'],
                    'order_id'=>$order['id'],
                    'uid'=>uid(),
                    'create_time'=>time(),
                    'remark'=>$remark,
                    'fee'=>$fee
                );
                $id = D('DonationLog')->add($array);
                PayUtils::order_save_resource($order['id'],'DONATION',$id);
                ajaxMsg(0,"",array('out_trade_no'=>$order['out_trade_no']));
            }else{
                ajaxMsg(1,"生成订单失败");
            }

        }else{
            ajaxMsg(1,"抱歉，改募捐项目已经关闭收款了");
        }
    }



    public function ajaxLoadingDonation(){
        // 获取分页参数(上一次的最后一条数据的排序字段)
        $published_at=I('post.published_at');
        if ($published_at == 0 || $published_at == null) {
            $published_at=time();
        }

        $where = array('Donation.status'=>array('gt', 0), 'Donation.create_time'=>array('lt', $published_at));
        $page = D('DonationView')->findPage($where, 15, 'Donation.create_time desc');

        ajaxMsg(0,to_json_string($page['list']),$page['list']);
    }

    public function donationDetail(){
        $id = I('get.id');
        $id = $id?$id:I('state');
        $this->check_wx_redirect('service_donationDetail',$id);//判断是否重定向登录
        $donation = D('DonationView')->where("Donation.id=$id")->find();

        $real_money = D('DonationLogView')->where(array('DonationLog.donation_id'=>$id,'PayOrder.status'=>1))->sum('DonationLog.fee');
        $help_count = D('DonationLogView')->where(array('DonationLog.donation_id'=>$id,'PayOrder.status'=>1))->count();
        $real_money = $real_money?$real_money:0;
        $donation['real_money'] = $real_money;
        $donation['help_count'] = $help_count;

        $this->assign('detail', $donation);
        if($donation['target'] > 0){
            $progress = $donation['real_money'] / $donation['target'] * 100 ;
        }else{
            $progress = 100;
        }

        $this->assign('width', $progress."%");

        $this->setHeader('扶贫捐款');
        $this->setTitle('扶贫捐款');
        $this->display();
    }

    public function ajaxLoadingDonationLog(){
        // 获取分页参数(上一次的最后一条数据的排序字段)
        $published_at=I('post.create_time');
        if ($published_at == 0 || $published_at == null) {
            $published_at=time();
        }
        $donationId = I('danationId');

        $where = array('DonationLog.donation_id'=>$donationId, 'DonationLog.create_time'=>array('lt', $published_at));
        $page = D('DonationLogView')->findPage($where, 15, 'DonationLog.create_time desc');

        ajaxMsg(0,to_json_string($page['list']),$page['list']);
    }
}