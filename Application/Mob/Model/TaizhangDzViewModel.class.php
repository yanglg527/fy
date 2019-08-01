<?php 
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]> 
 */
namespace Mob\Model;
use Common\Model\BaseViewModel;

class TaizhangDzViewModel extends BaseViewModel{
	protected $viewFields = array(
		'TaizhangDz' => array('id'=>'tdz_id','_type'=>'LEFT'),
        'Taizhang'=>array('*','_on'=>'Taizhang.id=TaizhangDz.taizhang_id','_type'=>'LEFT'),
		'User'=>array('realname'=>'user_realname','headimgurl'=>'user_headimgurl','_on'=>'User.uid=Taizhang.uid','_type'=>'LEFT'),
        'AdmPost'=>array('name'=>'user_adm_post_name','_on'=>'User.adm_id=AdmPost.id','_type'=>'LEFT'),
        'TaizhangTags'=>array('title'=>'tag_name','_on'=>'Taizhang.tag_id=TaizhangTags.id','_type'=>'LEFT'),
        'PartyOrganization'=>array('name'=>'organization_name','_on'=>'User.organization_id=PartyOrganization.id','_type'=>'LEFT')
	);
}