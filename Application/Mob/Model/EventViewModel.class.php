<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Mob\Model;

use Common\Model\BaseViewModel;

class EventViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'Event'=> array('*', '_type'=>'LEFT'),
        'EventType'=>array(
            '_on'=>'EventType.id=Event.type_id', '_type'=>'LEFT'
        ),
        'User'=>array('realname'=>'user_realname','headimgurl'=>'user_headimgurl','_on'=>'Event.uid=User.uid', '_type'=>'LEFT'),
    );


}