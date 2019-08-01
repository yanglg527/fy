<?php
/**
 * 主题党日 -- 出勤模型
 */
namespace Common\Model;
use Think\Model\RelationModel;

class ThemePartyDayAttendModel extends RelationModel {

    protected $_link = array(
      // 关联用户名
      'User' =>array(
          'mapping_type' => self::BELONGS_TO,
          'class_name'   => 'User',
          'mapping_fields' => 'uid,realname,headimgurl',
          'mapping_name'  => 'realname',
          'parent_key' => 'uid',
          'foreign_key' => 'uid',
      ),
    );

    /**
     * 按主题获取出勤人列表
     * @param int $id 主题ID
     * @param bool default false 是否分组
     * @return array
     */
    public function attendListByThemeId($id, $is_group=false)
    {
        $res = $this->relation(true)->field('uid,attend_type,status')->where(array('theme_id' => $id))->select();
        if (!empty($res) && $is_group) {
            return $this->formatAttendList($res);
        }
        return $res;
    }

    /**
     * 格式化出勤列表
     */
    protected function formatAttendList($attend)
    {
        foreach($attend as $k=>$v){
            if (empty($v['realname']['headimgurl'])) {
                $v['realname']['headimgurl'] = '/Public/Common/img/common-head.png';
            }
            $res[$v['attend_type']][] = $v['realname'];
        }
        $arr['qq'] = isset($res[0]) ? $res[0] : array();
        $arr['qj'] = isset($res[1]) ? $res[1] : array();
        $arr['cq'] = isset($res[2]) ? $res[2] : array();
        return $arr;
    }
}
