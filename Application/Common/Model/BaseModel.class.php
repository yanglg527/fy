<?php 
namespace Common\Model;
use Think\Model;
use Think\Page;

/**
 * 课程表
 * Class StudyScheduleViewModel
 * @package Study\Model
 */
class BaseModel extends Model{

	/**
	 * @param $where
	 * @param int $pageCount
	 * @param $order
	 * @return array
	 * 返回{
		"show":"显示的page格式",
	 	"list":[内容]
	 * }
	 */
	public function findPage($where, $pageCount=10, $order){
		$count = $this->where($where)->count();
		$page = new Page($count , $pageCount);
		$page->setConfig('first','首页');
		$page->setConfig('prev','上一页');
		$page->setConfig('next','下一页');
		$page->setConfig('last','尾页');
		$page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% 共 %TOTAL_PAGE% 页 ');
//		$page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% 第 '.I('p',1).' 页/共 %TOTAL_PAGE% 页 ( '.$pageCount.' 条/页 共 %TOTAL_ROW% 条)');
		$show = $page->show();
		$list = $this->where($where)->order($order)->limit($page->firstRow.','.$page->listRows)->select();
		return array("show"=>$show, "list"=>$list,'pageNo'=>I('p',1));
	}
}