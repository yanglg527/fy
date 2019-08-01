<?php
/**
 * 三会一课 控制器
 */
namespace Api\Controller;

use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;
use Think\Image;
class MeetingController extends BaseAuthController
{
    const fathername = 'meeting';
    const meetingtype = array('党员大会', '支委会', '党小组会', '党课');
    protected $domain;
    protected $userInfo;

    function _initialize()
    {
        parent::_initialize();
        $this->domain = C('DOMAIN');
        $userInfo = user();
        $this->userInfo = $userInfo;
    }
    /**
     * 获取所有用户
     */
    public function getAllUser()
    {
        checkRequestType('get');
        $users = getUsersBystatus();
        if ($users) {
            responseSuccess($users);
        }
        responseError('无数据', 404);
    }

    /**
     * 发布初始数据
     * @return [type] [description]
     */
    public function initMeetingData()
    {
        checkRequestType('get');
        $user = $this->userInfo;
        $branchId = $user['branch_id'];
        $data = array(
            'meetingtype' => self::meetingtype,
            'branchs' => array(
                'id' => $branchId,
                'name' => $user['branch_name']),
            'users' => getUserListByBranch($branchId),
            'tasks' => getTaskaListByStatus(1),
        );
        responseSuccess($data);
    }

    public function initEditData()
    {
        checkRequestType('get');
        $id = I('get.id');
        $info = D('Admin/ThreeMeeting')->editInfo($id);

        $res['id'] = $info['id'];
        $res['meetingType'] = $info['meeting_type'];
        $res['organization'] = $info['branch_id'];
        $res['tasks_id'] = $info['tasks_id'];
        $res['date'] = $info['start_time'];
        $res['address'] = $info['place'];
        $res['groupName'] = $info['party_group_name'];
        $res['theme'] = $info['theme'];
        $res['presenter'] = $info['host_id'];
        $res['recorder'] = $info['record_id'];
        $res['issue'] = $info['remarks'];
        $res['acticle'] = $info['content'];
        $res['saveType'] = $info['status'];
        $res['files'] = $info['files'];

        if (!empty($info)) {
          $user = M('user')->field('uid,realname')
            ->where(array('branch_id'=> $info['branch_id']))->select();

          foreach ($info['attend'] as $value) {
            $attends[$value['attend_type']][] = $value['uid'];
          }

          $res['sitIn'] = $attends[3]; // 列席
          if (!empty($res['sitIn'])) {
            $res['init_sitIn'] = getUsersBystatus();
          }

          $res['attend'] = $attends[2]; // 出席
          $res['leave'] = $attends[1]; // 请假
          $res['absent'] = $attends[0]; // 缺勤
          unset($info);
        }
        responseSuccess($res);
    }


    /**
     * 保存数据
     */
    public function ajaxSave()
    {
        checkRequestType('post');
        $post['uid'] = uid();
        $post['id'] = I('post.id');
        $post['meeting_type'] = I('post.meetingType');
        $post['branch_id'] = I('post.organization'); // 支部ID
        $post['tasks_id'] = I('post.tasks_id'); // 支部ID
        $post['start_time'] = I('post.date'); // 召开日期
        $post['place'] = I('post.address'); // 地址
        $post['party_group_name'] = I('post.groupName'); // 党小组名称
        $post['theme'] = I('post.theme');
        $post['host_id'] = I('post.presenter'); // 主持人
        $post['record_id'] = I('post.recorder'); // 记录人
        $post['remarks'] = I('post.issue'); // 会议议题
        $post['cx'] = explode(',', I('post.attend')); // 出席
        if (!empty(I('post.sitIn'))) {
            $post['lx'] = explode(',', I('post.sitIn')); // 列席
        }
        if (!empty(I('post.leave'))) {
            $post['qj'] = explode(',', I('post.leave')); // 请假
        }
        if (!empty(I('post.absent'))) {
            $post['qx'] = explode(',', I('post.absent')); // 缺席
        }
        $post['content'] = I('post.acticle'); // 会议内容
        $post['status'] = I('post.saveType'); // 状态

        $post['addFileId'] = I('post.files'); // 附件
        $post['delFileId'] = I('post.deleteFiles'); // 删除附件
        include_once('Application/Admin/Common/function.php');
        $_meeting = D('Admin/ThreeMeeting');
        $res = $_meeting->addOrupdate($post);
        if (is_array($res) && isset($res['id'])) {
            responseSuccess($res);
        }
        responseError($res, 401);
    }

