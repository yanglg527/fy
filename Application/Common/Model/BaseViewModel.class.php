<?php 
namespace Common\Model;
use Think\Model\ViewModel;
use Think\Page;

/**
 * 课程表
 * Class StudyScheduleViewModel
 * @package Study\Model
 */
class BaseViewModel extends ViewModel{
	public function find_page($where,$field, $groupBy,$order,$pageCount=15){
		return $this->findPage($where, $pageCount, $order, 1,$field,8,$groupBy);
	}

	/**
	 * @param $where
	 * @param int $pageCount //每页显示数量
	 * @param $order
	 * @param int $style//显示类型
	 * @param $field
	 * @param int $rollPage// 分页栏每页显示的页数
	 * @return array
	 *  * 返回
	{
	"show":"显示的page格式",
	"list":[内容]
	}
	 */
	public function findPage($where, $pageCount=10, $order, $style = 1,$field,$rollPage=8,$groupBy=""){
		if($where){
			if($groupBy){
				$count1 = $this->where($where)->count();
				$count = $this->where($where)->group($groupBy)->count();
				$count = $count==0?1:$count;
				$count = $count1/$count;
			}else{
				$count = $this->where($where)->count();
			}
		}else{
			if($groupBy){
				$count1 = $this->count();
				$count = $this->group($groupBy)->count();
				$count = $count==0?1:$count;
				$count = $count1/$count;
			}else{
			
				$count = $this->count();
				echo $count;
			}

		}
//		exit(count($this->group($groupBy)->where($where)->select()));
		$page = new Page($count , $pageCount);
		$page->rollPage = $rollPage;
		$page->setConfig('first','首页');
		$page->setConfig('prev','上一页');
		$page->setConfig('next','下一页');
		$page->setConfig('last','末页');

		$pageInfo = array('pageNo'=>I('p',1));
		$pageInfo['pageCount'] = $pageCount;
		if($count == 0){
			$pageInfo['pageNo'] = 1;
			$pageInfo['pageTotal'] = 0;
			$pageInfo['size'] = 0;
			return array("show"=>'', "list"=>array(),'pageInfo'=>$pageInfo);;
		}elseif($style == 0){
			$page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% 第 '.I('p',1).' 页/共 %TOTAL_PAGE% 页 ( '.$pageCount.' 条/页 共 %TOTAL_ROW% 条)');
		}else{

			$page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% 共 %TOTAL_PAGE% 页 ');

		}

		$pageInfo['pageNo'] = I('p',1);
		$pageInfo['pageTotal'] =  $page->totalPages;
		$pageInfo['size'] = $page->totalRows;


		$show = $page->show();
		if($field){
			$list =  $this->field($field);
		}else{
			$list =  $this;
		}
		if($where){
			if($groupBy){
				$list = $list->where($where)->group($groupBy)->order($order)->limit($page->firstRow.','.$page->listRows)->select();
			}else{
				$list = $list->where($where)->order($order)->limit($page->firstRow.','.$page->listRows)->select();
			}

		}else{
			if($groupBy){
				$list = $list->group($groupBy)->order($order)->limit($page->firstRow.','.$page->listRows)->select();
			}else{
				$list = $list->order($order)->limit($page->firstRow.','.$page->listRows)->select();
			}

		}

		$pageInfo['pageTotal'] = $page->totalPages;
		$pageInfo['size'] = $count;

		return array("show"=>$show, "list"=>$list, "pageInfo"=>$pageInfo);
	}

	/**
	 * 检查Order表达式中的视图字段
	 * @access protected
	 * @param string $order 字段
	 * @return string
	 */
	protected function checkOrder($order='') {
		return $order;
	}

}