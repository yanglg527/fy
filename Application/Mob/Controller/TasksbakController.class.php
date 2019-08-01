<?php
/**
 * 待办任务
 */
namespace Mob\Controller;
use Common\Controller\BaseAuthController;
use Common\Util\CalendarUtil;

class TasksController extends BaseAuthController {
    const taskstype = array('党支部', '党小组', '个人', '纪检');
    const status = array('禁用', '已结束', '进行中', '待开始');
    const statusclass = array('red', 'yellow', 'orange', 'red');
    const fathername = 'tasks_admin';
    protected $uid;

    function _initialize(){
        parent::_initialize();
        $uid = intval(uid());
  //      if (!$uid) ajaxMsg(1, '无权进入');
        $this->uid = $uid;
    }

    /**
     * 列表模板加载
     */
    public function index(){
        /**
         * 假装有计划任务，
         * 是有点浪费资源，
         * 但也没办法呀！
         */
        R('Api/Plan/index');
        $user = user();
        $this->assign('isEditable', checkPublishParameter( $user['uid'], $user['branch_id'] ));// 发布权限
        $this->display();
    }

    /**
     * 获得列表数据
     */
    public function ajaxGetList(){
        $keyword = I('post.keyword'); // 默认获取进行中
        $status = intval(I('post.status', 2)); // 默认获取进行中
        $type = intval(I('post.type', 2)); // 默认获取个人
        $page = intval(I('post.page', 1)); // 默认第一页
        $pageSize = I('post.pageSize', 15);
        $_TaskView = D('TaskListView');

        $map['TasksExecutor.exe_uid'] = $this->uid;
        $map['TasksItem.tasks_type'] = $type;
        // 只获取进行中和已结办的数据
        $map['TasksItem.status'] = array('in', '1,2,3');

        if (!empty($keyword)) {
            $map['TasksItem.tasks_title'] = array('like', '%' . $keyword . '%');
        }
        $res = array();
        // 加载第一页数据需要返回 任务总数
        if (1 === $page) {
            $total = $_TaskView->where($map)->getField('status',true);
            $countValues = array_count_values($total);
            $res['total'] = count($total);
            $res['ing'] = intval($countValues[2]);
            $res['end'] = intval($countValues[1]);
        }

        // 获取进行中的任务
        if (2 === $status) {
            $map['TasksItem.status'] = $status;
        }

        $list = $_TaskView->where($map)->page("$page,$pageSize")
        ->order('create_at desc')
        ->select();

        if (!empty($list)) {
            $baseStatus = self::status;
            $statusclass = self::statusclass;
            foreach ($list as $k => $v) {
                unset($list[$k]['tasks_id'],$list[$k]['exe_id']);
                $list[$k]['class'] = $statusclass[$v['status']];
                $list[$k]['status'] = $baseStatus[$v['status']];
                $list[$k]['start_date'] = date('Y-m-d H:i',strtotime($v['start_date']));
                $list[$k]['end_date'] = date('Y-m-d H:i',strtotime($v['end_date']));
                $list[$k]['create_at'] = date('Y-m-d',$v['create_at']);
            }
            $res['list'] = $list;
        }
        if (!empty($res)) {
            ajaxMsg(0, 'success', $res);
        }
        ajaxMsg(1, '无数据');
    }

