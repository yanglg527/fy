<?php

/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Event\Controller;

use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;
use Common\Util\AuthUtils;

class IndexController extends BaseAuthController
{

    function _initialize()
    {
        parent::_initialize();
        $this->setHeader('活动报名');
    }
    public function activityDisplay()
    {
        $user = user();
        $type_id = I('type_id');
        if (!$type_id) {
            $where['type_id'] = array('in', '1,2,3');
            $this->setTitle('活动展示');
        } else {
            $where['type_id'] = $type_id;
            $this->setTitle(M('event_type')->where(array('id' => $type_id))->getField('name'));
        }
        $where['status'] = 1;
        $list = D("EventRoleView")->where($where)->group('EventRole.event_id')->order("Event.create_time desc")->select();
        //设置特定支部的人员可以看到
        foreach($list as $key=>$item){
            $event_id = $item['event_id'];
            $branch_id = D('EventRole')->where(array('event_id'=>$event_id))->find();
            if($branch_id['branch_id']!== '0'and $branch_id['branch_id'] !== $user['branch_id'] ){
                unset($list[$key]);
            }
        }
        // var_dump($list);
        $head_msg['total'] = count($list);
        $head_msg['finish'] = 0;
        $head_msg['doing'] = 0;
        $doing_list = array();
        $finish_list = array();
        foreach($list as $k =>$v){
            if($v['end_time']<time())
            $head_msg['finish'] =  $head_msg['finish']+1;
            else
            $head_msg['doing'] =  $head_msg['doing']+1;

            if($v['entry_end_time'] + 24*3600 < time()){
                $list[$k]['entry_able'] = 0;
                $finish_list[$k] = $v;
            }else{
                $doing_list[$k] = $v;
                $list[$k]['entry_able'] = 1;
            }
        }
        $this->assign('list', $list);
        $this->assign('doing_list', $doing_list);
        $this->assign('finish_list', $finish_list);
        $this->assign('head_msg', $head_msg);
        $this->display();
    }
    public function index()
    {
        $header_left['url'] = __ROOT__ . "/Home/Activity/index";
        $this->assign('header_left', $header_left);

        $this->check_wx_redirect('event_index');//判断是否重定向登录
        $user = user();
        if (AuthUtils::check_adm_post($user, 1)) {
            $where = array(
                "Event.status" => 1,
             //   'Event.branch_id'=>$user['branch_id']
            );
            $list = D("EventRoleView")->where($where)->group('EventRole.event_id')->order("Event.create_time desc")->select();
            $this->assign('list', $list);
        }
        $this->setTitle('活动列表');
        $this->setHeader('活动列表');
        $this->display();
    }

    public function ajaxEventLoading()
    {
        $user = user();
        $create_time = I('post.create_time');
        if (!$create_time) {
            $create_time = time();
        }
        // 'EventRole.role_id'=>$user['role_id']  1548315033  
        $list = D("EventRoleView")->where(array(
            "Event.status" => 1,
            'Event.start_time' => array('gt', $create_time),
            'Event.entry_end_time' => array('gt', time()),
        ))->group('EventRole.event_id')->order("Event.create_time desc")->limit(10)->select();
        foreach ($list as $index => $item) {
            $list[$index]['content'] = "";
        }
        ajaxMsg(0, 'success', $list);
    }

    public function attend()
    {
        $uid = uid();
        $id = I('id');
        $id = $id ? $id : I('state');
        $this->check_wx_redirect('event_detail', $id);//判断是否重定向登录
        $detail = D('Event')->find(I('id'));
        $attend = D('EventAttend')->where(array('uid' => $uid, 'event_id' => $id, 'status' => 1))->find();
        $this->assign('attend', $attend);
        $this->assign('detail', $detail);
        $this->setTitle('活动报名');
        $this->setHeader('活动报名');
        $this->display();
    }

    public function manager()
    {
        $header_left['url'] = __ROOT__ . "/Event/Index/index";
        $this->assign('header_left', $header_left);

        $id = I('id');
        $id = $id ? $id : I('state');
        $this->check_wx_redirect('event_detail', $id);//判断是否重定向登录

        $detail = D('Event')->find(I('id'));
        $this->assign('detail', $detail);

        $branchs = D('PartyBranch')->select();
        $this->assign('branchs', $branchs);
        $roles = D('Role')->select();
        $this->assign('roles', $roles);

        $detail = D('Event')->find(I('id'));
        $this->setTitle($detail['title']);
        $this->setHeader($detail['title']);
        $this->display();
    }

