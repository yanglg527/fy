<?php
namespace Admin\Model;


use Common\Model\BaseViewModel;

class UserSpaceViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'UserSpace' => array('*', '_type'=>'LEFT'),
        'User' => array('realname'=>'user_realname', '_on'=>'User.uid=UserSpace.uid'),
    );
}