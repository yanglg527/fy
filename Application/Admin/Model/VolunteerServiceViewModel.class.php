<?php
/**
 * 志愿服务 - 列表视图模型
 * @author Calvin
 */
namespace Admin\Model;

use Common\Model\BaseViewModel;

class VolunteerServiceViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'VolunteerService' => array( '*', '_type'=>'LEFT'),
        'User' => array('realname'=>'sendname', 'branch_id','organization_id',
            '_on'=>'VolunteerService.uid=User.uid','_type'=>'LEFT'),

        'PartyBranch' => array('name'=>'branch_name',
            '_on'=>'User.branch_id=PartyBranch.id', '_type'=>'LEFT'),

		     'PartyOrganization'=>array('name'=>'organization_name',
            '_on'=>'User.organization_id=PartyOrganization.id','_type'=>'LEFT'),
    );
}
