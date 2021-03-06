<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Admin\Model;

use Common\Model\BaseViewModel;

class PartyOrganizationBranchsViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'PartyOrganizationBranchs' => array('*', '_type'=>'LEFT'),
        'PartyOrganization' => array('name'=>'name', '_on'=>'PartyOrganization.id=PartyOrganizationBranchs.organization_id', '_type'=>'LEFT'),
        'PartyBranch' => array('name'=>'branch_name', '_on'=>'PartyBranch.id=PartyOrganizationBranchs.branch_id', '_type'=>'LEFT'),
    );

}