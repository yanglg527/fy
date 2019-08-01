<?php
/**
 * 志愿服务器 模型
 */
namespace Admin\Model;
use Think\Model\RelationModel;

class VolunteerServiceModel extends RelationModel {
    protected $tableName="volunteer_service";
    protected $_validate = array(
        array('theme','require','主题为必填！',1),
        array('status',array(0,1,2,3),'非法状态操作!',1,'in'),
        array('detail','require','详情为必填！',1),
        array('address','require','服务地点为必填！',1),
        array('start_date','require','开始时间不能为空!',1),
        array('end_date','require','结束时间不能为空!',1),
    );
    // 自动完成
    protected $_auto = array (
        array('total', 0),
        array('detail', 'getFieldsd', 3, 'callback'),
        array('uid', 'admin_uid', 1, 'function'),
        array('create_at', 'time', 1, 'function'),
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

    /**
     * 新增或写入
     */
    public function ajaxSave($data)
    {
        $uid = intval(admin_uid());
        // 动态管理状态值
        if (2 == $data['status']) {
            $int = getStatusBydate($data['start_date'], $data['end_date']);
            if (false === $int) $data['status'] = 0;
            $data['status'] = $int;
        }
        try {
            if (empty($data['id'])) {
              if (!$this->create($data)) throw new \Exception($this->getError());
                if (!$num=$this->add()) throw new \Exception($this->getError());
            }else {
                $post = $this->create($data, 2);
                if (!$post) throw new \Exception($this->getError());
                $num = intval($data['id']);
                // 检查数据是否存在
                $sendUid = $this->field('uid')->find($num)['uid'];
                if($uid != $sendUid) throw new \Exception('内容不允许被更新');
                // 更新
                if (false === ($this->where("id=$num")->save($post)) )
                        throw new \Exception($this->getError());
            }
            return array('id' => $num);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return false;
    }

    public function getStatusById($id)
    {
      return $this->where(array('id' => $id))->getField('status');
    }
}
