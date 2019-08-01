<?php

namespace Mob\Model;

use Common\Model\BaseViewModel;

class ArticlesViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'Articles' => array('*', '_type'=>'LEFT'),
        'ArticlesTags' => array('tag_id'=>'tag_id', '_on'=>'Articles.id=ArticlesTags.article_id', '_type'=>'LEFT'),
      
    );
}