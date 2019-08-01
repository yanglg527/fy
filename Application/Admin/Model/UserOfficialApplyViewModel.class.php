<?php
/**
 * 用户职务视图模型
 */
namespace Common\Model;

use Common\Model\BaseViewModel;

class UserOfficialApplyViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'UserOfficialApply' => array('*', 'status'=>'apply_status', '_type'=>'LEFT'),
        'User' => array('*', '_on'=>'User.uid=UserOfficialApply.uid', '_type'=>'LEFT'),
        'PartyBranch' => array('name'=>'branch_name', '_on'=>'PartyBranch.id=User.branch_id')
    );
}