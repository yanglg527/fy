<?php
namespace Admin\Model;
use Common\Model\BaseRelationModel;

/**
 * 支部关联模型
 * Class PartyBranchRelationModel
 * @package Admin\Model
 */
class PartyBranchRelationModel extends BaseRelationModel{
//    protected $_link = array(
//        'tags' => array(
//            'mapping_type' => self::MANY_TO_MANY,
//            'foreign_key' => 'article_id', //关联外键
//            'relation_foreign_key' =>  'tag_id',   //关联表(副表)在中间表中的字段名称(外键)
//            'relation_table' => 'articles_tags',  //中间表的表名(多对多关系中必须指定)
//            'mapping_name' => 'tags', //作为数据字段
//            // 'class_name' => 'ArticleTag', //模型名
//        ),
//
//    );
	protected $_link = array(
       'user' => array(
           'mapping_type'  => self::HAS_MANY,
           'class_name'    => 'User',
           'foreign_key'   => 'branch_id',
           'mapping_name'  => 'users',
       ),

   );

}
