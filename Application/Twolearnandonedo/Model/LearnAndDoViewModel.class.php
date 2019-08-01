<?php
/**
 * 两学一做 视图模型
 * @author Calvin
 */
namespace Twolearnandonedo\Model;

use Common\Model\BaseViewModel;

class LearnAndDoViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'TwoStudyOneDo' => array('id', 'branch_id', 'type', 'uid', 'create_at', 'pageviews', 'theme', '_type'=>'LEFT'),
        'User' => array('realname', 'branch_id', '_on'=>'TwoStudyOneDo.uid=User.uid', '_type'=>'LEFT'),
        'PartyBranch' => array('name'=>'branch_name', '_on'=>'User.branch_id=PartyBranch.id', '_type'=>'LEFT'),
    );
}
