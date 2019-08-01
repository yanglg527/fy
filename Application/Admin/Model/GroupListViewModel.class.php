<?php
/**
 * 党小组列表
 */
namespace Admin\Model;

use Common\Model\BaseViewModel;

class GroupListViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'PartyGroup' => array('id','name','cover','branch_id', '_type'=>'LEFT'),
        'PartyBranch'=>array('name'=>'branch_name', '_on'=>'PartyGroup.branch_id=PartyBranch.id', '_type'=>'LEFT'),
    );

}
