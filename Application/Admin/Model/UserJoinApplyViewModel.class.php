<?php
/**
 * 用户职务视图模型
 */
namespace Common\Model;

use Common\Model\BaseViewModel;

class UserJoinApplyViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'UserJoinApply' => array('*', 'status'=>'apply_status', 'create_time'=>'apply_time', '_type'=>'LEFT'),
        'User' => array('*','status'=>'user_status', '_on'=>'User.uid=UserJoinApply.uid', '_type'=>'LEFT'),
        'PartyBranch' => array('name'=>'branch_name', '_on'=>'PartyBranch.id=User.branch_id')
    );
}