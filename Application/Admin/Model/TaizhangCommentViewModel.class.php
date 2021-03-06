<?php 
/**作用:关联用户明细表,添加用户,删除用户,更新用户
 * 关联模型 一对一 HAS_ONE
 * 表:user,auth_group_access
 * 外键:user.uid=auth_group.uid
 * @author :黄药师 <[46914685@qq.com]> 
 */
namespace Admin\Model;
use Common\Model\BaseViewModel;

class TaizhangCommentViewModel extends BaseViewModel{
	protected $viewFields = array(
		'TaizhangComment' => array('*','_type'=>'LEFT'),
		'Taizhang'=>array('title'=>'taizhang_title','type'=>'taizhang_type','_on'=>'TaizhangComment.taizhang_id=Taizhang.id','_type'=>'LEFT'),
		'User'=>array('realname'=>'user_realname','_on'=>'User.uid=TaizhangComment.uid','_type'=>'LEFT'),
	);
}