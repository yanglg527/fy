<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Event\Model;

use Common\Model\BaseViewModel;

class EventRoleViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'EventRole' => array('*', '_type'=>'LEFT'),
        'Role' => array('name'=>'role_name', '_on'=>'EventRole.role_id=Role.id', '_type'=>'LEFT'),
        'Event'=>array(
            '*','title'=>'event_title','_on'=>'EventRole.event_id=Event.id', '_type'=>'LEFT'),
        'EventType'=>array(
            '_on'=>'EventType.id=Event.type_id','name'=>'type_name', '_type'=>'LEFT'
        ),
        // 'PartyBranch' => array('name'=>'branch_name', '_on'=>'Event.branch_id=PartyBranch.id', '_type'=>'LEFT'),
        // 'PartyBranchHq' => array('name'=>'branch_hq_name', '_on'=>'Event.branch_hq_id=PartyBranchHq.id', '_type'=>'LEFT'),
    );


}