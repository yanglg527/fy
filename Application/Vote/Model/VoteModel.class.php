<?php
namespace Vote\Model;

use Think\Model\RelationModel;

class VoteModel extends RelationModel
{
    protected $tableName = 'Vote';
    protected $_validate = array(
        array('status',array(1),'非法状态操作!',1,'in'),
        array('title','require','投票主题不能为空!',1),
        array('limited_time','require','截止时间不能为空!',1),
    );
    protected $_auto = array (
        array('uid', 'uid', 1, 'function'),
        array('auth_type', '1', 1), // 1市局 2总支 3 支部
        array('is_multiply', 1, 1), // 单选 0 多选 1
        array('visited_count', 0, 1), // 访问量
        array('content', 'getFieldsdContent', 3, 'callback'),
        array('published_time', 'time', 1, 'function'),
        array('limited_time', 'getFieldsd', 3, 'callback'),

    );

    protected function getFieldsd($field)
    {
        return strtotime($field)+86399;
    }

    protected function getFieldsdContent($field){
        return htmlspecialchars_decode($field);
    }

    public function ajaxSave($post){
        $selective = explode(',', $post['selective']);
        $roles = explode(',', $post['roles']);
        $supervisorArr = explode(',', $post['supervisor']);
        $voteBranchArr = explode(',', $post['branchs']);
        try {
            $this->startTrans();
            // 可选等于备选
            if (!$this->create($post)) throw new \Exception($this->getError());
            if (!$id = $this->add()) throw new \Exception($this->getError());
            // 写入备选项
            if (empty($selective)) throw new \Exception('必须选择两位以上党员！');
            $item =array();
            foreach ($selective as $k => $v) {
                $item[$k]['vote_id'] = $id;
                $item[$k]['title'] = $v;
                $item[$k]['count'] = 0;
                $item[$k]['sort'] = $k + 1;
                $item[$k]['image'] = '';
            }
            $itemBool = D('VoteItem')->addAll($item);
            if ($itemBool === false) throw new \Exception('系统繁忙请稍后重试！');

            // 写入备选项
            if (empty($roles)) throw new \Exception('角色数据异常请稍后重试!');
            $rolesItem =array();
            foreach ($roles as $k => $v) {
                $rolesItem[$k]['vote_id'] = $id;
                $rolesItem[$k]['role_id'] = $v;
            }
            $roleBool = D('VoteRole')->addAll($rolesItem);
            if ($roleBool === false) throw new \Exception('系统繁忙请稍后重试！2');

            // 写入支部表
            if (empty($voteBranchArr)) throw new \Exception('必须指定参与投票的支部!');
            $voteBranch = array();
            foreach ($voteBranchArr as $key => $value) {
                $voteBranch[$key]['vote_id'] = $id;
                $voteBranch[$key]['branch_id'] = $value;
            }
            $branchBool = D('VoteLimitBranch')->addAll($voteBranch);
            if ($branchBool === false) throw new \Exception('系统繁忙请稍后重试！3');

            // 监督员
            if (!empty($supervisorArr)) {
                $supervisor = array();
                foreach ($supervisorArr as $key => $value) {
                    $supervisor[$key]['vote_id'] = $id;
                    $supervisor[$key]['uid'] = $value;
                }
                $supBool = D('VoteSupervisor')->addAll($supervisor);
                if ($supBool === false) throw new \Exception('系统繁忙请稍后重试!');
            }

            $this->commit();
            return array('id' => $id);
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }

    }

}
