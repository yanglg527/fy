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
class MissionViewModel extends BaseViewModel{
	protected $viewFields = array(
		'Mission' => array('*', '_type'=>'LEFT'),
	);
}
