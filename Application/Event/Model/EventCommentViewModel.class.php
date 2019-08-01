<?php
namespace Event\Model;

use Common\Model\BaseViewModel;
use Think\Model\RelationModel;
use Think\Model\ViewModel;
use Think\Page;

class EventCommentViewModel extends BaseViewModel
{

    public $viewFields = array(
        'EventComment'=>array(
            '*', '_type'=>'LEFT'
        ),
        'User'=>array(
            '*',
            '_on'=>'User.uid=EventComment.uid'),


    );



}