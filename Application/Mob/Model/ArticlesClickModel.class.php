<?php

namespace Mob\Model;

use Common\Model\BaseViewModel;

class ArticlesClickModel extends BaseViewModel
{
    protected $viewFields = array(
        'Articles' => array('*', '_type'=>'LEFT'),
        'ArticleClickLog' => array('score'=>'score','create_time'=>'create_time','uid'=>'uid', '_on'=>'Articles.id=ArticleClickLog.article_id', '_type'=>'LEFT'),
    );
}