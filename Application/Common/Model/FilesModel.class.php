<?php
namespace Common\Model;
// use Think\Model;
use Think\Model\RelationModel;

/**
 * 附件
 * @author Calvin
 */
class FilesModel extends RelationModel{
		protected $tableName = 'files';
		// 自动完成
		protected $_auto = array (
				// array('upload_id', 'uid', 1, 'function'),
				array('create_at', 'time', 1, 'function'),
				array('files_status', 0, 1),
				array('father_id', 0),
		);
	//array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
		protected $_validate = array(
				array('former_name','require','文件名不合法！',1),
				array('files_path','require','路径不能为空!',1),
				array('filetype','require','文件类型不合法！',1),
		);

		public function insert($data){
			if( !$this->create($data) ) ajaxMsg(1, $this->getError());
			$num = $this->add();
	        if ($num) {
	            $res = array(
	              'id'  => $num,
				  'url' => $data['files_path'],
	              'name' => $data['former_name']
	            );
				return $res;
	        }
			ajaxMsg(1, '文件上传异常');
		}

		/**
		 * 按用户关联附件
		 * @param  int     $uid         用户ID
		 * @param  int     $fatherId    关联的ID
		 * @param  string  $fileId      文件ID 多个文件 英文逗号分隔
		 * @param  integer $filesStatus 文件状态 默认开启
		 */
		public function associatAnnexByUser($uid, $fatherId, $fileId, $filesStatus = 1)
		{
			$field = array(
				'father_id' => $fatherId,
				'files_status' => $filesStatus
			);
			$this->where(
				array('files_id' => array('in',$fileId),'upload_id' => array('eq',$uid))
			);
			return $this->save($field);
		}
}