    public function ajaxAttendLoading()
    {

        $id = I('id');

        $user = user();
        $attend_time = I('post.attend_time');
        if (!$attend_time) {
            $attend_time = time();
        }
        if (AuthUtils::check_adm_post($user, 1)) {
            $where = array(
                'Event.id' => $id,
                "EventAttend.status" => 1,
                'EventAttend.attend_time' => array('lt', $attend_time),
                'Event.branch_id' => $user['branch_id']
            );
            $branch_id = I('branch_id');
            if ($branch_id) {
                $where['User.branch_id'] = $branch_id;
            }
            $attend_role = I('attend_role');
            if ($attend_role) {
                $where['User.role_id'] = $attend_role;
            }
            $sign_in_status = I('sign_in_status', 0);
            if ($sign_in_status != 'all') {
                $where['EventAttend.sign_status'] = $sign_in_status;
            }
            $sign_out_status = I('sign_out_status', 0);
            if ($sign_in_status != 'all') {
                $where['EventAttend.sign_out_status'] = $sign_out_status;
            }


            $list = D("EventAttendView")->where($where)->order("EventAttend.attend_time desc")->limit(10)->select();
            foreach ($list as $index => $item) {
                $list[$index]['content'] = "";
            }
        }
        ajaxMsg(0, 'success', $list);
    }

    public function ajaxAttendSignIn()
    {

        $id = I('id');
        $status = I('status', 0);

        $user = user();
        $attend = D('EventAttend')->find($id);
        if (!$attend || $attend['status'] != 1) {
            ajaxMsg(1, "该用户已退出活动了");
        }
        $event = D('Event')->find($attend['event_id']);

        if ($event['branch_id'] == $user['branch_id'] && AuthUtils::check_adm_post($user, 1)) {
            D('EventAttend')->where(array('id' => $id))->save(array('sign_time' => time(), 'sign_status' => $status));
        }
        ajaxMsg(0, 'success');
    }
    public function ajaxAttendSignOut()
    {

        $id = I('id');
        $status = I('status', 0);
        $user = user();
        $attend = D('EventAttend')->find($id);
        if (!$attend && $attend['status'] == 1) {
            ajaxMsg(1, "该用户已退出活动了");
        }
        $event = D('Event')->find($attend['event_id']);
        if ($event['branch_id'] == $user['branch_id'] && AuthUtils::check_adm_post($user, 1)) {
            D('EventAttend')->where(array('id' => $id))->save(array('sign_out_time' => time(), 'sign_out_status' => $status));
        }
        ajaxMsg(0, 'success');
    }


    public function detail()
    {

        $header_left['url'] = __ROOT__ . "/Event/Index/activityDisplay";
        $this->assign('header_left', $header_left);

        $id = I('id');
        $id = $id ? $id : I('state');
        $this->check_wx_redirect('event_detail', $id);//判断是否重定向登录
        $detail = D('Event')->find(I('id'));
        $detail['read_count'] = $detail['read_count'] + 1;
        D('Event')->save($detail);
        if ($detail['status'] > 0) {
            $id = $detail['id'];
            $attends = D('EventAttendView')->where("EventAttend.status>0 and EventAttend.event_id=$id")->order('EventAttend.attend_time desc')->select();
            $no_attends = D('EventAttendView')->where("EventAttend.status = -5 and EventAttend.event_id=$id")->order('EventAttend.attend_time desc')->select();//不参与的名单   status = -5
            $no_attends_count = count($no_attends);
            $comments = D('EventCommentView')->where(array('event_id'=>$id,'status'=>1))->order('EventComment.comment_time desc')->select();


            $uid = uid();
            $user = user();
            $attend = D('EventAttend')->where("uid=$uid and event_id=$id")->find();
            $status = $attend['status'];
            //判断报名权限
            $attendStatus = 0;//未报名
            if ($attend['status'] == -2 || $detail['entry_end_time']<time()) {//被禁止报名了
                $attendStatus = -2;//没有权限报名
            } elseif ($attend['status'] == 1) {//已经报名了
                $attendStatus = 1;//已经报名了
            }

            $roles = D('EventRoleView')->where(array('EventRole.event_id' => $id))->select();
            $detail['role_limit'] = '';
            $size = sizeof($roles);
            foreach ($roles as $index => $role) {
                if ($index + 1 == $size) {
                    $detail['role_limit'] = $detail['role_limit'] . $role['role_name'];
                } else {
                    $detail['role_limit'] = $detail['role_limit'] . $role['role_name'] . "、";
                }
            }

            $count = 0;
            foreach ($roles as $role) {
                if ($role['role_id'] == $user['role_id']) {
                    $count++;
                }
            }
            if ($count == 0) {
                $attendStatus = -2;//没有权限报名
            }

            $this->assign('status', $status);
            $this->assign('attendStatus', $attendStatus);
            $this->assign('detail', $detail);
            $this->assign('attends', $attends);
            $this->assign('no_attends', $no_attends);
            $this->assign('no_attends_count', $no_attends_count);
            $this->assign('comments', $comments);

            $this->setHeader($detail['title']);
            $this->setTitle($detail['title']);
        } else {
            $this->setHeader("抱歉，找不到该活动");
            $this->setTitle("抱歉，找不到该活动");
        }
        $this->display();
    }

