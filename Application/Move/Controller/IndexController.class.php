<?php
namespace Move\Controller;
/**
 * 临时模块 主要用于处理旧数据
 * 之后写到新的系统中
 * 数据库中 old_ 开头的表是旧的数据表
 * error :
 * 		找不到需要迁移的台账 => 旧库中没符合条件的数据
 * 		找不到匹配人员 => 新库中找不到对应的发布人
 */

use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;


class IndexController extends BaseAuthController {
	protected $mapTags; // 台账标签对照 key 旧库标签id => value 新库标签id
	protected $mapUsers; // 台账用户对照 key 就库 id => value id 新库id realname 新库用户名

	protected $_Tags;
	protected $_OldTags;
	protected $_Taizhang;
	protected $_OldTaizhang;
	protected $_OldUser;
	protected $_User;

	function _initialize() {
		parent::_initialize();
		$this->_Tags = M('TaizhangTags');
		$this->_OldTags = M('OldTaizhangTags');

		$this->_Taizhang = D('Taizhang');
		$this->_OldTaizhang = M('OldTaizhang');

		$this->_OldUser = M('OldUser');
		$this->_User = M('User');
	}

	public function index(){
		set_time_limit(0);
		// 获取需要迁移的台账
		$taizhangItme = $this->getMoveTaiZhangLists();
		// 获取添加积分的记录($score['itme'])和最终增加的积分总和($score['userScoreDetail'])
		$score = $this->getScoreLog();

		// 数据准备好
		try {
			$this->_Taizhang->startTrans();
			// 插入台账
			if (!$this->_Taizhang->addAll($taizhangItme))
				throw new \Exception("插入台账时发生错误", 1);
			// 插入记录
			$_UserScoreLog = M('UserScoreLog');
			if (!$_UserScoreLog->addAll($score['itme']))
				throw new \Exception("插入积分时发生错误", 1);

			// 更新用户积分
			foreach ($score["userScoreDetail"] as $key => $value) {
				$bool = $this->_User->where(array('uid' => $key))->setInc('score', $value);
				if ($bool === false) throw new \Exception("用户UID".$key."增加".$value."积分时发生错误", 1);
			}
			$this->_Taizhang->commit();
			ajaxMsg(0, 'success', array('taizhangItme' => $taizhangItme,$score));
		} catch (\Exception $e) {
			$this->_Taizhang->rollback();
			ajaxMsg(1, $e);
		}
	}

	/**
	 * 获得积分记录和具体加分详情
	 * @return array
	 */
	protected function getScoreLog(){
		$OldScore = M('OldUserScoreLog');
		$scoreList = $OldScore->where(array(
			'uid' => array('GT', '10406'),
			'type' => array('in', '1,5')) // 过滤台账
			)->select();
		$i=0;
		$itme = array();
		// 重新映射库中用户
		foreach ($scoreList as $v) {
			// 如果旧库中用户存在于新库中
			if (!empty($this->mapUsers[$v['uid']]['uid'])) {
				$itme[$i]['uid'] = $this->mapUsers[$v['uid']]['uid']; // 新旧uid映射
				$itme[$i]['score'] = $v['score'];
				$itme[$i]['content'] = $v['content'];
				$itme[$i]['type'] = $v['type'];
				$itme[$i]['create_time'] = $v['create_time'];
				$i++;
			}
		}
		unset($scoreList);
		// 计算每个用户积分详情
		$userScoreDetail = $this->computeUserIntegralByScore($itme);

		return array(
			'itme' => $itme,
			'userScoreDetail' => $userScoreDetail
		);
	}

	/**
	 * 计算用户积分
	 */
	protected function computeUserIntegralByScore($itme){
		$userScore = array();
		$userScoreInfo = array();
		foreach ($itme as $value) {
			$userScore[$value['uid']][] = $value['score'];
		}
		// 求和
		foreach ($userScore as $k => $v) {
			$userScoreInfo[$k] = array_sum($v);
		}
		return $userScoreInfo;
	}

	/**
	 * 加载新旧、库用户对照表
	 */
	protected function loadOldUsersMap(){
		$oldUsers = $this->_OldUser->field('uid,realname,mobile')->where(array(
			'status' => 1,
			'uid' => array('GT', '10406')))
			->select();

		foreach ($oldUsers as $value) {
			$user = $this->_User->field('uid,realname')->where(array(
				'mobile' => $value['mobile'],
				'realname' => $value['realname']))
				->find();
			if (!empty($user)) {
				$this->mapUsers[$value['uid']]['uid'] = $user['uid'];
				$this->mapUsers[$value['uid']]['realname'] = $user['realname'];
			}
		}
		return true;
	}

	/**
	 * 更新台账标签并生成对照组织
	 * @return array
	 */
	protected function updeTags(){


		$oldTags = $this->_OldTags->select();
		if (empty($oldTags)) ajaxMsg(1, '标签不存在，程序已终止..');
		foreach ($oldTags as $value) {
			$title = $this->_Tags->where(array('title' => $value['title']))->find();
			if (empty($title)) {
				// 缺少记录 插入
				$num = $this->_Tags->add(array('title' => $value['title']));
				if ($num) { $this->mapTags[$value['id']] = $num; }
			}else {
				$this->mapTags[$value['id']] = $title['id'];
			}
		}
		return true;
	}

	/**
	 * 获得需要迁移的台账列表
	 * @return array
	 */
	protected function getMoveTaiZhangLists(){
		$this->updeTags();
		$this->loadOldUsersMap();

		$lists = $this->_OldTaizhang->where(array(
			'uid' => array('GT', '10406'), // 过滤无关用户
			'template_id' => 3)) // 只要指定模板
			->select();
		if (empty($lists)) ajaxMsg(1, '找不到需要迁移的台账 ' . date("Y-m-d H:i", time()));
		$i = 0;
		$data = array();
		foreach ($lists as $value) {
			if (!empty($this->mapUsers[$value['uid']]['uid'])) {
				$data[$i]['uid'] = $this->mapUsers[$value['uid']]['uid'];
				$data[$i]['title'] = $value['title'];
				$data[$i]['cover'] = $value['cover'];
				$data[$i]['publish_name'] = $this->mapUsers[$value['uid']]['realname'];
				$data[$i]['publish_time'] = $value['publish_time'];
				$data[$i]['type'] = $value['type'];
				$data[$i]['address'] = $value['address'];
				$data[$i]['type2'] = $value['type2'];
				$data[$i]['create_time'] = $value['create_time'];
				$data[$i]['status'] = $value['status'];
				$data[$i]['tag_id'] = $this->mapTags[$value['tag_id']];
				$data[$i]['norm_id'] = $value['norm_id'];
				$data[$i]['dz_count'] = $value['dz_count'];
				$data[$i]['template_id'] = $value['template_id'];
				$data[$i]['pl_count'] = $value['pl_count'];
				$data[$i]['sc_count'] = $value['sc_count'];
				$data[$i]['party_name'] = $value['party_name'];
				$data[$i]['user_name'] = $value['user_name'];
				$data[$i]['organization_id'] = $value['organization_id'];
				$data[$i]['add_uid'] = $this->mapUsers[$value['uid']]['uid'];
				$data[$i]['tz_content'] = $value['tz_content'];
				$data[$i]['mission_id'] = $value['mission_id'];
				$data[$i]['affairs_id'] = $value['affairs_id'];
				$i++;
			}
		}
		if (empty($data)) ajaxMsg(1, '找不到匹配人员 ' . date("Y-m-d H:i", time()));
		return $data;
	}
}
