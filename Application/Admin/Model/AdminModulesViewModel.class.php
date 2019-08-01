<?php 
/**
 * 规则表:auth_rule,模块表:modules
 * 视图模型
 * 外键:auth_rule.mid=modules.id
 *
 */
namespace Admin\Model;
use Common\Model\BaseViewModel;
use Think\Model\ViewModel;
class AdminModulesViewModel extends BaseViewModel{
	public $viewFields=array(		
		'AdminModules'=>array('*'),
		);
}