    /**
     * 列表页
     */
    public function index()
    {
        checkRequestType('get');
        $keyword = I('get.keyword');
        $type = I('get.type', 'm'); // 类型:月 m、年 y、笔记 r
        $p = I('get.p', 1); // 页码
        $map['ThreeMeeting.status'] = array('eq',2);
        // logs('三会一课列表-keyword='.$keyword. '&type='.$type);
        // 排序
        $order = 'create_at desc';
        switch ($type) {
            case 'y':
                // 一年数据记录
                $map['ThreeMeeting.create_at'] = array(
                     array('egt',strtotime(date('Y-01-01',time()))),
                     array('lt',strtotime(date('Y-01-01',time()).'+1 year'))
                );
                break;
            case 'r':
                // 按流量
                $order = 'pageviews desc,create_at';
                break;
            default:
                // 一月内
                $map['ThreeMeeting.create_at'] = array(
                    //date里不加d,显示本月1号
                   array('egt', strtotime(date('Y-m', time()))),
                   array('lt', strtotime(date('Y-m', time()).'+1 month'))
                );
                break;
        }
        if (!empty($keyword)) {
            $map['ThreeMeeting.theme'] = array('like', '%' . $keyword . '%');
        }

        // 是否拥有发布权限 0 OR 1
        $res['allowcreate'] = checkPublishPermiss();
        $list = D('ThreeMeetingView')
        ->where($map)
        ->page("$p,10")
        ->order($order)
        ->select();
        if (!empty($list)) {
            $meetingType = self::meetingtype;
            foreach ($list as $key => $value) {
                $list[$key]['likenum']= intval($value['likenum']);
                $list[$key]['comment']= intval($value['comment']);
                $list[$key]['collection']= intval($value['collection']);
                $list[$key]['pageviews']= intval($value['pageviews']);

                $list[$key]['meeting_type_id']=$value['meeting_type'];
                $list[$key]['meeting_type']=$meetingType[$value['meeting_type']];
                $list[$key]['create_at'] = date('Y-m-d', $value['create_at']);
            }

            $res['meetinglist'] = $list;
            responseSuccess($res);
        }
        responseSuccess($res,'无数据',404);
    }

    /**
     * 草稿箱
     */
    public function drafts()
    {
        checkRequestType('get');
        $draftList = M('ThreeMeeting')
        ->field('id,meeting_type,theme,create_at')
        // 自己发布并且状态为0
        ->where(array('uid' => $this->userInfo['uid'], 'status' =>0))
        ->order('create_at desc')
        ->select();
        if ($draftList) {
            $meetingType = self::meetingtype;
            foreach ($draftList as $k => $v) {
                $draftList[$k]['meeting_type'] = $meetingType[$v['meeting_type']];
                $draftList[$k]['create_at'] = date('Y-m-d', $v['create_at']);
            }
            responseSuccess($draftList);
        }
        responseError('无数据',404);
    }

