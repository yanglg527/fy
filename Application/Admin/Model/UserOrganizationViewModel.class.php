<?php 
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]> 
 */
namespace Admin\Model;
use Common\Model\BaseViewModel;

class UserOrganizationViewModel extends BaseViewModel{
	protected $viewFields = array(
		'User' => array('*','headimgurl','_type'=>'LEFT'),
		'PartyBranch'=>array('name'=>'branch_name','_on'=>'User.admin_branch_id=PartyBranch.id', '_type'=>'LEFT'),
        'PartyOrganizationSj'=>array('uid'=>'sj_uid','organization_id'=>'sj_organization_id','_on'=>'User.uid=PartyOrganizationSj.uid', '_type'=>'LEFT'),
        'PartyOrganizationMs'=>array('uid'=>'ms_uid','organization_id'=>'ms_organization_id','_on'=>'User.uid=PartyOrganizationMs.uid', '_type'=>'LEFT'),
        'PartyOrganizationAdm'=>array('uid'=>'adm_uid','organization_uid'=>'adm_organization_uid','_on'=>'User.uid=PartyOrganizationAdm.uid', '_type'=>'LEFT'),
        'PartyOrganizationAdm '=>array('_as'=>'PartyOrganizationAdmS','uid'=>'sj_adm_uid','organization_uid'=>'sj_adm_organization_uid','_on'=>'User.uid=PartyOrganizationAdmS.organization_uid', '_type'=>'LEFT'),
        'PartyOrganization   '=>array("_as"=>'PartyOrganization','name'=>'organization_name','_on'=>'User.organization_id=PartyOrganization.id', '_type'=>'LEFT'),
        'PartyOrganization'=>array("_as"=>'PartyOrganizationM','name'=>'ms_organization_name','_on'=>'PartyOrganizationMs.organization_id=PartyOrganizationM.id', '_type'=>'LEFT'),
        'PartyOrganization '=>array("_as"=>'PartyOrganizationS','name'=>'sj_organization_name','_on'=>'PartyOrganizationSj.organization_id=PartyOrganizationS.id', '_type'=>'LEFT'),
        'PartyOrganization  '=>array("_as"=>'PartyOrganizationA','name'=>'adm_organization_name','_on'=>'PartyOrganizationAdm.organization_id=PartyOrganizationA.id', '_type'=>'LEFT'),
        'User '=>array("_as"=>'UserO','realname'=>'adm_organization_realname','_on'=>'PartyOrganizationAdm.organization_uid=UserO.uid', '_type'=>'LEFT'),
        'User  '=>array("_as"=>'User1','realname'=>'sj_adm_realname','_on'=>'PartyOrganizationAdmS.uid=User1.uid', '_type'=>'LEFT'),
    );
}