<?php
/**
 * 两学一做视图模型
 * @author Calvin
 */
namespace Admin\Model;

use Common\Model\BaseViewModel;

class OneDoViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'TwoStudyOneDo' => array('id','theme', 'status','type','uid','branch_id','create_at', '_type'=>'LEFT'),
        'User' => array('realname'=>'user_realname', 'organization_id', '_on'=>'TwoStudyOneDo.uid=User.uid', '_type'=>'LEFT'),
        'PartyBranch' => array('name'=>'branch_name', '_on'=>'TwoStudyOneDo.branch_id=PartyBranch.id', '_type'=>'LEFT'),
		'PartyOrganization'=>array('name'=>'organization_name','_on'=>'User.organization_id=PartyOrganization.id','_type'=>'LEFT'),
    );

}
