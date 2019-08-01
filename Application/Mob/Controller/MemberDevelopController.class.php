<?php

/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */

namespace Mob\Controller;

use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;
use Common\Util\FileUploadUtil;

class MemberDevelopController extends BaseAuthController
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function ajax_upload_file()
    {
        $file = FileUploadUtil::upload("file/", 'file');
        $id = D('UserFile')->add(array('name' => $file['file_name'], 'path' => $file['save_path'], 'create_time' => time(), 'uid' => uid()));
        $file['id'] = $id;
        ajaxMsg(0, "success", $file);
    }

    public function get_list()
    {
        $branch_id = I('branch_id', 'all');
        $role_id = I('role_id', 5);
        $lastItem = I('lastItem', 1000000);
        $search['User.role_id'] = $role_id;
        $user = user();
        if ($branch_id != 'all') {
            $search['User.branch_id'] = $branch_id;
        }
        $search['User.status'] = 1;
        $search['User.is_admin'] = 0;
        $search['uid'] = array('lt', $lastItem);

        $list = D('UserView')->where($search)->order('User.uid desc')->limit(20)->select();
        ajaxMsg(0, '', $list);
    }

    public function ajax_branch()
    {
        ajaxMsg(0, '', D('PartyBranch')->order('id asc')->select());
    }

    public function index2()
    {
        $this->check_wx_redirect('mob_party_member_index');
        //判断是否重定向登录
        $user = user();

        if ($user['role_id'] > 1) {
            $search = array('User.uid' => array('not in', $user['uid']));
        }

        //群众
        //		$search['User.role_id'] = 5;
        //		$list1 = D('UserView') -> where($search) -> order('User.score desc') -> select();
        //积极分子
        $search['User.role_id'] = 4;
        $list2 = D('UserView')->where($search)->order('User.score desc')->select();
        //重点发展对象
        $search['User.role_id'] = 3;
        $list3 = D('UserView')->where($search)->order('User.score desc')->select();
        //预备党员
        $search['User.role_id'] = 2;
        $list4 = D('UserView')->where($search)->order('User.score desc')->select();
        if ($user['role_id'] > 1) {
            if ($user['role_id'] == 5) {
                array_unshift($list1, $user);
            } elseif ($user['role_id'] == 4) {
                array_unshift($list2, $user);
                array_unshift($list3, $user);
            } elseif ($user['role_id'] == 2) {
                array_unshift($list4, $user);
            }
        }
        $this->assign('branchs', D('PartyBranch')->order('id asc')->select());

        //		$this -> assign('list1', $list1);
        $this->assign('list2', $list2);
        $this->assign('list3', $list3);
        $this->assign('list4', $list4);
        $this->display();
    }
    public function normShow()
    {
        //党员发展规则
        $normlist = D('Norm')->where(array('status' => 1))->order('order_id')->select();
        foreach ($normlist as $k => $v) {
            $normlist[$k]['content'] = preg_replace('/style=".*?"/', '', $v['content']);
        }
        $this->assign('normlist', $normlist);
        $this->display();
    }
    /**
     * 首页
     */
    public function index()
    {
        $this->check_wx_redirect('mob_party_member_index');
        //判断是否重定向登录
        $user = user();

        if ($user['role_id'] > 1) {
            $search = array('User.uid' => array('not in', $user['uid']));
        }
        //党员发展规则
        $normlist = D('Norm')->where(array('status' => 1))->select();

        $search['User.status'] = 1;
        //群众
        $search['User.role_id'] = 5;
        $list_qz = D('UserView') -> where($search) -> order('User.score desc') -> select();
        $count['qz'] = D('User')->where(array('role_id' => 5,'status'=>1))->count();
        //积极分子
        $search['User.role_id'] = 4;
        $list_jj = D('UserView')->where($search)->order('User.score desc')->select();
        $count['jj'] = D('User')->where(array('role_id' => 4,'status'=>1))->count();
        //重点发展对象
        $search['User.role_id'] = 3;
        $list_zd = D('UserView')->where($search)->order('User.score desc')->select();
        $count['zd'] = D('User')->where(array('role_id' => 3,'status'=>1))->count();
        //预备党员
        $search['User.role_id'] = 2;
        $list_yb = D('UserView')->where($search)->order('User.score desc')->select();
        $count['yb'] = D('User')->where(array('role_id' => 2,'status'=>1))->count();

        //转到正式党员名单
        $list_md = array();
        $user_role_log = D('UserRoleLog')->where(array('new_role'=>1))->group('uid')->order('conversion_time desc')->select();
        $count['md'] = count($user_role_log);
        if ($user_role_log) {
            foreach ($user_role_log as $k=>$v) {
                $in_list[] = $v['uid'];
            }
            $list_md = D('UserView')->where(array('uid'=>array('in',$in_list)))->order('User.score desc')->select();
        }

        if ($user['role_id'] > 1) {
            if ($user['role_id'] == 5) {
                array_unshift($list_qz, $user);
            } elseif ($user['role_id'] == 4) {
                array_unshift($list_jj, $user);
            } elseif ($user['role_id'] == 3) {
                array_unshift($list_zd, $user);
            } elseif ($user['role_id'] == 2) {
                array_unshift($list_yb, $user);
            }
        }
        $this->assign('branchs', D('PartyBranch')->order('id asc')->select());

        $this->assign('count', $count);
		$this -> assign('list_md', $list_md);
        $this -> assign('list_qz', $list_qz);
        $this->assign('list_jj', $list_jj);
        $this->assign('list_zd', $list_zd);
        $this->assign('list_yb', $list_yb);
        $this->assign('normlist', $normlist);
        $this->display();
    }

    public function zone()
    {
        $id = I('id');
        $detail = D('Common/UserView')->where(array('User.uid' => $id))->find();
       
        $score = $detail['score'];
        $sort = D('User')->where(array('score' => array('gt', $score)))->count();
        $current_user = user();
      
        //判断是否为管理员，暂时管理员能修改人员的
        $check_auth = D('PartyBranchMembers')->where(array('uid'=>$current_user['uid'],'status'=>3,'branch_id'=>$current_user['branch_id']))->find();
        $this->assign('check_auth',$check_auth);
        //获取用户积分排名
        $tz_count = D('Taizhang')->where(array('uid' => $detail['uid']))->count();
        $detail['score_pm'] = $sort == 0 ? 1 : $sort;
        $detail['tz_count'] = $tz_count;
        $this->assign('detail', $detail);

        $year = date('Y', time());
        $yearTime = strtotime($year . '-1-1');
        $nextYearTime = strtoTime(($year + 1) . '-1-1') - 1;
        $tag1 = D('UserOfficialApply')->where(array('uid' => $id))->find();
        $tag2 = D('UserReportView')->where(array('UserReport.uid' => $id, 'UserReport.status' => array('gt', -1), 'UserReport.create_time' => array('between', array($yearTime, $nextYearTime))))->order('UserReport.create_time desc')->select();
        $tag3 = D('UserExamView')->where(array('UserExam.uid' => $id, 'UserExam.status' => array('gt', -1), 'UserExam.create_time' => array('between', array($yearTime, $nextYearTime))))->order('UserExam.create_time desc')->select();
        $tag4 = D('UserAdviceView')->where(array('UserAdvice.uid' => $id, 'UserAdvice.status' => array('gt', -1), 'UserAdvice.create_time' => array('between', array($yearTime, $nextYearTime))))->order('UserAdvice.create_time desc')->select();

        $this->assign('tag1', $tag1);
        $this->assign('tag2', $tag2);
        $this->assign('tag3', $tag3);
        $this->assign('tag4', $tag4);
        $this->display();
    }

    public function apply()
    {
        $user = user();
        $detail = D('UserOfficialApply')->where(array('uid' => $user['uid']))->find();
        if ($detail['file_ids']) {
            $files = D('UserFile')->where(array('id' => array('in', $detail['file_ids'])))->select();
            $this->assign('files', $files);
        }
        $this->assign('detail', $detail);
        $this->display();
    }

    public function apply_add()
    {
        $user = user();
        $detail = D('UserOfficialApply')->where(array('uid' => $user['uid'], 'status' => array('gt', -1)))->find();
        if ($detail['file_ids']) {
            $files = D('UserFile')->where(array('id' => array('in', $detail['file_ids'])))->select();
            $this->assign('files', $files);
        }
        $this->assign('detail', $detail);
        $this->display();
    }

    public function ajax_save_apply()
    {
        $user = user();
        $detail = D('UserOfficialApply')->where(array('uid' => $user['uid'], 'status' => array('gt', -1)))->find();
        $array = array('title' => I('title'), 'content' => $_POST['content'], 'file_ids' => I('file_ids'), 'update_time' => time());
        if ($detail) {
            D('UserOfficialApply')->where(array('uid' => $user['uid']))->save($array);
        } else {
            $array['status'] = 0;
            $array['uid'] = $user['uid'];
            $array['create_time'] = time();
            D('UserOfficialApply')->add($array);
        }

        ajaxMsg(0, "success");
    }

    public function report()
    {
        $id = I('id');
        $detail = D('UserReport')->where(array('id' => $id))->find();
        if ($detail['file_ids']) {
            $files = D('UserFile')->where(array('id' => array('in', $detail['file_ids'])))->select();
            $this->assign('files', $files);
        }
        $this->assign('detail', $detail);
        $this->display();
    }

    public function report_add()
    {
        $user = user();
        $id = I('id');
        if ($id) {
            $detail = D('UserReport')->where(array('uid' => $user['uid'], 'id' => $id))->find();
            if ($detail['file_ids']) {
                $files = D('UserFile')->where(array('id' => array('in', $detail['file_ids'])))->select();
                $this->assign('files', $files);
            }
            $this->assign('detail', $detail);
        }

        $this->display();
    }

    public function ajax_save_report()
    {
        $user = user();
        $id = I('id');
        $array = array('title' => I('title'), 'file_ids' => I('file_ids'), 'content' => $_POST['content'], 'update_time' => time());
        if ($id) {
            $detail = D('UserReport')->where(array('uid' => $user['uid'], 'id' => $id))->find();
            if ($detail) {
                D('UserReport')->where(array('id' => $id))->save($array);
            } else {
                ajaxMsg(1, "编辑失败");
            }
        } else {
            $array['role_id'] = $user['role_id'];
            $array['uid'] = $user['uid'];
            $array['create_time'] = time();
            D('UserReport')->add($array);
        }

        ajaxMsg(0, "success");
    }

    public function exam()
    {
        $id = I('id');
        $detail = D('UserExam')->where(array('id' => $id))->find();
        if ($detail['file_ids']) {
            $files = D('UserFile')->where(array('id' => array('in', $detail['file_ids'])))->select();
            $this->assign('files', $files);
        }
        $this->assign('detail', $detail);
        $this->display();
    }

    public function exam_add()
    {
        $user = user();
        $id = I('id');
        if ($id) {
            $detail = D('UserExam')->where(array('uid' => $user['uid'], 'id' => $id))->find();
            if ($detail['file_ids']) {
                $files = D('UserFile')->where(array('id' => array('in', $detail['file_ids'])))->select();
                $this->assign('files', $files);
            }
            $this->assign('detail', $detail);
        }

        $this->display();
    }

    public function ajax_save_exam()
    {
        $user = user();
        $id = I('id');

        $array = array('title' => I('title'), 'file_ids' => I('file_ids'), 'score' => I('score'), 'update_time' => time());
        if ($id) {
            $detail = D('UserExam')->where(array('uid' => $user['uid'], 'id' => $id))->find();
            if ($detail) {
                D('UserExam')->where(array('id' => $id))->save($array);
            } else {
                ajaxMsg(1, "编辑失败");
            }
        } else {
            $array['role_id'] = $user['role_id'];
            $array['uid'] = $user['uid'];
            $array['create_time'] = time();
            D('UserExam')->add($array);
        }
        ajaxMsg(0, "success");
    }

    public function advice()
    {
        $id = I('id');
        $detail = D('UserAdvice')->where(array('id' => $id))->find();
        if ($detail['file_ids']) {
            $files = D('UserFile')->where(array('id' => array('in', $detail['file_ids'])))->select();
            $this->assign('files', $files);
        }
        $this->assign('detail', $detail);
        $this->display();
    }

    public function advice_add()
    {
        $user = user();
        $id = I('id');
        if ($id) {
            $detail = D('UserAdvice')->where(array('uid' => $user['uid'], 'id' => $id))->find();
            if ($detail['file_ids']) {
                $files = D('UserFile')->where(array('id' => array('in', $detail['file_ids'])))->select();
                $this->assign('files', $files);
            }
            $this->assign('detail', $detail);
        }

        $this->display();
    }

    public function ajax_save_advice()
    {
        $user = user();
        $id = I('id');
        $array = array('title' => I('title'), 'content' => $_POST['content'], 'file_ids' => I('file_ids'), 'update_time' => time());
        if ($id) {
            $detail = D('UserAdvice')->where(array('uid' => $user['uid'], 'id' => $id))->find();
            if ($detail) {
                D('UserAdvice')->where(array('id' => $id))->save($array);
            } else {
                ajaxMsg(1, "编辑失败");
            }
        } else {
            $array['role_id'] = $user['role_id'];
            $array['uid'] = $user['uid'];
            $array['create_time'] = time();
            D('UserAdvice')->add($array);
        }

        ajaxMsg(0, "success");
    }

    public function ajaxLoad()
    {
        $id = I('id');
        $type = I('type');
        $year = I('year', date('Y', time()));
        $yearTime = strtotime($year . '-1-1');
        $nextYearTime = strtoTime(($year + 1) . '-1-1') - 1;

        if ($type == 'sxhb') {
            $list = D('UserReportView')->where(array('UserReport.uid' => $id, 'UserReport.create_time' => array('between', array($yearTime, $nextYearTime))))->order('UserReport.create_time desc')->select();
        } elseif ($type == 'ksjl') {
            $list = D('UserExamView')->where(array('UserExam.uid' => $id, 'UserExam.create_time' => array('between', array($yearTime, $nextYearTime))))->order('UserExam.create_time desc')->select();
        } elseif ($type == 'advice') {
            $list = D('UserAdviceView')->where(array('UserAdvice.uid' => $id, 'UserAdvice.create_time' => array('between', array($yearTime, $nextYearTime))))->order('UserAdvice.create_time desc')->select();
        }
        ajaxMsg(0, $type, $list);
    }
}
