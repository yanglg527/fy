<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Admin\Model;

use Common\Model\BaseViewModel;

class PartyBranchMembersViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'PartyBranchMembers' => array('*', '_type'=>'LEFT'),
        'PartyBranch'=>array('name'=>'branch_name','sort'=>'branch_sort', '_on'=>'PartyBranchMembers.branch_id=PartyBranch.id', '_type'=>'LEFT'),
        'PartyPost'=>array('name'=>'post_name','sort'=>'post_sort', '_on'=>'PartyPost.status_id=PartyBranchMembers.status', '_type'=>'LEFT'),
    );

}
