<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Home\Model;

use Common\Model\BaseViewModel;

class PartyFeeUserViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'PartyFeeUser' => array('*', '_type'=>'LEFT'),
        'PartyFee' => array('end_date'=>'fee_end_date', '_on'=>'PartyFeeUser.fee_id=PartyFee.id', '_type'=>'LEFT'),
        'PartyFeeName' => array('year'=>'fee_year','name'=>'fee_name', '_on'=>'PartyFee.fee_name_id=PartyFeeName.id', '_type'=>'LEFT'),
        'PayOrder' => array('out_trade_no','status'=>'order_status', '_on'=>'PayOrder.id=PartyFeeUser.order_id', '_type'=>'LEFT'),
        'User'=>array(
            'post_id'=>'user_post_id',
            'headimgurl'=>'user_headimgurl',
            'realname'=>'user_realname',
            'branch_hq_id'=>'user_branch_hq_id',
            '_on'=>'PartyFeeUser.uid=User.uid','_type'=>'LEFT'),
        'Post'=>array('name'=>'post_name','_on'=>'User.post_id=Post.id', '_type'=>'LEFT')

    );
}