<?php
namespace Weixin\Controller;

use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;
use Think\Controller;
use Weixin\Util\PayUtils;
use Weixin\Util\QYConfig;

/**
 * 微信支付
 */
class WeixinpayTestController extends BaseController
{

    /**
     * //支付界面
     * 微信 公众号jssdk支付
     */
    public function weixinpay_js()
    {
        // 此处根据实际业务情况生成订单 然后拿着订单去支付

        // 用时间戳虚拟一个订单号  （请根据实际业务更改）
        $out_trade_no = time();

        // 组合url
        $url = U('Weixin/Weixinpay/pay', array('out_trade_no' => $out_trade_no));
        // 前往支付
        redirect($url);//跳转出现弹窗支付
    }

    /**
     *
     * notify_url接收页面
     *
     */
    public function notify()
    {
        // 导入微信支付sdk
        Vendor('Weixinpay.Weixinpay');
        $wxpay = new \Weixinpay();
        $result = $wxpay->notify();
        if ($result) {
            // 验证成功 修改数据库的订单状态等 $result['out_trade_no']为订单号
            $out_trade_no = $result['out_trade_no'];
            $transaction_id = $result['transaction_id'];
            PayUtils::finish_order($out_trade_no, $transaction_id);
        }
    }

    /**
     *
     * 公众号支付 必须以get形式传递 out_trade_no 参数
     * 示例请看 /Application/Home/Controller/IndexController.class.php
     * 中的weixinpay_js方法
     *
     */
    public function pay()
    {
        // 导入微信支付sdk
        Vendor('Weixinpay.Weixinpay');
        $wxpay = new \Weixinpay();
        // 获取jssdk需要用到的数据

        //获取数据库数据
        $order = D('PayOrder')->where(array("out_trade_no" => I('get.state')))->find();
        //放入下面方法
        if ($order) {
            $user = user();
            $config = C('WEIXINQY_CONFIG');
            $openid =  cover_userid_to_openid(  $config['CORP_ID'],   $config['SECRET'], $user['qyuserid']);
            $data = $wxpay->getParameters($order['total_fee'] * 100, $order['body'], $order['out_trade_no'], $order['product_id'], $openid['openid']);
            // 将数据分配到前台页面
            $assign = array(
                'data' => json_encode($data),
                'order'=> $order
            );
            $this->assign($assign);
        }

        /***测试*/
//        $assign = array(
//            'order'=> $order
//        );
//        $this->assign($assign);
        /***测试*/
        $this->setHeader('微信支付');
        $this->setTitle('微信支付');
        $this->display();
    }

}