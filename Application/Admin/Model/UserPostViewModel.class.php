<?php
/**
 * 用户职务视图模型
 */
namespace Common\Model;

use Common\Model\BaseViewModel;

class UserPostViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'User' => array('*', '_type'=>'LEFT'),
        'Post' => array('name'=>'post_name', 'type'=>'post_type', 'code'=>'post_code', '_on'=>'User.post_id=Post.id'),
    );
}