<?php
/**
 * 两学一做模型
 * @author Calvin
 */
namespace Admin\Model;
use Think\Model\RelationModel;
class TwoStudyOneDoModel extends RelationModel{
	// protected $insertFields = 'branch_id,type,step,info,theme,claim,mission,start_time';
	protected $tableName="two_study_one_do";

	// 自动完成
    protected $_auto = array (
        array('uid', 'admin_uid', 1, 'function'),
        array('create_at', 'time', 1, 'function'),
        array('info', 'getFieldsd', 3, 'callback'),
    );
	//array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
	protected $_validate = array(
        array('branch_id','require','请选择党组织!',1),
        array('branch_id','number','党组织必须为数字!',1),
        array('type',array(0,1,2,3),'类型不正确！',1,'in'),
        array('start_time','require','开始时间不能为空!',1),
        array('theme','require','主题不能为空！',1),
        array('step','require','主要任务不能为空！',1),
        array('mission','require','重点任务不能为空！',1),
        array('claim','require','工作要求不能为空！',1),
        array('info','require','详情不能为空！',1),
        array('status',array(0,2),'请选择是存到草稿箱或者发布',1,'in'),
    );

	//关联模型
	protected $_link=array(
			// 关联 用户
			'User' =>array(
			  'mapping_type' => self::BELONGS_TO,
			  'class_name'   => 'User',
			  'foreign_key' => 'uid',
			  'parent_key' => 'uid',
			  'mapping_fields' => 'realname,headimgurl',
			  'mapping_name'  => 'send_user',
			  // 'mapping_order' => 'create_at desc',
			  // 'condition' => 'status=1',
			),
			// 考勤表
			'StudyDoAttend'=>array(
				'mapping_type'=>self::HAS_MANY,
				'class_name'=>'study_do_attend',
				'mapping_name'  => 'attend',
				'mapping_fields' => 'id,uid,attend_type',
				'foreign_key'=>'do_id',
			),
			// 附件表
			'Files'=>array(
				'mapping_type' => self::HAS_MANY,
				'class_name'   => 'Files',
				'mapping_fields' => 'files_id,files_path,former_name,filetype',
				'mapping_name'  => 'files',
				'foreign_key' => 'father_id',
				'mapping_order' => 'create_at desc',
				'condition' => 'father_name="oneDo" AND files_status=1',
				),
	);

	protected function getFieldsd($field)
	{
	  return htmlspecialchars_decode($field);
	}

	/**
	 * 插入
	 */
	public function insert($data){
		try {
			$uid = intval(uid());
			$this->startTrans();
			if (!$this->create($data)) throw new \Exception($this->getError()); // 验证异常
			$num = $this->add();
			if ( $num ) {
				$attend = M('StudyDoAttend');
				if( !empty($data['cq']) ){ // 出席人员
				  $cq = formatToArray($data['cq'], $num, 2, 'do_id');
				  if ( !$attend->addAll($cq) ) throw new \Exception($attend->getError());
				}

				if( !empty($data['qx']) ){ // 缺勤人员
				  $qx = formatToArray($data['qx'], $num, 0, 'do_id');
				  if ( !$attend->addAll($qx) ) throw new \Exception($attend->getError());
				}

				if( !empty($data['qj']) ){ // 请假人员
				  $qj = formatToArray($data['qj'], $num, 1, 'do_id');
				  if ( !$attend->addAll($qj) ) throw new \Exception($attend->getError());
				}

				$_Files = D('Files');


				if (!empty($data['addFileId'])) { // 有文件上传
					$addBool = $_Files->associatAnnexByUser($uid, $num, $data['addFileId']);
					if ($addBool === false) throw new \Exception("关联附件失败");
				}

				if (!empty($data['delFileId'])) { // 有文件上传
					$bool = $_Files->associatAnnexByUser($uid, $num, $data['delFileId'], 0);
					if ($bool === false) throw new \Exception("关联附件失败");
				}
				$this->commit();
				return array('id' => $num);
			}
			throw new \Exception($this->getError());
		} catch (\Exception $e) {
			$this->rollback();
			return $e->getMessage();
		}
		return false;
	}

	public function updateAction($data){
		try {
			$uid = intval(uid());
			$id = intval($data['id']);
			$this->startTrans();
			if (!$this->create($data)) throw new \Exception($this->getError()); // 验证异常
			$bool = $this->save();
			if ( $bool !== false ) {
				$attend = M('StudyDoAttend');
				if(($attend->where("do_id=$id")->delete()) === false) throw new \Exception($attend->getError());
				if( !empty($data['cq']) ){ // 出席人员
					$cq = formatToArray($data['cq'], $id, 2, 'do_id');
					if ( !$attend->addAll($cq) ) throw new \Exception($attend->getError());
				}

				if( !empty($data['qx']) ){ // 缺勤人员
					$qx = formatToArray($data['qx'], $id, 0, 'do_id');
					if ( !$attend->addAll($qx) ) throw new \Exception($attend->getError());
				}

				if( !empty($data['qj']) ){ // 请假人员
					$qj = formatToArray($data['qj'], $id, 1, 'do_id');
					if ( !$attend->addAll($qj) ) throw new \Exception($attend->getError());
				}

				$_Files = D('Files');
				if (!empty($data['addFileId'])) { // 有文件上传
					$addBool = $_Files->associatAnnexByUser($uid, $id, $data['addFileId']);
					if ($addBool === false) throw new \Exception("关联附件失败");
				}

				if (!empty($data['delFileId'])) { // 有文件上传
					$bool = $_Files->associatAnnexByUser($uid, $id, $data['delFileId'], 0);
					if ($bool === false) throw new \Exception("关联附件失败");
				}
				$this->commit();
				return array('id' => $id);
			}
			throw new \Exception($this->getError());
		} catch (\Exception $e) {
			$this->rollback();
			return $e->getMessage();
		}
		return false;
	}

	/**
	 * 列表
	 */
	public function lists($branchId, $status=2, $page=1, $pagesize=10)
	{
	  $res = $this->relation(['send_user','branch'])
	  ->page("$page,$pagesize")
	  ->field('theme,type,')
	  ->where( array('status' => $status, 'branch_id' => $branchId) )
	  ->order('create_at desc')
	  ->select();
	  return $res;
	}

	/**
	 * 详情
	 */
	public function editInfo($id)
	{
		$res = $this->relation(true)->find($id);
		return $res;
	}

	/**
	 * 详情
	 */
	public function info($id)
	{
	  $res = $this->relation(['files', 'send_user'])->find($id);
	  if ($res['status'] == 2) {
		$this->find($id);
		$this->pageviews = ++$this->pageviews;
		$this->save();
		return $res;
	  }else {
		return array();
	  }
	}

}