    /**
     * 详情
     */
    public function detail(){
        $id = I('get.id');
        $monitor = I('get.monitor');
        $_TaskExe = D('TasksExecutor');
        $int = $_TaskExe->checkExecutor($id);
        if (false !== $int || !empty($monitor)) {
            // $monitor 是从任务汇总出跳转进来的
            if (false === $int && !empty($monitor)) {
                $this->assign('monitor', $monitor);
            }
            // 处理记录
            if (0 === $int) {
                $res = $_TaskExe->statusAction($id, 2);
            }
            $_DetailView = D('Admin/TasksItem');
            $item = $_DetailView->detail($id);
            if ($item) {
                $_mailbox = D('MailboxView');
                $_logView = D('TaskLogView');
                // 信箱
                $item['mailbox'] = $_mailbox->where(array(
                    'TasksMailbox.tasks_id' => $id,
                    'TasksMailbox.status' => 1,
                ))->order('create_at desc')->select();

                // 待开始提示
                if (3 == $item['status']) {
                    $item['status_msg'] = '任务还没开始哦!';
                }
                if (1 === $int) {
                    $item['status_msg'] = '任务已结办！';
                }
                // 进度
                $log = $_logView->where(array(
                    'tasks_id' => $id
                ))->order('create_at desc')->select();
                $item['log'] = $log;
                // 处理附件
                if ($item['files']) {
                    foreach ($item['files'] as $k => &$v) {
                        $v['filetype'] = getFileExtension($v['files_path']);
                        if ($v['upload_id'] == uid()) {
                            $item['my_files'][] = $v;
                            unset($item['files'][$k]);
                        }
                    }
                }

                // 出勤
                $att = $this->getTasksAttendanceList($id);
                if (!empty($att)) {
                    $tem = array();
                    foreach ($att as $key => &$value) {
                        if ($value['exe_status'] === '1') {
                            $tem['cp'][] = $value;
                            continue;
                        }
                        $tem['ov'][] = $value;
                    }
                    $tem['total'] = count($att);
                    $tem['overdue'] = count($tem['ov']);
                    $tem['complete'] = count($tem['cp']);
                    unset($att);
                }
                $this->assign('att', $tem);
            }
        }
        $this->assign('item', $item);
        $this->display();
    }

    /**
     * 获取出勤列表
     * @param  [type] $tasksId [description]
     * @return [type]          [description]
     */
    protected function getTasksAttendanceList($tasksId){
        $_attendanceView = D('TasksAttendanceListView');
        return $_attendanceView->where(array(
            'TasksExecutor.tasks_id' => $tasksId
        ))->select();
    }

    /**
     * 添加新的进度记录
     */
    public function addNewProgress(){
        if (IS_GET) {
            $res = array(
                'id' => I('get.id'),
                'date' => date('Y-m-d H:i', time())
            );
            $this->assign($res);
            $this->display();
        }

        if (IS_AJAX) {
            $id = I('post.id');
            $status = intval(I('post.status'));
            $content = I('post.content');
            // $createAt = I('post.create_at');
            $createAt = date('Y-m-d H:i', time());
            $fileIds = explode(',', I('post.file_id'));
            $delFileIds = explode(',', I('post.surplus_file_id')); // to string
            $addFileId = getAddFileLists($fileIds, $delFileIds);
            $delFileId = getDelFileLists($fileIds, $delFileIds);
            try {
                $_Files = D('Files');
                $_Files->startTrans();
                $bool = updateExeLog($id, $content, strtotime($createAt));
                if (false === $bool) throw new \Exception('更新发生错误..请重试'.$bool);
                if (1 === $status) {
                    endTasks($id);
                }
                if (!empty($addFileId)) {
                    $addBool = $_Files->associatAnnexByUser(uid(), $id, $addFileId);
                    if (false === $addBool) throw new \Exception("关联附件失败");
                }

                if (!empty($delFileId)) {
                    $delBool = $_Files->associatAnnexByUser(uid(), $id, $delFileId, 0);
                    if (false === $delBool) throw new \Exception("关联附件失败");
                }
                $_Files->commit();
                ajaxMsg(0, 'success');
            } catch (\Exception $e) {
                $_Files->rollback();
                ajaxMsg(1, $e->getMessage());
            }
        }
    }


    /**
     * 信箱留言
     */
    public function addMailbox(){
        if (IS_GET) {
            $id = I('get.id');
            $this->assign('id', $id);
            $this->display();
            return ;
        }

        if (IS_AJAX) {
            $post['uid'] = uid();
            $post['status'] = 1;
            $post['tasks_id'] = I('post.id');
            $post['content'] = I('post.content');
            $post['create_at'] = time();

            if (M('TasksMailbox')->add($post)) {
                ajaxMsg(0, '添加成功！等待管理员审核');
            }
            ajaxMsg(1, '添加失败！请稍后重试..');
        }
    }

