<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-01-09
 * Time: 10:39
 */

namespace Weixin\Util;


use Todo\Util\TodoUtils;

class PayUtils
{
    /**
     * 1、生成订单，如：返回中order 含有id和唯一标识out_trade_no
     * $order = make_order($uid, $total_fee='0', $body='订单描述', $title="订单标题");
     *
     * 2、生成需求订单，订单中的 order_id = order['id']  对应指向 参考 party_fee_user
     * $party_fee_user = array(
    'order_id'=>$order['id'],
    'fee'..........
     * )
     * D('PartyFeeUser')->add($party_fee_user);
     *
     * 3、支付连接 参考/Home/View/Cost/lists.html 如:href="__ROOT__/Weixin/Weixinpay/pay?state={$item.out_trade_no}"
     * 生成上面的数据后直接跳转链接  __ROOT__/Weixin/Weixinpay/pay?state={$item.out_trade_no} 就可以了
     *
     * 4、支付成功后PayOrder 下字段 status变为1
     * 通过视图模型可以查看支付状态，参考 Home/Model/PartyFeeUserViewModel.class.php
     *  'PayOrder' => array('out_trade_no','status'=>'order_status', '_on'=>'PayOrder.id=PartyFeeUser.order_id', '_type'=>'LEFT'),
     *
     * 注意：请不要直接修改订单价格，修改价格时make_order创建新的订单，保存新的order_id就可以了，参考Admin/Controller/CostController下的ajaxSaveInfoUser
     *
     * 创建订单
     * @param $uid 创建订单用户的uid
     * @param string $total_fee 订单金额(最多小数点后两位)
     * @param string $body 订单描述
     * @param string $title 订单标题
     * @param int $product_id 可以忽略 不用填写
     * $type COST|党费  DONATION|捐款
     * @return array(
     * 'id'=>id
    'out_trade_no'=>$out_trade_no,
    'total_fee'=>$total_fee,
    'status'=>0,
    'create_time'=>time(),
    'body'=>$body,
    'uid'=>$uid,
    'title'=>$title,
    'product_id'=>$product_id,
    )
     */
    static function make_order($uid, $total_fee='0', $body='订单描述', $title="订单标题" ,$product_id=1,$anonymous)
    {
        $total_fee = intval($total_fee*100);
        $total_fee = $total_fee/100;
        if($total_fee <= 0){
            return false;
        }
        $out_trade_no = PayUtils::getMillisecond().''.$product_id. ''.rand(1000, 9000). ''.rand(10, 99);
        $order = array(
            'out_trade_no'=>$out_trade_no,
            'total_fee'=>$total_fee,
            'status'=>0,
            'create_time'=>time(),
            'body'=>$body,
            'uid'=>$uid,
            'title'=>$title,
            'product_id'=>$product_id,
            'anonymous'=>$anonymous,
        );
        $id = D('PayOrder')->add(
            $order
        );
        $order['id'] = $id;
        return $order;
    }
    static function order_save_resource($order_id, $type='',$resource_id)
    {
        D('PayOrder')->where(array('id'=>$order_id))->save(
            array('type'=>$type,'resource_id'=>$resource_id)
        );
    }
    static function finish_order($out_trade_no,$transaction_id){
        D('PayOrder')->where("out_trade_no=$out_trade_no")->save(array("status" => 1,'pay_time'=>time(),'transaction_id'=>$transaction_id));
        $order = D('PayOrder')->where("out_trade_no=$out_trade_no")->find();
        if($order['type'] == 'COST'){
            TodoUtils::finish($order['resource_id'],'COST');
        }
    }
    static function getMillisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }
}