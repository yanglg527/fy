<?php
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]>
 */
namespace Home\Model;

use Common\Model\BaseViewModel;

class ArticlesViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'Articles' => array('*', '_type'=>'LEFT'),
        'ArticlesTags' => array('tag_id'=>'tag_id','second_tag_id'=>'second_tag_id', '_on'=>'Articles.id=ArticlesTags.article_id', '_type'=>'LEFT'),

    );
}