    /**
     * ajax上传文件
     */
    public function ajaxUploadAnnex(){
        $res = uploadAnnex(self::fathername, self::fathername, uid());
        if (!empty($res['url'])) {
            $res['type'] = getFileExtension($res['name']);
            $res['url'] = C('DOMAIN').$res['url'];
            ajaxMsg(0, 'success', $res);
        }else {
            ajaxMsg(1, '更新失败'. $res);
        }
    }

    /**
     * 发布数据初始化
     * $tasksType array('党支部', '党小组', '个人', '纪检')
     */
    public function initData(){
        $tasksType = I('get.tasksType');
        $datas = array();
        switch ($tasksType) {
            case '0': // 党支部
                $datas = getBranchInfo();
                break;
            case '1': // 党小组
                $datas = D('Admin/GroupListView')->select();
                break;
            case '2': // 个人
                $datas = getUsersBystatus();
                break;
        }
        $this->assign('tasksType', array(
            'id' => $tasksType,
            'name' => self::taskstype[$tasksType]));
        $this->assign('data', $datas);
        $this->display();
    }

    /**
     * POST 参数处理
     * @return [type] [description]
     */
    protected function checkParameter(){
        $tasksType= I('post.tasks_type');
        $implementer = explode(',', I('post.implementer'));
        $post['tasks_type'] = $tasksType;
        $post['branch_id'] = $tasksType === '0' ? $implementer : '';
        $post['group_id'] = $tasksType === '1' ? $implementer : '';
        $post['target'] = $tasksType === '2' ? $implementer : '';
        $post['start_date'] = I('post.start_date');
        $post['end_date'] = I('post.end_date');
        $post['tasks_title'] = I('post.tasks_title');
        $post['tasks_contents'] = I('post.tasks_contents');
        $post['work_item'] = htmlspecialchars_decode(I('post.work_item'));
        $post['status'] = I('post.status');
        $post['file_id'] = explode(',', I('post.file_id'));
        $post['surplus_file_id'] = explode(',', I('post.surplus_file_id')); // to string
        $post['addFileId'] = getAddFileLists($post['file_id'], $post['surplus_file_id']);
        $post['delFileId'] = getDelFileLists($post['file_id'], $post['surplus_file_id']);
        $user = user();
        $post['publish_branch'] = $user['branch_id'];
        $post['uid'] = $user['uid'];
        if (2 == $post['status']) {
            $status = getStatusBydate($post['start_date'], $post['end_date']);
            if ($status !== false)  $post['status'] = $status;
        }
        return $post;
    }

    /**
     * 发布
     */
    public function ajaxSave(){
        // 验证权限
        $post = $this->checkParameter();
        $res = D('Admin/TasksItem')->ajaxSave($post);
        // 接收参数
        // 写入库
        if (is_array($res) && isset($res['id'])) {
          ajaxMsg(0, 'success', $res);
        }
        ajaxMsg(1, $res.'请重试');
    }

