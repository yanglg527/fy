<?php
/**
 * 主题党日
 */
namespace Admin\Model;
use Think\Model\RelationModel;

class ThemePartyDayModel extends RelationModel {
    protected $tableName="theme_party_day";
    protected $_validate = array(
        array('branch_id','require','请选择党组织!',1),
        array('branch_id','number','党组织必须为数字!',1),
        array('start_time','require','开始时间不能为空!',1),
        array('end_time','require','结束时间不能为空!',1),
        array('content','require','内容不能为空！',1),
        array('place','require','地点不能为空！',1),
        array('theme','require','主题不能为空！',1),
        array('must_num','number','应到人数必须是数字!', 2),
        array('real_num','number','实到人数必须是数字!', 2),
        array('host_id','number','请选择主持人!',1),
        array('record_id','number','请选择记录人!',0),
        array('status',array(0,2,3),'非法状态操作!',1,'in'),
    );
    // 自动完成
    protected $_auto = array (
        array('content', 'getFieldsd', 3, 'callback'),
        array('must_num', 'completAttend', 3, 'callback'),
        array('real_num', 'completAttend', 3, 'callback'),
        array('uid', 'admin_uid', 1, 'function'),
        array('year', 'thisYear', 1, 'function'),
        array('create_at', 'time', 1, 'function'),
    );

    protected $_link = array(
        // 附件表
        'Files'=>array(
            'mapping_type' => self::HAS_MANY,
            'class_name'   => 'Files',
            'mapping_fields' => 'files_id,files_path,former_name,filetype',
            'mapping_name'  => 'files',
            'foreign_key' => 'father_id',
            'mapping_order' => 'create_at desc',
            'condition' => 'father_name="ThemeDay" AND files_status=1',
            ),
        // 考勤表
        'ThemePartyDayAttend'=>array(
            'mapping_type'=>self::HAS_MANY,
            'class_name'=>'theme_party_day_attend',
            'mapping_name'  => 'attend',
            'mapping_fields' => 'id,uid,attend_type',
            'foreign_key'=>'theme_id',
        ),
        // 发布人
        'send_user' =>array(
          'mapping_type' => self::BELONGS_TO,
          'class_name'   => 'User',
          'mapping_fields' => 'realname',
          'mapping_name'  => 'send_user',
          'parent_key' => 'uid',
          'foreign_key' => 'uid',
        ),
        // 主持人
        'host_name' =>array(
          'mapping_type' => self::BELONGS_TO,
          'class_name'   => 'User',
          'mapping_fields' => 'realname',
          'mapping_name'  => 'host_name',
          'parent_key' => 'uid',
          'foreign_key' => 'host_id',
        ),
        // 记录人
        'record_name' =>array(
          'mapping_type' => self::BELONGS_TO,
          'class_name'   => 'User',
          'mapping_fields' => 'realname',
          'mapping_name'  => 'record_name',
          'parent_key' => 'uid',
          'foreign_key' => 'record_id',
        ),
        // 支部
        'branch_name' =>array(
          'mapping_type' => self::BELONGS_TO,
          'class_name'   => 'party_branch',
          'mapping_fields' => 'name',
          'mapping_name'  => 'branchname',
          'parent_key' => 'uid',
          'foreign_key' => 'branch_id',
        ),
    );

    /**
    * 富文本处理
    * @param string $field 需要处理的富文本
    * @return string  处理后的富文本
    */
    protected function getFieldsd($field)
    {
        return htmlspecialchars_decode($field);
    }

    protected function completAttend($field)
    {
        return 666;
    }

    /**
     * 新增或写入
     * @param [type] $data [description]
     */
    public function addOrupdate($data)
    {
        $uid = intval(uid());
        try {
            $this->startTrans();
            $attend = M('ThemePartyDayAttend');

            if (empty($data['id'])) {
                if (!$this->create($data)) throw new \Exception($this->getError());
                if (!$num=$this->add()) throw new \Exception($this->getError());
            }else {
                $post = $this->create($data, 2);
                if (!$post) throw new \Exception($this->getError());
                $num = intval($data['id']);
                // 检查数据是否存在
                $sendUid = $this->field('uid')->find($num)['uid'];
                if(!$sendUid) throw new \Exception('内容不允许被更新');
                // 更新
                if (false === ($this->where("id=$num")->save($post)) )
                        throw new \Exception($this->getError());

                $bool = $attend->where("theme_id=$num")->delete();
                if(false === $bool) throw new \Exception($this->getError());
            }

            if( !empty($data['cq']) ){ // 出席人员
              $cq = formatToArray($data['cq'], $num, 2, 'theme_id');
              if ( !$attend->addAll($cq) ) throw new \Exception('');
            }

            if( !empty($data['qx']) ){ // 缺勤人员
              $qx = formatToArray($data['qx'], $num, 0, 'theme_id');
              if ( !$attend->addAll($qx) ) throw new \Exception($attend->getError());
            }

            if( !empty($data['qj']) ){ // 请假人员
              $qj = formatToArray($data['qj'], $num, 1, 'theme_id');
              if ( !$attend->addAll($qj) ) throw new \Exception($attend->getError());
            }

            $_Files = D('Files');
            if (!empty($data['addFileId'])) { // 有文件上传
                $addBool = $_Files->associatAnnexByUser($uid, $num, $data['addFileId']);
                if (false === $addBool) throw new \Exception("关联附件失败");
            }

            if (!empty($data['delFileId'])) { // 有文件上传
                $bool = $_Files->associatAnnexByUser($uid, $num, $data['delFileId'], 0);
                if (false === $bool) throw new \Exception("关联附件失败");
            }

            $this->commit();
            return array('id' => $num);
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
        return false;
    }

    /**
     * 详情
     * @param int $id
     * @return array
     */
    public function detail($id)
    {
        $res = $this->relation(true)->find($id);
        if (!empty($res)) {
            $res['send_user'] = $res['send_user']['realname'];
            $res['host_name'] = $res['host_name']['realname'];
            $res['record_name'] = $res['record_name']['realname'];
            $res['attend'] = $this->getPartyDayAttendListByThemeId($id);
            // echo "<pre>";
            // var_dump($res);exit;
            return $res;
        }
        return array();
    }

    /**
     * 编辑数据初始化
     */
    public function editInfo($id)
    {
        $res = $this->relation(['files','attend'])->find($id);
        return $res;
    }

    /**
     *  按主题ID获得出勤人列表
     * @param array
     */
    protected function getPartyDayAttendListByThemeId($id)
    {
        // ThemePartyDayAttend
        return D('ThemePartyDayAttend')->attendListByThemeId($id, true);
    }

}
