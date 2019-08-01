<?php 
/**d
 * @author :黄药师 <[46914685@qq.com]> 
 */
namespace Admin\Model;
use Common\Model\BaseViewModel;

class PartyOrganizationMsSjViewModel extends BaseViewModel{
	protected $viewFields = array(
		'PartyOrganization' => array('*','_type'=>'LEFT'),
        'PartyOrganizationMs'=>array('uid'=>'ms_uid','_on'=>'PartyOrganization.id=PartyOrganizationMs.organization_id', '_type'=>'LEFT'),
        'PartyOrganizationSj'=>array('uid'=>'sj_uid','_on'=>'PartyOrganization.id=PartyOrganizationSj.organization_id', '_type'=>'LEFT'),
    );
}