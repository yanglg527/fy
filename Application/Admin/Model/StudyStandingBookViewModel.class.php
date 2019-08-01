<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Common\Model;

use Common\Model\BaseViewModel;

class StudyStandingBookViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'StudyStandingBook' => array('*', '_type' => 'LEFT'),
        'PartyBranch' => array('name'=>'branch_name', '_on'=>'StudyStandingBook.branch_id=PartyBranch.id', '_type' => 'LEFT'),
        'User' => array('realname'=>'user_realname', '_on'=>'StudyStandingBook.uid=User.uid', '_type' => 'LEFT'),
        'PartyOrganization' => array('name'=>'organization_name', '_on'=>'StudyStandingBook.organization_id=PartyOrganization.id', '_type' => 'LEFT'),
    );
}