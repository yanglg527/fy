<?php
namespace Admin\Controller;

use Admin\Model\AdminAuthRuleViewModel;
use Common\Controller\BaseController;
use Mob\Model\ReviiewViewModel;
use Think\Controller;
use Admin\Util\AdminUtil;

/**
 * 文章管理
 * Class ContentController
 * @package Home\Controller
 */
class NoteController extends BaseAdminController
{

    function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->set_sidebar_module('App');
        $this->set_sidebar_sub('Note');
    }

    public function index()
    {
        $keyword = I('keyword');
        $start_date = I('get.start_date');
        $end_date = I('get.end_date');

        $search['start_date'] = $start_date;
        $search['end_date'] = $end_date;
        $search['keyword'] = $keyword;
        $this->assign('search', $search);

        $startDate = $start_date == "" ? 0 : strtotime($start_date);
        $endDate = $end_date == "" ? time():strtotime($end_date)+(24*60*60);

        // 统计笔记数量
        $noteCount = D('Notes')->where("create_time > $startDate and create_time < $endDate and status > -1")->count();
        $this->assign('note_count', $noteCount);
        $note1Count = D('Notes')->where("type = 0 and create_time > $startDate and create_time < $endDate and status > -1")->count();
        $this->assign('note1_count', $note1Count);
        $this->assign('note2_count', $noteCount-$note1Count);
        // 统计学时
        $timeSum = D('Notes')->where("create_time > $startDate and create_time < $endDate and status > -1")->sum('study_time');
        $timeSum = $timeSum == "" ? 0 : $timeSum;
        $this->assign('time_sum', $timeSum);
        $time1Sum = D('Notes')->where("type = 0 and create_time > $startDate and create_time < $endDate and status > -1")->sum('study_time');
        $time1Sum = $time1Sum == "" ? 0 : $time1Sum;
        $this->assign('time1_sum', $time1Sum);
        $this->assign('time2_sum', $timeSum-$time1Sum);

        $map = array();
        if ($keyword) {
            $map['PartyBranch.name'] = array('like', "%$keyword%");
        }

        $page = D('PartyBranchView')->find_page($map, "", "PartyBranch.id", "PartyBranch.id asc", 15);
//        exit('branch = '.to_json_string($page) . "keyword = ".$keyword);
        // 遍历筛选出来的支部，统计各支部的成员数量
        foreach ($page['list'] as $index => $item) {
            // 统计各支部党员数量
            $itemid = $item['id'];  // 支部id
            // 统计支部成员数量
            $item['member_count'] = D('User')->where("branch_id=$itemid")->count();
            // 统计支部笔记数量
            $item['note_count'] = D('Notes')->where("create_time > $startDate and create_time < $endDate and branch_id=$itemid and status > -1")->count();
            // 统计支部学时
            $item['time_sum'] = D('Notes')->where("create_time > $startDate and create_time < $endDate and branch_id=$itemid and status > -1")->sum('study_time');
            $item['time_sum'] = $item['time_sum'] == "" ? 0 : $item['time_sum'];
            $page['list'][$index] = $item;
        }

        $this->assign('page', $page);

        $this->display();
    }

    public function branch_note()
    {
        $keyword = I('keyword');
        $start_date = I('get.start_date');
        $end_date = I('get.end_date');

        $search['start_date'] = $start_date;
        $search['end_date'] = $end_date;
        $search['keyword'] = $keyword;

        $startDate = $start_date == "" ? 0 : strtotime($start_date);
        $endDate = $end_date == "" ? time():strtotime($end_date)+(24*60*60);

        $auth = AdminUtil::auth();
        $map = array();
        $map['Notes.status'] = array('gt', -1);
        $map['Notes.create_time'] = array( array('gt', $startDate), array('lt', $endDate));

        if ($auth == 3) {
            $branch = AdminUtil::auth_branch();
            $branchId = $branch['id'];
            $map['Notes.branch_id'] = $branch['id'];
        }else{
            $branchId = I('branch_id');
            $branch = D('PartyBranch')->find($branchId);
            $this->assign('branch', $branch);
            $map['Notes.branch_id'] = $branchId;
            $search['branch_id'] = $branchId;
        }
        $this->assign('search', $search);

        // 统计笔记数量
        $noteCount = D('Notes')->where("branch_id = $branchId and create_time > $startDate and create_time < $endDate and status > -1")->count();
        $this->assign('note_count', $noteCount);
        $note1Count = D('Notes')->where("branch_id = $branchId and type = 0 and create_time > $startDate and create_time < $endDate and status > -1")->count();
        $this->assign('note1_count', $note1Count);
        $this->assign('note2_count', $noteCount-$note1Count);
        // 统计学时
        $timeSum = D('Notes')->where("branch_id = $branchId and create_time > $startDate and create_time < $endDate and status > -1")->sum('study_time');
        $timeSum = $timeSum == "" ? 0 : $timeSum;
        $this->assign('time_sum', $timeSum);
        $time1Sum = D('Notes')->where("branch_id = $branchId and type = 0 and create_time > $startDate and create_time < $endDate and status > -1")->sum('study_time');
        $time1Sum = $time1Sum == "" ? 0 : $time1Sum;
        $this->assign('time1_sum', $time1Sum);
        $this->assign('time2_sum', $timeSum-$time1Sum);

        if ($keyword) {
            $map['User.realname'] = array('like', "%$keyword%");
        }

        $page =D('NotesView')->findPage($map, 15, 'create_time desc');
        $this->assign('page', $page);
        $this->display();
    }

    public function detail()
    {
//        $auth = AdminUtil::auth();
        //默认是1
        $auth = 1;
        if($auth == 1){
            $id = I('id');
            $detail =  D('NotesView')->find($id);
            $this->assign('detail', $detail);
        }elseif($auth == 3){
            $id = I('id');
            $branch = AdminUtil::auth_branch();
            $detail =  D('NotesView')->where(array('Notes.id'=>$id, 'Notes.branch_id'=>$branch['id']))->find();
            $this->assign('detail', $detail);
        }

        $this->display();
    }

    public function ajaxDelete($id = 0){
        if($id > 0){
            $note = D('Notes')->find($id);
            if($note){
                $note['status'] = -1;
                D('Notes')->save($note);
                ajaxMsg(0, '已删除');
            }
        }
        ajaxMsg(-1, '笔记不存在');
    }

}