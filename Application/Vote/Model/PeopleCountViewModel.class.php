<?php
namespace Vote\Model;

use Common\Model\BaseViewModel;

class PeopleCountViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'VoteLog' => array('uid', '_type'=>'LEFT'),
        'VoteItem' => array('id', '_on'=>'VoteItem.id=VoteLog.item_id', '_type'=>'LEFT'),
    );

}