    /**
     * 详情
     */
    public function detail()
    {
        checkRequestType('get');
        $meetingType = self::meetingtype;
        $id = I('get.id');
        if (empty($id)) responseError('缺少必须参数', 401);
        $detail = D('MeetingDetailView')->find($id);
        if ($detail) {
            $detail['meeting_type'] = $meetingType[$detail['meeting_type']];
            $detail['create_at'] = date('Y-m-d', $detail['create_at']);
            $res = D('ThreeMeetingAttendView')
            ->where(array('ThreeMeetingAttend.meeting_id' => $id))->select();
            foreach ($res as $key => $value) {
                $attend_type[$value['attend_type']][]=$value;
            }
            $detail['qx'] = $attend_type[0];
            $detail['qj'] = $attend_type[1];
            $detail['cx'] = $attend_type[2];
            $detail['lx'] = $attend_type[3];
            $_files= M('Files');
            $files = $_files->field('files_path,former_name,filetype')
            ->where(array(
                'father_name' => self::fathername,
                'father_id' => $id,
                'files_status' => 1,
            ))->select();
            foreach ( $files as $key=>$item){
                $url = urlencode($item['files_path']);
                $files[$key]['files_path_yulan'] = 'https://view.officeapps.live.com/op/view.aspx?src='.$_SERVER['HTTP_HOST'].$url;
            }
            $detail['files'] = $files;

            $detail['comment_list']=D('ThreeMeetingComment')->meetingCommentListById($id);
            // 坑爹的信箱留言
            $detail['mailbox'] = array();
            if (!empty($detail['tasks_id'])) {
                $TasksMails= D('TasksMailboxView')
                ->where(array(
                    'TasksMailbox.tasks_id' => intval($detail['tasks_id'])
                ))->order('create_at desc')->select();
                $img = 'http://shp.qpic.cn/bizmp/BK28ORPnUtSD0DRYfhmhCBRGTB0LoALuuLQ1ccibvSymicFjHUAgibTmA/';
                if ($TasksMails) {
                    foreach ($TasksMails as $k => $v) {
                        $TasksMails[$k]['date'] = date('Y-m-d H:i', $v['date']);
                    }
                    $detail['mailbox'] = $TasksMails;
                }
            }
            addPageviews($id);
            responseSuccess($detail);
        }
        responseError('无数据',404);
    }

    /**
     * 添加评论
     */
    public function addComment()
    {
        checkRequestType('post');
        // $rws_post = file_get_contents('php://input');
        // $mypost = json_decode($rws_post);
        // $meetingId = $mypost->id;
        // $content = $mypost->comment;
        $meetingId = I('post.id');
        $content = I('post.comment');
        $res = D('ThreeMeetingComment')->addComment($meetingId, $content);
        if (intval($res)) {
            responseSuccess(array('id' =>$res), '添加评论成功');
        }
        responseError($res);
    }

    /**
     * 删除评论
     */
    public function delComment()
    {
        checkRequestType('post');
        $meetingId = I('post.id');
        if (!$meetingId) responseError('缺少必须参数');
        $res = D('ThreeMeetingComment')->delComment($meetingId);
        if (true ===$res) {
            responseSuccess('删除评论成功');
        }
        responseError($res.' 删除评论失败', 403);
    }

    /**
     * 点赞管理
     */
    public function likeAction()
    {
        checkRequestType('post');
        $meetingId = intval(I('post.id'));
        $isLike = I('post.is_like');
        $res = D('ThreeMeetingLike')->runLikeAction($meetingId, $isLike);
        if (intval($res)) {
            responseSuccess('操作成功');
        }
        responseError($res);
    }

    /**
     * 收藏管理
     */
    public function collectionAction()
    {
        checkRequestType('post');
        $meetingId = intval(I('post.id'));
        $isCollection = I('post.is_collection');
        $res = D('ThreeMeetingTibet')->runCollectionAction($meetingId, $isCollection);
        if (intval($res)) {
          responseSuccess('操作成功');
        }
        responseError($res);
    }

    /**
     * 附件上传 完成
     */
    public function ajaxUploadAnnex()
    {
        checkRequestType('post');
        $res = uploadAnnex(self::fathername, self::fathername);
        responseSuccess($res);
    }

    /**
     * 按支部获取成员列表
     */
    public function getMemberListByBranch()
    {
        checkRequestType('get');
        $branchId = intval(user()['branch_id']);
        $users = getUserListByBranch($branchId);
        responseSuccess($users);
    }

}
