<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Mob\Model;

use Common\Model\BaseViewModel;

class DonationLogViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'DonationLog' => array('*', '_type' => 'LEFT'),
        'Donation' => array('title'=>'donation_name', '_on'=>'DonationLog.donation_id=Donation.id', '_type' => 'LEFT'),
        'User' => array('realname'=>'user_realname', 'headimgurl'=>'user_headimgurl', '_on'=>'DonationLog.uid=User.uid', '_type' => 'LEFT'),
        'PayOrder' => array('status'=>'order_status', '_on'=>'DonationLog.order_id=PayOrder.id', '_type' => 'LEFT'),
    );
}