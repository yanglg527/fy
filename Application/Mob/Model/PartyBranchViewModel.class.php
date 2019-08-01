<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Mob\Model;

use Common\Model\BaseViewModel;

class PartyBranchViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'PartyBranch' => array('*', '_type'=>'LEFT'),
        'User' => array('*', '_on'=>'PartyBranch.id=User.branch_id', '_type'=>'LEFT'),
        'Post' => array('name'=>'post_name', '_on'=>'User.post_id=Post.id', '_type'=>'LEFT'),

    );
}