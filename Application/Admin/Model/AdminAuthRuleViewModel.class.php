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
class AdminAuthRuleViewModel extends BaseViewModel{
	public $viewFields=array(		
		'AdminAuthRule'=>array('id','name','title','type','condition'=>'term','status','mid'),
		//condition必须别名,否则出错
		'AdminModules'=>array('moduleName'=>'modulename','_on'=>'AdminAuthRule.mid=AdminModules.id')
		);
}