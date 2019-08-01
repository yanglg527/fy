<?php 
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]> 
 */
namespace Mob\Model;
use Common\Model\BaseViewModel;

class PartyOrganizationViewModel extends BaseViewModel{
	protected $viewFields = array(
		'PartyOrganization' => array('*','_type'=>'LEFT'),
		'PartyOrganizationSj' => array('uid'=>'sj_uid', '_on'=>'PartyOrganizationSj.organization_id=PartyOrganization.id', '_type'=>'LEFT'),
        'User'=>array('headimgurl'=>'sj_headimgurl','realname'=>'sj_realname','_on'=>'User.uid=PartyOrganizationSj.uid','_type'=>'LEFT'),
	);
}