    public function ajaxComment()
    {
        $this->check_wx_redirect();//判断是否重定向登录
        $detail = D('Event')->find(I('id'));
        $content = I('content');
        if (!$content) {

            ajaxMsg(1, "请填写内容");
        }

        if ($detail['status'] > 0) {
            D("EventComment")->add(array('uid' => uid(), 'content' => str_replace(array("\r\n", "\r", "\n"), "<br>", $content), 'comment_time' => time(), 'event_id' => I('id')));
            ajaxMsg(0, "success");
        } else {
            ajaxMsg(1, "抱歉，找不到该活动");

        }
    }


    public function ajaxAttend()
    {
        $this->check_wx_redirect();//判断是否重定向登录
//        ajaxMsg(1, to_json_string($_POST));
        $id = I('id');
        $mobile = I('mobile');
        if (!$mobile) {
            ajaxMsg(1, "请先填写手机号码");
        }


        $detail = D('Event')->find($id);
        if ($detail['status'] > 0) {
            $attendStartTime = $detail['entry_start_time'];
            $attendEndTime = $detail['entry_end_time'];
            $timeNow = time();
            if ($attendStartTime > $timeNow) {
                ajaxMsg(1, "报名时间还没开始");
            }
            if ($attendEndTime < $timeNow) {
                ajaxMsg(1, "报名已结束");
            }
            if ($detail['people_limit'] > 0) {
                if ($detail['attend_count'] >= $detail['people_limit']) {
                    ajaxMsg(1, "抱歉，报名人数已满");
                }
            }

            $user = user();

            $roles = D('EventRole')->where(array('event_id' => $id))->select();
            $count = 0;
            foreach ($roles as $role) {
                if ($role['role_id'] == $user['role_id']) {
                    $count++;
                }
            }
            if ($count == 0) {
                ajaxMsg(1, "你没有该活动报名权限");
            }



            $uid = $user['uid'];
            $attend = D("EventAttend")->where("uid=$uid and event_id=$id")->find();
            if ($attend) {
                if ($attend['status'] == -2) {//被禁止报名了
                    ajaxMsg(1, "你没有该活动报名权限");
                } elseif ($attend['status'] == 1) {//已经报名了
                    ajaxMsg(1, "你已经报名了");
                } elseif ($attend['status'] == -1) {
                    D('EventAttend')->where("uid=$uid and event_id=$id")->save(array(
                        'name' => $user['realname'], 'remark' => I('remark'), 'mobile' => $mobile, 'birthday' => I('birthday'),
                        'position' => I('position'), 'work_unit' => I('work_unit'), 'gender' => I('gender'), 'status' => 1, 'attend_time' => $timeNow
                    ));

                }
            } else {//新加报名
                D("EventAttend")->add(array(
                    'uid' => $user['uid'],
                    'event_id' => $id,
                    'name' => $user['realname'],
                    'remark' => I('remark'),
                    'mobile' => $mobile,
                    'birthday' => I('birthday'),
                    'position' => I('position'),
                    'work_unit' => I('work_unit'),
                    'gender' => I('gender'),
                    'status' => 1,
                    'attend_time' => $timeNow,
                    'create_time'=>$timeNow
                ));
            }
            $detail['attend_count'] = $detail['attend_count'] + 1;
            D('Event')->save($detail);

            ajaxMsg(0, "success");
        } else {
            ajaxMsg(1, "抱歉，找不到该活动");
        }

    }

