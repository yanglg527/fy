<?php
namespace Common\Model;
use Think\Model\RelationModel;

class BranchTaskPoolModel extends RelationModel{
    protected $tableName="tasks_item";
    const statustxt = array('未完成', '已完成', '未完成');
    const statusstyle = array('orange', 'gray', 'orange');
    protected $_link = array(
        'TasksExecutor' => array(
            'mapping_type' => self::HAS_MANY,
            'class_name' => 'TasksExecutor',
            'mapping_fields' => 'exe_status',
            'mapping_name'  => 'exe',
            'foreign_key' => 'tasks_id',
            'mapping_order' => 'create_at desc',
            // 'condition' => 'father_name="ThemeDay" AND files_status=1',
        ),
        // 发布人
        'PublishUser' =>array(
            'mapping_type' => self::BELONGS_TO,
            'class_name'   => 'User',
            'mapping_fields' => 'realname',
            'mapping_name'  => 'PublishUser',
            'parent_key' => 'uid',
            'foreign_key' => 'uid',
        ),
    );

    /**
     * 支部获取任务列表
     * @param  array  $map 查询条件
     * @return [type]      [description]
     */
    public function tasksPoolListByBranch(array $map){
        if (empty($map)) return array();
        $overdue = 0; // 未完成
        $complete = 0; // 已完成

        $tasksList = $this->relation(true)
        ->field('id,uid,publish_branch,tasks_title,status,start_date,end_date,create_at')
        ->where($map)
        ->order('create_at desc')
        ->select();

        foreach ($tasksList as $key => &$value) {
            // 样式和提示文本
            $value['status_txt'] = self::statustxt[$value['status']];
            $value['status_style'] = self::statusstyle[$value['status']];
            // 时间处理
            $value['create_at'] = date('Y-m-d', $value['create_at']);
            $value['start_date'] = date('Y-m-d H:i', strtotime($value['start_date']));
            $value['end_date'] = date('Y-m-d H:i', strtotime($value['end_date']));
            // 人员统计
            $value['exe'] = array_column($value['exe'], 'exe_status');
            $countVal = array_count_values($value['exe']);
            $value['overdue'] = intval($countVal[0]) + intval($countVal[2]); // 未完成
            $value['complete'] = intval($countVal[1]); // 完成

            $complete += intval($countVal[1]);
            $overdue += intval($countVal[2]);
            $overdue += intval($countVal[0]);

            $value['PublishUser'] = $value['PublishUser']['realname'];
            unset($value['exe']);
        }
        $statusColumn = array_column($tasksList, 'status');
        $statusCountVal = array_count_values($statusColumn);
        $count = array(
            'rightNum' => count($tasksList),
            'total' => count($tasksList),
            'overdue' => intval($statusCountVal[0]) + intval($statusCountVal[2]),
            'complete' => intval($statusCountVal[1])
        );

        $list = array(
            'count' => $count,
            'branchTasks' => $tasksList,
        );

        return $list;
    }
}
