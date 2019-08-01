<?php
namespace Admin\Model;

use Common\Model\BaseViewModel;
use Think\Model\RelationModel;
use Think\Model\ViewModel;
use Think\Page;

class AllianceArticleViewModel extends BaseViewModel
{

    public $viewFields = array(
        'AllianceArticle'=>array(
            '*','_type'=>'LEFT'
        ),
        'User'=>array(
            'realname'=>'user_realname',
            '_on'=>'User.uid=AllianceArticle.uid', '_type'=>'LEFT'),
        'PartyBranch'=>array('name'=>'branch_name','_on'=>'AllianceArticle.branch_id=PartyBranch.id', '_type'=>'LEFT'),


    );



}