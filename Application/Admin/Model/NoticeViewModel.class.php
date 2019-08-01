<?php 
namespace Admin\Model;
use Common\Model\BaseRelationModel;
use Common\Model\BaseViewModel;
use Think\Model\ViewModel;
use Think\Page;

/**
 * 课程
 * Class NoticeViewModel
 * @package Admin\Model
 */
class NoticeViewModel extends BaseViewModel{
	protected $viewFields = array(
		'Notice' => array('*', '_type'=>'LEFT'),
		'NoticeType' => array('name'=>'type_name', '_on'=>'Notice.type_id=NoticeType.id', '_type'=>'LEFT'),
	);
}
