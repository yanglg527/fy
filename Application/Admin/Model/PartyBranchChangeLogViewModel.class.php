<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Common\Model;

use Common\Model\BaseViewModel;

class PartyBranchChangeLogViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'PartyBranchChangeLog' => array('*', '_type'=>'LEFT'),
        'PartyBranch' => array('name'=>'branch_name', '_on'=>'PartyBranch.id=PartyBranchChangeLog.branch_id', '_type'=>'LEFT'),
        'PartyBranchHq' => array('name'=>'hq_name', '_on'=>'PartyBranchHq.id=PartyBranchChangeLog.branch_hq_id', '_type'=>'LEFT'),
    );
}