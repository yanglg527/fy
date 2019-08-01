<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Mob\Model;

use Common\Model\BaseViewModel;

class TemporaryBranchMemberViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'TemporaryBranchMember' => array('*', 'branch_id', '_type'=>'LEFT'),
        'TemporaryBranch' => array('branch_name', 'create_time', 'member_count', 'uid'=>'creater_uid', '_on'=>'TemporaryBranch.id=TemporaryBranchMember.branch_id','_type'=>'LEFT'),
        'User'=>array('realname'=>'creater_name', 'headimgurl', '_on'=>'User.uid=TemporaryBranch.uid','_type'=>'LEFT')
    );
}