    //不参与的数据保存  'status' => -5  不参与
    public function ajaxNoAttend()
    {
        $this->check_wx_redirect();//判断是否重定向登录
        $id = I('id');
        $user = user();
        $uid = $user['uid'];
        $timeNow = time();
        $attend = D("EventAttend")->where("uid=$uid and event_id=$id")->find();
        if ($attend['status'] == -5) {
            ajaxMsg(1, "您已经是不参与状态了");
        }
        $result = D("EventAttend")->add(array(
            'uid' => $user['uid'],
            'event_id' => $id,
            'name' => $user['realname'],
            'status' => -5,
            'attend_time' => $timeNow,
            'create_time' => $timeNow
        ));
       if($result){
           ajaxMsg(0, "success");
       }else {
           ajaxMsg(1, "操作失败");
       }
    }
    public function ajaxCancelAttend()
    {
        $this->check_wx_redirect();//判断是否重定向登录
        $id = I('id');
        $detail = D('Event')->find($id);
        if ($detail['status'] > 0) {
            $attendEndTime = $detail['entry_end_time'];
            $timeNow = time();
            if ($attendEndTime < $timeNow) {
                ajaxMsg(1, "报名已截止");
            }
            $user = user();
            $uid = $user['uid'];
            $attend = D("EventAttend")->where("uid=$uid and event_id=$id")->find();
            if ($attend) {
                if ($attend['status'] == -2) {//被禁止报名了
                    ajaxMsg(0, "你已取消报名了");
                } elseif ($attend['status'] == 1) {//已经报名了
                    D('EventAttend')->where("uid=$uid and event_id=$id")->save(array('status' => -1, 'sign_status' => 0, 'sign_out_status' => 0, 'sign_time' => null, 'sign_out_time' => null));
                    $detail['attend_count'] = $detail['attend_count'] - 1;
                    D('Event')->save($detail);
                }
                ajaxMsg(0, "你已取消报名了");
            } else {//新加报名
                ajaxMsg(0, "你已取消报名了");
            }

        } else {
            ajaxMsg(1, "该活动已经取消了");
        }
    }

    public function attenddetail()
    {
        $this->check_wx_redirect('event_index');//判断是否重定向登录
        $this->setHeader('活动详情');
        $this->setTitle('活动详情');
        $this->display();
    }

    public function sign()
    {
        $signCode = I('state');
        $this->check_wx_redirect("event_sign", $signCode);
        $uid = uid();
        $event = D('Event')->where("sign_code='$signCode'")->find();
        if ($event && $event['status'] > 0) {
            $eventId = $event['id'];
            $attend = D('EventAttend')->where("uid=$uid and event_id=$eventId")->find();
            if ($attend) {
                if ($attend['status'] != 1) {
                    $msg = ("抱歉，你已取消活动了");
                } elseif ($attend['sign_status'] == 1) {
                    $msg = ("你已经签过到了");
                } else {
                    D('EventAttend')->where("uid=$uid and event_id=$eventId")->save(array(
                        'sign_status' => 1,
                        'sign_time' => time()
                    ));
                    $msg = ("签到成功");
                }
            } else {
                $msg = ("抱歉，你还没有报名这次活动");
            }
        } else {
            $msg = ("抱歉,没有找到该活动");
        }

        $this->setHeader($event['title'] . '--签到');
        $this->setTitle($event['title'] . '--签到');
        $this->assign('signStatus', $msg);
        $this->assign('signTitle1', '感谢参与本次活动');
        $this->assign('signTitle2', '如有任何疑问，请及时与活动主办方联系');
        $this->assign('signTitle3', '关注企业号，实时掌握活动动态');
        $this->display();
    }


    public function signOut()
    {

        $signCode = I('state');
        $this->check_wx_redirect("event_sign_out", $signCode);
        $uid = uid();
        $event = D('Event')->where("sign_code='$signCode'")->find();
        if ($event && $event['status'] > 0) {
            $eventId = $event['id'];
            $attend = D('EventAttend')->where("uid=$uid and event_id=$eventId")->find();
            if ($attend) {
                if ($attend['status'] != 1) {
                    $msg = ("抱歉，你已取消活动了");
                } elseif ($attend['sign_out_status'] == 1) {
                    $msg = ("抱歉你已经签退了");
                } else {
                    D('EventAttend')->where("uid=$uid and event_id=$eventId")->save(array(
                        'sign_out_status' => 1,
                        'sign_out_time' => time()
                    ));
                    $msg = ("签退成功");
                }

            } else {
                $msg = ("抱歉，你还没有报名这次活动");
            }

        } else {
            $msg = ("抱歉,没有找到该活动");
        }

        $this->setHeader($event['title'] . '--签退');
        $this->setTitle($event['title'] . '--签退');
        $this->assign('signStatus', $msg);
        $this->assign('signTitle2', '活动已圆满结束，感谢各位积极参与');
        $this->assign('signTitle3', '关注企业号，参与更多活动');
        $this->display('sign');
    }

    public function pc_index()
    {
        $this->display();
    }
}