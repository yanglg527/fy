<?php 
/**d
 * @author :黄药师 <[46914685@qq.com]> 
 */
namespace Admin\Model;
use Common\Model\BaseViewModel;

class PartyBranchAdmViewModel extends BaseViewModel{
	protected $viewFields = array(
        'PartyBranchAdm'=>array('id','uid'=>'adm_uid', '_type'=>'LEFT'),
		'User' => array('*','headimgurl','_on'=>'User.uid=PartyBranchAdm.uid','_type'=>'LEFT'),
        'PartyBranch'=>array('name'=>'branch_name','_on'=>'PartyBranch.id=PartyBranchAdm.branch_id', '_type'=>'LEFT'),
    );
}