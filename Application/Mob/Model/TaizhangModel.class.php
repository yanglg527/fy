<?php 
namespace Mob\Model;
use Common\Model\BaseRelationModel;
use Common\Model\BaseViewModel;
use Think\Model\ViewModel;

/**
 * Class TaizhangModel
 * @package Mob\Model
 */
class TaizhangModel extends BaseRelationModel{
	protected $_link = array(
		'contents' => array(
			'mapping_type' => self::HAS_MANY,
			'foreign_key' => 'taizhang_id', //关联外键
			'class_name' => 'taizhang_contents',  //中间表的表名(多对多关系中必须指定)
			// 'mapping_name' => 'tag_id', //作为数据字段
			// 'class_name' => 'ArticleTag', //模型名
			'mapping_name' => 'contents', //作为数据字段
			'condition' => "status='0'",
		),

	);
}
