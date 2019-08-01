<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Vote\Model;

use Common\Model\BaseViewModel;

class VoteRoleViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'VoteRole' => array('*', '_type'=>'LEFT'),
        'Role' => array('name'=>'role_name', '_on'=>'VoteRole.role_id=Role.id', '_type'=>'LEFT'),
        'Vote' => array('*', '_on'=>'VoteRole.vote_id=Vote.id', '_type'=>'LEFT'),
        'PartyBranch' => array('name'=>'branch_name', '_on'=>'Vote.branch_id=PartyBranch.id', '_type'=>'LEFT'),
        'PartyBranchHq' => array('name'=>'branch_hq_name', '_on'=>'Vote.branch_hq_id=PartyBranchHq.id', '_type'=>'LEFT'),
        // 'VoteLimitBranch' => array('branch_id'=>'limit_branch_id', '_on'=>'VoteRole.vote_id=VoteLimitBranch.vote_id', '_type'=>'LEFT'),
        'User' => array('realname'=>'publish_name', '_on'=>'Vote.uid=User.uid', '_type'=>'LEFT'),
    );


}
