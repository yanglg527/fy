<?php 
namespace Admin\Model;
use Common\Model\BaseRelationModel;
use Common\Model\BaseViewModel;
use Think\Model\ViewModel;
use Think\Page;

/**
 * 课程
 * Class ArticlesViewModel
 * @package Admin\Model
 */
class ArticlesModel extends BaseRelationModel{
	protected $_link = array(
		'tags' => array(
			'mapping_type' => self::MANY_TO_MANY,
			'foreign_key' => 'article_id', //关联外键
			'relation_foreign_key' =>  'tag_id',   //关联表(副表)在中间表中的字段名称(外键)
			'relation_table' => 'articles_tags',  //中间表的表名(多对多关系中必须指定)
			'mapping_name' => 'tags', //作为数据字段
			// 'class_name' => 'ArticleTag', //模型名
			
		),
		'user'=>array(
            'mapping_type' => self::HAS_ONE,
            'class_name' => 'user',
            'mapping_key' =>'uid',
            'foreign_key' => 'uid',
            'as_fields'=>'realname',

            ),
	);
}
