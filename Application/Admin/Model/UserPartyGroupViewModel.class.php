<?php 
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]> 
 */
namespace Admin\Model;
use Common\Model\BaseViewModel;

class UserPartyGroupViewModel extends BaseViewModel{
	protected $viewFields = array(
		'User' => array('*','headimgurl','_type'=>'LEFT'),
        'PartyGroup'=>array("_as"=>'PartyGroup','name'=>'party_group_name','_on'=>'User.party_group_id=PartyGroup.id', '_type'=>'LEFT'),
        'PartyGroupGl'=>array("_as"=>'PartyGroupGL','id'=>'party_group_gl_id','_on'=>'PartyGroupGL.uid=User.uid', '_type'=>'LEFT'),
        
    );
}
