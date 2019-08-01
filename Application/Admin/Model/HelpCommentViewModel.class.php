<?php 
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]> 
 */
namespace Admin\Model;
use Common\Model\BaseViewModel;

class HelpCommentViewModel extends BaseViewModel{
	protected $viewFields = array(
		'HelpComment' => array('*','_type'=>'LEFT'),
		'Help'=>array('title'=>'help_title','content'=>'help_content','_on'=>'HelpComment.help_id=Help.id','_type'=>'LEFT'),
		'User'=>array('realname'=>'user_realname','_on'=>'User.uid=HelpComment.uid','_type'=>'LEFT'),
	);
}