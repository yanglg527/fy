<?php
/**
 * 三会一课 收藏模型
 */
namespace Api\Model;
use Think\Model\RelationModel;

class ThreeMeetingTibetModel extends RelationModel {
    protected $uid;
    protected $_validate = array(
        array('meeting_id','require','缺少参数!',1),
        array('is_collection','require','缺少参数!',1),
        array('is_collection',array(0,1),'值不合法!',1,'in'),
        array('meeting_id','number','不合法的会议标识!',1),
    );
    // 自动完成
    protected $_auto = array (
        array('uid', 'uid', 1, 'function'),
    );

    protected function _initialize(){
        parent::_initialize();
        $this->uid = intval(uid());
    }

    /**
     * 点赞
     */
    public function runCollectionAction($meetingId, $isCollection){
        $data = array(
            'uid' => $this->uid,
            'meeting_id' => $meetingId,
            'is_collection' => $isCollection,
        );
        try {
            $this->startTrans();
            if(!$this->create($data)) throw new \Exception($this->getError());
            $id = $this->checkUserIsCollection($meetingId);
            if ($id) { // 如果记录存在
                $bool = $this->save(array('id'=> $id, 'is_collection'=>$isCollection));
                if (false === $bool) throw new \Exception($this->getError());
            }else {
                $id = $this->add();
                if (!$id) throw new \Exception($this->getError());
            }
            // return
            if (!meetingCollection($meetingId, $isCollection))
                throw new \Exception('更新收藏总数时发生错误..请重试');

            $this->commit();
            return $meetingId;
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }

    }

    /**
     * 检查用户
     * @param  int $meetingId 会议ID
     */
    public function checkUserIsCollection($meetingId)
    {
        $id = $this->field('id')
        ->where(array('uid' => $this->uid, 'meeting_id' => $meetingId))
        ->find()['id'];
        if (!empty($id)) {
            return $id;
        }
        return false;
    }

}