    /**
     * 任务汇总
     */
    public function tasksPool() {
        $text = array(
            'title' => '任务统计',
            'fillTabsLeft' => '支部列表',
            'fillTabsRight' => '个党支部',
        );
        $this->assign($text);

        $keyword = I('get.keyword');
        $overdue = 0; // 未完成
        $complete = 0; // 已完成
        if (empty($keyword)) {
            // 获得除测试支部外的支部列表
            $branch = getBranchInfo();
        }else {
            $branch = M('PartyBranch')->field('id,name,cover')->where(array(
                'name' =>  array('like','%'.$keyword.'%')
            ))->select();
        }

        $_branchMembers = M('PartyBranchMembers');
        $_tasksItem = M('tasks_item');
        foreach($branch as $k=>$v){
            if(empty($v['cover'])){
                $branch[$k]['cover'] = '/Public/Mob/images//branch_info/flag.jpg';
            }

            //插入书记名称
            $sj = $_branchMembers->where(array('status'=>'1','branch_id'=>$v['id']))->find();
            $branch[$k]['sj_realname'] = $sj['realname'];
            //插入委员名称
            $newwy = array();
            $wy = $_branchMembers->where(array('branch_id'=>$v['id']))->field('realname')->select();
            foreach($wy as $key=>$item){
                if($item['realname'] == $sj['realname']){  //如果这个委员  还是书记  把他放前面
                    array_unshift($newwy,$item['realname']);
                }else{
                    $newwy[$key] = $item['realname'];
                }
            }
            if($wy){
                $realname_list = array_unique($newwy);//去除名字相同的元素，只留一个（存在场景：一个人同时担任多个职务）
            }else{
                $realname_list = null;//如果成员表没有数据  则为空
            }
            $tasksStatus = $_tasksItem->where(array(
                'publish_branch' => $v['id'],
                'status' => array('in', '1,2')
            ))->getField('status',true);

            $branch[$k]['wy'] = $realname_list;

            $countValues = array_count_values($tasksStatus);
            $branch[$k]['total'] = count($tasksStatus);     // 总数
            $branch[$k]['overdue'] = intval($countValues[2]); // 未完成
            $branch[$k]['complete'] = intval($countValues[1]); // 完成
            $overdue += intval($countValues[2]);
            $complete += intval($countValues[1]);
        }

        $count = array(
            'rightNum' => count($branch),
            'total' => $overdue+$complete,
            'overdue' => $overdue,
            'complete' => $complete,
        );

        // 头部数据统计部分
        $this->assign('count', $count);
        $this->assign('branch', $branch);
        $this->display();
    }


    /**
     * 任务列表
     */
    public function branchTasksPool(){
        $text = array(
            'title' => I('get.branchName'),
            'fillTabsLeft' => '任务列表',
            'fillTabsRight' => '个任务',
        );
        $overdue = 0; // 未完成
        $complete = 0; // 已完成
        $this->assign($text);
        $keyword  = I('get.keyword');
        $status   = I('get.status');
        $branchId = I('get.branchId');

        $_BranchTaskPoolView = D('BranchTaskPool');
        $map = array();
        if (!empty($status)) {
            $map['status'] = $status === '-1' ? array('in', '1,2') : $status;
        }

        if (!empty($branchId)) {
            $map['publish_branch'] = $branchId;
        }else {
            $map['publish_branch'] = array('NEQ', 318);
        }


        $branchTasks = $_BranchTaskPoolView->tasksPoolListByBranch($map);

        $branchTasks['count']['branchId'] = $branchId;
        // ajaxMsg(0, '', $branchTasks);
        $this->assign('monitor', uid());
        $this->assign($branchTasks);
        $this->display();
    }

    /**
     * 任务审核
     */
    public function tasksVerify(){
        $uid = uid();
        $map = array(
            'uid' => $uid,
            'status' => array('in', '1,2,3') // 结束 进行中 待开始
        );
        $statusTxt = array(1=> '已完成', 2 => '进行中', 3 => '待开始');
        $myPublishTasks = D('BranchTaskPool')->tasksPoolListByBranch($map);
        $this->assign('statusTxt', $statusTxt);
        $this->assign($myPublishTasks);
        $this->display();
    }

    /**
     * 状态操作
     * @return [type] [description]
     */
    public function statusAction(){
        $id = I('post.id');
        $status = I('post.status');
        $_TasksItem = D('Admin/TasksItem');
        // 验证用户一致性
        $uid = $_TasksItem->field('uid')->find($id);
        if ($uid['uid'] !== uid()) {
            ajaxMsg(1, '无权操作！非发布者本人');
        }

        $bool = $_TasksItem->ajaxStatus($id, $status);
        if (true === $bool){
            ajaxMsg(0, 'success');
        }else {
            ajaxMsg(1, '操作失败，请重试！！');
        }
    }

}
