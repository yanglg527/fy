<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Vote\Model;

use Common\Model\BaseViewModel;

class VoteLogViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'VoteLog' => array('*', '_type'=>'LEFT'),
        'VoteItem' => array('title'=>'item_title', '_on'=>'VoteLog.item_id=VoteItem.id', '_type'=>'LEFT'),
        'Vote' => array('title'=>'vote_title', '_on'=>'VoteItem.vote_id=Vote.id', '_type'=>'LEFT'),
        'PartyBranch' => array('name'=>'branch_name', '_on'=>'Vote.branch_id=PartyBranch.id', '_type'=>'LEFT'),
    );


}