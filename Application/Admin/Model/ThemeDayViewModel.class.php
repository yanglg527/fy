<?php
/**
 * 主题党日 - 列表视图模型
 * @author Calvin
 */
namespace Admin\Model;

use Common\Model\BaseViewModel;

class ThemeDayViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'ThemePartyDay' => array(
            'id','theme', 'status',
            'uid','branch_id',
            'start_time', 'end_time',
            'create_at', '_type'=>'LEFT'),

        'User' => array('realname'=>'sendname',
            'organization_id',
            '_on'=>'ThemePartyDay.uid=User.uid','_type'=>'LEFT'),

        'PartyBranch' => array('name'=>'branch_name',
            '_on'=>'ThemePartyDay.branch_id=PartyBranch.id', '_type'=>'LEFT'),

		'PartyOrganization'=>array('name'=>'organization_name',
            '_on'=>'User.organization_id=PartyOrganization.id','_type'=>'LEFT'),
    );
}
