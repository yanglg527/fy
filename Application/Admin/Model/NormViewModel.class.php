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
class NormViewModel extends BaseViewModel{
	
    protected $viewFields = array(
        'Norm' => array('*', '_type'=>'LEFT'),
        
    );
}