<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Home\Model;

use Common\Model\BaseViewModel;

class UserJobResumeViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'UserJobResume' => array('*','position'=>'job_position', '_type'=>'LEFT'),
        'User'=>array('realname'=>'user_realname',  '_on'=>'UserJobResume.uid=User.uid','_type'=>'LEFT'),
        'WorkUnit'=>array('name'=>'work_unit_name','_on'=>'WorkUnit.id=UserJobResume.work_unit_id', '_type'=>'LEFT')
    );
}