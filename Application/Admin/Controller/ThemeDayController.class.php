<?php
namespace Admin\Controller;

use Common\Controller\BaseController;
use Think\Controller;

/**
 * 主题党日
 * @author Calvin 2019/1/23
 */
class ThemeDayController extends BaseAdminController
{
    const fathername = 'ThemeDay';
    const basestatus = array('草稿', '已结束', '待开始', '进行中', '已终止');

    function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->set_sidebar_module('ThemeDay');
    }

    /**
     * 列表页
     * @return [type] [description]
     */
    public function index()
    {
        $this->set_sidebar_sub('index');
        $admin = admin();
        $auth = session_auth();
        $map = array();
        $branchId = I('get.branch_id');
        $status = I('get.status');
        $keyword = I('get.keyword');

        // 如果是超级管理员就渲染全部数据
        // 否则只能看自己发的
        if (1 !== intval($admin['uid'])) {
            $map['ThemePartyDay.uid'] = array('eq', $admin['uid']);
        }

        if (is_numeric($status)) {
            $map['ThemePartyDay.status'] = array('eq', $status);
        }else {
            $map['ThemePartyDay.status'] = array('gt', -1);
        }
        if (is_numeric($branchId)) {
            $map['ThemePartyDay.branch_id'] = array('eq', $branchId);
        }
        if ($keyword) {
            $map['ThemePartyDay.theme'] = array('like', '%' . $keyword . '%');
        }
        $lists = D('ThemeDayView')->findPage($map, 15, 'create_at desc');

        $this->assign('brancds', M('PartyBranch')->field('id,name')->select());
        $this->assign('page', $lists);
        $this->assign('search', array('keyword' => $keyword, 'status' => $status, 'branch_id' => $branchId));
        $this->assign('status', self::basestatus);
        $this->display();
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $id = I('get.id');
        $user = array();
        $this->set_sidebar_sub('index');
        $attend = array();
        $info = D('ThemePartyDay')->editInfo($id);
        // echo "<pre>";
        // var_dump($info);exit;
        if (!empty($info)) {
          $user = M('user')->field('uid,realname')
            ->where(array('branch_id'=> $info['branch_id']))->select();

          foreach ($info['attend'] as $value) {
            $attend[$value['attend_type']][] = $value['uid'];
          }
          unset($info['attend']);
        }
        $this->assign('attend', $attend);
        $this->assign('info', $info);
        $this->assign('brancds', getBranchInfo());
        // $this->assign('types', self::learntype);
        $this->assign('users', $user);
        $this->display();
    }

    // 新增
    public function showAdd()
    {
        $this->set_sidebar_sub('index');
        $userBranchId = I('get.branchId', admin()['branch_id']);
        $branchId = I('get.branch_id')?I('get.branch_id'):$userBranchId;
        $user = M('user')->field('uid,realname')->where(array('branch_id'=> $branchId))->select();
        $brancds = M('PartyBranch')->field('id,name')->select();
        // $type = self::learntype;
        $this->assign('users', $user);
        $this->assign('brancds', $brancds);
        $this->assign('branchid', $branchId);
        $this->display();
    }

    /**
     * Ajax 保存数据
     * @return [type] [description]
     */
    public function ajaxSave()
    {
        $post = I('post.data');
        $branchId = admin()['branch_id'];
        $postData['id'] = $post['id'];
        $postData['branch_id'] = intval($post['branchId']) ? intval($post['branchId']) : $branchId;
        $postData['host_id'] = intval($post['host_id']);
        $postData['record_id'] = intval($post['record_id']);
        $postData['effect'] = $post['effect'];
        $postData['content'] = $post['content'];
        $postData['theme'] = $post['theme'];
        $postData['place'] = $post['place'];
        $postData['end_time'] = $post['end_time'];
        $postData['start_time'] = $post['start_time'];
        $postData['status'] = $post['status'];

        $postData['cq'] = $post['cq'];
        $postData['qj'] = $post['qj'];
        $postData['qx'] = $post['qx'];
        $post['file_id'] = explode(',', $post['file_id']);
        $post['surplus_file_id'] = explode(',', $post['surplus_file_id']); // to string

        $postData['addFileId'] = getAddFileLists($post['file_id'], $post['surplus_file_id']);
        $postData['delFileId'] = getDelFileLists($post['file_id'], $post['surplus_file_id']);
        if (2 == intval($postData['status'])) {
            // $nowTime = strtotime(date('Y-m-d')); // 当天零点
            // 日期转时间戳
            $startTime = strtotime($postData['start_time']);
            $endTime = strtotime($postData['end_time']);
            $time = time();
            if ($time > $startTime && $time < $endTime) {
                // 当前时间大于开始时间 并且小于结束时间 进行中...
                $postData['status'] = 3;
            }elseif ($time > $endTime) {
                // 当前时间大于结束时间 已结束
                $postData['status'] = 1;
            }
        }
        $_tpd = D('ThemePartyDay');
        $res = $_tpd->addOrupdate($postData);
        if (isset($res['id'])) ajaxMsg(0, '操作成功');
        ajaxMsg(1, $res);
    }

    /**
     * 附件上传
     */
    public function ajaxUploadAnnex()
    {
        $res = uploadAnnex(self::fathername, self::fathername);
        ajaxMsg(200,'success', $res);
    }

    public function ajaxStatus()
    {
        $id = I('post.id');
        $status = I('post.status');
        $_theme = M('ThemePartyDay');
        $bool = $_theme->where(array('id'=>$id))->setField('status', $status);
        if (!$bool) ajaxMsg(1, '服务器繁忙，请重试..');
        ajaxMsg(0, 'success');
    }
}
