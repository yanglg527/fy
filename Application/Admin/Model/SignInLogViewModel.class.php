<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Common\Model;

use Common\Model\BaseViewModel;

class SignInLogViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'SignInLog' => array('*', '_type' => 'LEFT'),
        'SignInPrizeLog' => array('point'=>'prize_point', '_on' => 'SignInLog.uid = SignInPrizeLog.uid and date_format(from_unixtime(SignInLog.create_time),"%Y-%m-%d") = date_format(from_unixtime(SignInPrizeLog.create_time),"%Y-%m-%d")', '_type' => 'LEFT'),
        'User' => array('realname'=>'real_name', '_on'=>'User.uid=SignInLog.uid'),
        'Role' => array('name' => 'role_name', '_on' => 'User.role_id=Role.id'),
        'PartyBranch' => array('name' => 'branch_name', '_on' => 'User.branch_id=PartyBranch.id')
    );
}