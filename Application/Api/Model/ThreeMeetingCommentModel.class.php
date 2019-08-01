<?php
/**
 * 三会一课 评论模型
 */
namespace Api\Model;
use Think\Model\RelationModel;

class ThreeMeetingCommentModel extends RelationModel {
    protected $tableName="three_meeting_comment";
    protected $_validate = array(
        array('comments','require','评论为必填!',1),
        array('meeting_id','number','不合法的会议标识!',1),
    );
    // 自动完成
    protected $_auto = array (
        array('status', 'getFieldsd', 3, 'callback'),
        array('uid', 'uid', 1, 'function'),
        array('create_at', 'time', 1, 'function'),
    );

    protected $_link = array(
        // 发布人
        'send_user' =>array(
          'mapping_type' => self::BELONGS_TO,
          'class_name'   => 'User',
          'mapping_fields' => 'realname,headimgurl',
          'mapping_name'  => 'send_user',
          'parent_key' => 'uid',
          'foreign_key' => 'uid',
        ),
    );

    protected function getFieldsd($field)
    {
        // 这里模仿审核开关
        $status = 0;
        return $status;
    }

    /**
     * 添加评论
     */
    public function addComment($meetingId, $content){
        $data = array(
            'meeting_id' => $meetingId,
            'comments' => $content,
        );
        try {
            $this->startTrans();
            if (!$this->create($data)) throw new \Exception($this->getError());
            if (!$id=$this->add()) throw new \Exception($this->getError());
            // $num =M('ThreeMeeting')->where("id=$meetingId")->setInc('comment');
            // if (!$num) throw new \Exception("评论发生错误！请重试");
            $this->commit();
            return $id;
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
    }

    public function delComment($meetingId)
    {
        $uid = intval(uid());
        $sendUid = $this->field('uid')->find($meetingId)['uid'];
        if ($sendUid && $uid == $sendUid) {
            $bool = $this->where(array(
                'uid' => $uid,
                'meeting_id'=>$meetingId)
            )->setField('status', -1);
            if (false === $bool) return false;
            return true;
        }
        return '非评论发布者！';
    }


    /**
     * 按ID获得会议评论的列表
     */
    public function meetingCommentListById($id)
    {
      $list = $this->relation(true)
      ->field('uid,comments,create_at')
      ->where(array('meeting_id'=>$id,'status'=>1))
      ->order('create_at desc')->select();
      if ($list) {
        foreach ($list as $key => &$value) {
          $list[$key]['realname'] = $value['send_user']['realname'];
          $list[$key]['headimgurl'] = $value['send_user']['headimgurl'];
          unset($value['send_user']);
        }
      }
      return $list;
    }

}
