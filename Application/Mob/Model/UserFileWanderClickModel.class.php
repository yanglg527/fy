<?php

namespace Mob\Model;

use Common\Model\BaseViewModel;

class UserFileWanderClickModel extends BaseViewModel
{
    protected $viewFields = array(
        'UserFileWanderClick' => array('*', '_type'=>'LEFT'),
        'User' => array('realname'=>'realname','headimgurl'=>'headimgurl','_on'=>'UserFileWanderClick.uid=User.uid', '_type'=>'LEFT'),
    );
}