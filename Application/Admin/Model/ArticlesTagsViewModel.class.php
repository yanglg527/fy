<?php 
namespace Admin\Model;
use Common\Model\BaseViewModel;
use Think\Model\ViewModel;
use Think\Page;

/**
 * 课程
 * Class ArticleTagViewModel
 * @package Admin\Model
 */
class ArticlesTagsViewModel extends BaseViewModel{
		protected $viewFields = array(
			'ArticlesTags' => array('*'),
			//收藏人信息
			'Tags'=>array('name'=>'tag_name', '_on'=>'ArticlesTags.tag_id=Tags.id','_type'=>'LEFT'),
		);

}