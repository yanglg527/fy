<?php 
/**d
 * @author :黄药师 <[46914685@qq.com]> 
 */
namespace Admin\Model;
use Common\Model\BaseViewModel;

class PartyOrganizationUserViewModel extends BaseViewModel{
	protected $viewFields = array(
		'User' => array('*','headimgurl','_type'=>'LEFT'),
        'PartyOrganizationAdm'=>array('uid'=>'adm_uid','_on'=>'User.uid=PartyOrganizationAdm.organization_uid', '_type'=>'LEFT'),
        'User '=>array("_as"=>'UserAdm','realname'=>'adm_realname','_on'=>'PartyOrganizationAdm.uid=UserAdm.uid', '_type'=>'LEFT'),
    );
}