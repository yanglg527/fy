<?php
/**
 * 待办任务执行者 模型
 */
namespace Mob\Model;

use Think\Model;

class TasksExecutorModel extends Model
{
    protected $tableName = 'tasks_executor';

    /**
     * 检查是否执行者
     */
    public function checkExecutor($id){
        $exeStatus = $this->where(array(
            'tasks_id' => $id,
            'exe_uid' => uid(),
        ))->getField('exe_status', true);
        if (empty($exeStatus)) return false;
        return intval($exeStatus[0]);
    }

    /**
     * 状态管理 1结办 2办理中
     */
    public function statusAction($id, $status){
        try {
            $this->startTrans();
            $bool = $this->where(array(
                'exe_uid' => uid(),
                'tasks_id' => $id
            ))->setField('exe_status', $status);
            if (false === $bool) throw new \Exception('系统发生异常..');
            $username = '系统提示:【'.user()['realname'].'】';
            $content = $status===2?$username.'阅读了任务':$username.'完成了任务';
            if (!updateExeLog($id, $content)) throw new \Exception('写入记录失败..请重试');
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
    }
}
