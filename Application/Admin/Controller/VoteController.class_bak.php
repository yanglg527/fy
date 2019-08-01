<?php
namespace Admin\Controller;

use Admin\Model\AdminAuthRuleViewModel;
use Common\Controller\BaseController;
use Think\Controller;

/**
 * 文章管理
 * Class ContentController
 * @package Home\Controller
 */
class VoteController extends BaseAdminController
{

    function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->set_sidebar_module('App');
        $this->set_sidebar_sub('Vote');
    }

    public function index()
    {

        $admin = admin();
        $auth = session_auth();
        $map = array();
        if ($auth == 2) { //总支
            $map['Vote.branch_hq_id'] = $admin['admin_branch_hq_id'];
        }
        if ($auth == 3) { //支部
            $map['Vote.branch_id'] = $admin['admin_branch_id'];
        }

        $map['Vote.status'] = array('gt', -1);

        $keyword = I('keyword');
        if ($keyword) {
            $map['Vote.title'] = array('like', '%' . $keyword . '%');
        }

        $this->assign('search', array('keyword' => $keyword));
        $this->assign('page', D('VoteView')->findPage($map, 15, 'id desc'));
        $this->display();
    }

    public function manage($id = 0)
    {
        if ($id > 0) {
            $condition['id'] = $id;
            $detail = D('Vote')->where($condition)->find();

            $totalCount = D('Vote/VoteLogView')->where(array('VoteItem.vote_id' => $id))->count();
            $people = D('Vote/VoteLogView')->where(array('VoteItem.vote_id' => $id))->group('VoteLog.uid')->count();
            $people = $people == 0 ? 1 : $people;
            $detail['people_count'] = $totalCount / $people;
            $detail['total_count'] = $totalCount;



            $this->assign('vote_roles', D('VoteRole')->where(array('vote_id' => $id))->select());
            $this->assign('items', D('VoteItem')->where(array('vote_id' => $id))->select());
            $this->assign('detail', $detail);
        }
        $this->display();
    }

    public function edit($id = 0)
    {
        if ($id > 0) {
            $condition['id'] = $id;
            $detail = D('Vote')->where($condition)->find();
            $this->assign('vote_roles', D('VoteRole')->where(array('vote_id' => $id))->select());
            $this->assign('items', D('VoteItem')->where(array('vote_id' => $id))->select());
            $this->assign('visiblerange', array(1 => '正常完成时显示', 2 => '投票结束后显示', 3 => '管理员可见', 4 => '党建办可见',));
            $supervisors = D('VoteSupervisorView')->where(array('VoteSupervisor.vote_id' => $id))->getField('realname', true);
            $detail['supervisor'] = implode(',', $supervisors);
            $this->assign('detail', $detail);
        }
        $role = M('Role')->select();
        array_push($role, array('id'=>6,'name'=>'党务工作者'));
        // ajaxMsg(0, '', $detail);
        $this->assign('p', I('get.p', 1));
        $this->assign('allUser', getUsersBystatus(1));
        $this->assign('branchs',getBranchInfo()); 
        $this->assign('roles', D('Role')->select());

        $dwmsg = D('PartyBranchMembersView')->order('branch_sort desc')->select();
        foreach($dwmsg as $k=>$v){
        
            $dw_list[$k]['name'] = $v['realname'].'-'.$v['branch_name'].'-'.$v['post_name'];
        }
        $this->assign('dw_list', $dw_list);

        $rolemsg = D('UserView')->where(array('User.status'=>1,'User.uid'=>array('neq',1)))->order('branch_sort desc')->select();
           
        foreach($rolemsg as $k=>$v){
           
            $role_list[$k]['name'] = $v['realname'].'-'.$v['branch_name'].'-'.$v['role_name'];
        }
        $this->assign('role_list', $role_list);
        $this->display();
    }

    public function ajaxDecVote($id = 0)
    {

        $detail = D('Vote')->where(array('id' => $id))->find();
        if (checkAuth($detail['branch_id']) > 0) {
            D('Vote')->where(array('id' => $id))->save(array('status' => -1));
            ajaxMsg(0, '保存成功');
        }
    }

    #新增文章 ajax提交
    public function ajaxSave()
    {
        // ajaxMsg(0, 'ajaxSave', I());
        //监督员数组转为逗号分隔字符串
        $jd = I('jd');
        $id = I('id');
        $title = I('title');
        $cover = I('cover');
        $limited_time = I('limited_time');
        $multiply_count = I('multiply_count', 1);
        $content = $_POST['content'];
        $status = I('status', 1);
        // 支部
        $branchs = I('branchs');

        if (!$title && !$id) {
            ajaxMsg(1, '请先填写标题');
        }
        if (!$limited_time) {
            ajaxMsg(1, '请先选择截止时间');
        }

        $items = I('items');
        $roles = I('roles');
        $size = sizeof($items);
        $sizeRole = sizeof($roles);
        if ($size < 2 && !$id) {
            ajaxMsg(1, '选项必须大于2个');
        }


        if (!$roles && !$id) {
            ajaxMsg(1, "查看限制至少选择1个");
        }
        if ($sizeRole < 1 && !$id) {
            ajaxMsg(1, '查看限制至少选择1个');
        }
        foreach ($items as $item) {
            if (!$item['title'] && !$id) {
                ajaxMsg(1, '选项内容还没填写');
            }
        }

        if ($id) {
            $array = array(
                'limited_time' => strtotime($limited_time),
                'status' => $status,
            );
            $detail = D('Vote')->find($id);
            if (!$detail) {
                ajaxMsg(1, "找不到该项目");
            }
            $auth = checkAuth($detail['branch_id']);
            if ($auth > 0) {
                D('Vote')->where(array('id' => $id))->save($array);
            }
        } else {
            $array = array(
                'limited_time' => strtotime($limited_time),
                'cover' => $cover == '' ? null : $cover,
                'title' => $title,
                'status' => $status,
                'content' => $content,
                'visiblerange' => I('visiblerange'),
                'multiply_count' => $multiply_count > 1 ? $multiply_count : 1,
                'is_multiply' => $multiply_count > 1 ? 1 : 0,
            );

            $auth = session_auth();
            if ($auth > 0) {
                $array['published_time'] = time();
                $array = set_save_auth($array);
                //                ajaxMsg(1,to_json_string($roles));
                $id = D('Vote')->add($array);

                $supervisor = array();
                foreach ($jd as $key => $value) {
                    $supervisor[$key]['vote_id'] = $id;
                    $supervisor[$key]['uid'] = $value;
                }
                M('VoteSupervisor')->addAll($supervisor);
                // 可参与投票的支部
                $branchsArr = array();
                foreach ($branchs as $key => $value) {
                    $branchsArr[$key]['vote_id'] = $id;
                    $branchsArr[$key]['branch_id'] = $value;
                }
                M('VoteLimitBranch')->addAll($branchsArr);
                foreach ($items as $item) {
                    D('VoteItem')->add(array(
                        'vote_id' => $id, 'title' => $item['title'], 'image' => $item['image'] == '' ? null : $item['image'], 'count' => 0,
                        'sort' => $item['sort']
                    ));
                }

                foreach ($roles as $item) {
                    D('VoteRole')->add(array('vote_id' => $id, 'role_id' => $item));
                }
            }
        }
        ajaxMsg(0, '保存成功');
    }

 

    public function ajaxGetSelect()
    {
        # code...
        $type = I('type');
        switch ($type) {
            case 'branch':
                  $list = getBranchInfo();
                break;
            case 'dw':
                    $msg = D('PartyBranchMembersView')->order('branch_sort desc')->select();
                    foreach($msg as $k=>$v){

                        $list[$k]['name'] = $v['realname'].'-'.$v['branch_name'].'-'.$v['post_name'];
                    }
                break;
            case 'role':
            $msg = D('UserView')->where(array('User.status'=>1,'User.uid'=>array('neq',1)))->order('branch_sort desc')->select();
           
            foreach($msg as $k=>$v){
               
                $list[$k]['name'] = $v['realname'].'-'.$v['branch_name'].'-'.$v['role_name'];
            }
                break;
        }
        if($list){
            ajaxMsg(0, '获取数据成功',$list);
        }else{
            ajaxMsg(1, '获取数据出错');
        }
    }
}