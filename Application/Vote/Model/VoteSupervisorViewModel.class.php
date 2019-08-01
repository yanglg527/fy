<?php
namespace Vote\Model;

use Common\Model\BaseViewModel;

class VoteSupervisorViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'VoteSupervisor' => array('uid', '_type'=>'LEFT'),
        'User' => array('realname'=>'supervisorName', '_on'=>'VoteSupervisor.uid=User.uid', '_type'=>'LEFT'),
    );
}
