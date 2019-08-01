<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
    namespace Mob\Controller;
use Common\Controller\BaseAuthController;
use Common\Util\CalendarUtil;

class InformController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
    }


    // 信息首页(任务日历列表 + 新点赞数量 + 新评论数)
    public function inform()
    {
        $this->check_wx_redirect('todo');//判断是否重定向登录
        $firstDay = strtotime(CalendarUtil::getCurMonthFirstDay(date("Y-m")));     // 本月第一天
        $lastDay = time(); // 本月最后一天,当前时间的效果等同于本月最后一天

        $this->assign("missionList", $this->_getMissionList(0, $firstDay, $lastDay));

        $this->display();
    }

    public function index(){
        $this->assign('norms',D('TaizhangNorms')->order('id asc')->select());
        $this->assign('tags', D('TaizhangTags')->order('title asc')->select());
        // echo to_json_string();

        $this->display();
    }

    public function ajaxNoticeLoading(){
        $this->check_wx_redirect('inform_index');//判断是否重定向登录
        $uid = uid();
        $map['uid']=$uid;
        $time_now=time();
        if (I('post.type') == 0) {
            $map['MU.status'] = 0;
            $map['MS.status'] = 1;
            $map['s_time'] = array('lt',$time_now);
            $map['e_time'] = array('gt',$time_now);

            $notices1 = M('mission_user')->alias('MU')->field('MU.id,MU.uid,MU.admin_uid,MU.status,MU.s_time,MU.e_time,MU.mission_id,MS.title,MS.content,MS.assist_unit_id,MS.mission_score,MS.push_person,MS.person_adm_id,MS.standard_id')->join('mission as MS on MS.id=MU.mission_id')->where($map)->order('MU.e_time asc')->select();

            //秘书
            $map1['admin_uid'] = $uid;
            $map1['MU.status'] = 0;
            $map1['MS.status'] = 1;
            $map1['s_time'] = array('lt',$time_now);
            $map1['e_time'] = array('gt',$time_now);
            $notices2 = M('mission_user')->alias('MU')->field('MU.id,MU.uid,MU.admin_uid,MU.status,MU.s_time,MU.e_time,MU.mission_id,MS.title,MS.content,MS.assist_unit_id,MS.mission_score,MS.push_person,MS.person_adm_id,MS.standard_id')->join('mission as MS on MS.id=MU.mission_id')->where($map1)->order('MU.e_time asc')->select();

            $notices = array_merge($notices1,$notices2);
            // if($notices){
            //     foreach($notices as $k=>$v){
            //         //把serialize类型的数据转成数组
            //         $notices[$k]['standard_id'] = unserialize($notices[$k]['standard_id']);
            //         foreach($notices[$k]['standard_id'] as $k1=>$v1){
            //             $notices[$k]['standard'][$k1] = M('t_partybldregtype')->where(array('id'=>$v1))->find();
            //         }
            //     }
            // }
        }else{
            $map['MU.status'] = 0;
            $map['MS.status'] = 1;
            $map['e_time'] = array('lt',$time_now);

            //秘书
            $map1['admin_uid'] = $uid;
            $map1['MU.status'] = 0;
            $map1['MS.status'] = 1;
            $map1['e_time'] = array('lt',$time_now);

            $notices1 = M('mission_user')->alias('MU')->field('MU.id,MU.uid,MU.admin_uid,MU.status,MU.s_time,MU.e_time,MU.mission_id,MU.taizhang_id,MS.title,MS.content,MS.assist_unit_id,MS.mission_score,MS.push_person,MS.person_adm_id,MS.standard_id')->join('mission as MS on MS.id=MU.mission_id')->where($map)->order('MU.e_time asc')->select();

            $notices2 = M('mission_user')->alias('MU')->field('MU.id,MU.uid,MU.admin_uid,MU.status,MU.s_time,MU.e_time,MU.mission_id,MU.taizhang_id,MS.title,MS.content,MS.assist_unit_id,MS.mission_score,MS.push_person,MS.person_adm_id,MS.standard_id')->join('mission as MS on MS.id=MU.mission_id')->where($map1)->order('MU.e_time asc')->select();

            $notices = array_merge($notices1,$notices2);
            // if($notices){
            //     foreach($notices as $k=>$v){
            //         //把serialize类型的数据转成数组
            //         $notices[$k]['standard_id'] = unserialize($notices[$k]['standard_id']);
            //         foreach($notices[$k]['standard_id'] as $k1=>$v1){
            //             $notices[$k]['standard'][$k1] = M('t_partybldregtype')->where(array('id'=>$v1))->find();
            //         }
            //     }
            // }
        }

         ajaxMsg(0,"",$notices);
    }


    //获取任务详情
    public function ajaxNoticeDetail(){
        $id = I('post.id');

        $map['MU.id'] = $id;
        $notices = M('mission_user')->alias('MU')->field('MU.id,MU.uid,MU.admin_uid,MU.status,MU.s_time,MU.e_time,MU.mission_id,MS.title,MS.content,MS.assist_unit_id,MS.mission_score,MS.push_person,MS.person_adm_id,MS.standard_id')->join('mission as MS on MS.id=MU.mission_id')->where($map)->find();
        if($notices){
            //把serialize类型的数据转成数组
            $notices['standard_id'] = unserialize($notices['standard_id']);
            foreach($notices['standard_id'] as $k=>$v){
                $notices['standard'][$k] = M('t_partybldreg')->where(array('id'=>$v))->find();
            }
        }

        ajaxMsg(0,"",$notices);
    }

    public function test(){
        $time_now=time();
        $notices=D()->query("select notice_user_status.id,notice_user_status.uid,notice_user_status.is_add_taizhang,notice.content as notice_content,notice.finish_time as notice_finish_time,notice.taizhang_type as notice_taizhang_type,(select name from notice_type where notice_type.id = notice.type_id) AS type_name
                from notice_user_status LEFT JOIN notice ON notice_user_status.notice_id=notice.id where (notice_user_status.status=1 AND notice_user_status.uid=3789 AND (notice_user_status.is_add_taizhang=1 OR notice.finish_time<$time_now))");
        echo to_json_string($notices);
    }

    public function ajaxComentLoading(){
        $create_time=I('post.create_time');
        if ($create_time > 0) {
            $where["create_time"]=array('lt',$create_time);
        }
        $where["taizhang_uid"]=uid();
        $list=D('TaizhangCommentView2')->where($where)->order('create_time desc')->limit(10)->select();
        // echo to_json_string($articles);
        ajaxMsg(0,'',$list);
    }

    public function ajaxLikeLoading(){
        $create_time=I('post.create_time');
        if ($create_time > 0) {
            $where["create_time"]=array('lt',$create_time);
        }
        $where["taizhang_uid"]=uid();
        $list=D('TaizhangDzView2')->where($where)->order('create_time desc')->limit(10)->select();
        // echo to_json_string($articles);
        ajaxMsg(0,'',$list);
    }

    public function partyTask()
    {
        $this->check_wx_redirect('todo');//判断是否重定向登录

        $this->display();
    }

    public function ajaxLoadingMissionList(){
        $type = I('type');
        $firstDay = strtotime(CalendarUtil::getCurMonthFirstDay(I('date')));
        $lastDay = strtotime(CalendarUtil::getCurMonthLastDay(I('date')));
//        ajaxMsg(0,"type ==== ".$type."  f=".I('date'), null);
        $missionList = $this->_getMissionList($type, $firstDay, $lastDay);
        if(sizeof($missionList) > 0){
            ajaxMsg(0,"json = ".to_json_string($missionList), $missionList);
        }else{
            ajaxMsg(0,"没有任务", null);
        }
    }

    public  function ajaxConfirmTodo(){
        $this->check_wx_redirect('todo');//判断是否重定向登录
        $id = I('id');
        $uid = uid();
        $userTodo = D('TodoUserStatus')->where("todo_id=$id and uid=$uid")->find();
//        ajaxMsg(1,to_json_string($userTodo));
        if($userTodo){
            $userTodo['status'] = 1;
            $userTodo['create_time'] = time();
            D('TodoUserStatus')->save($userTodo);
        }else{
            $userTodo['status'] = 1;
            $userTodo['create_time'] = time();
            $userTodo['uid'] = $uid;
            $userTodo['todo_id'] = $id;
            D('TodoUserStatus')->add($userTodo);
        }

        ajaxMsg(0,'成功');
    }


    function _getMissionList($type_name, $firstDay, $lastDay){
        $user = user();
        $branch_id = $user['branch_id'];
        $roleId = $user['role_id'];
        if($branch_id){
            if($type_name > 0){ // 获取已办事项列表
                $where = array( 'Todo.status'=>1,'TodoUserStatus.uid'=>$user['uid'],  'Todo.create_time'=>array(array('lt',$lastDay),array('gt',$firstDay)),'TodoUserStatus.status'=>array('gt',0));
                $page = D('TodoReceiverRoleView')->find_page($where, '','TodoReceiverRole.todo_id', 'TodoUserStatus.status asc, Todo.create_time desc');
            }else{ // 获取未办事项列表
                $uid = $user['uid'];

                $statusIds = D()->query("select group_concat(todo_id) from todo_user_status where  uid=$uid and status=0");
                $in1 = strlen($statusIds[0]['group_concat(todo_id)']) <=  0 ? false:$statusIds[0]['group_concat(todo_id)'];
                $inRes =  D()->query("select group_concat(todo_id) from todo_receiver_role where role_id=$roleId and branch_id=$branch_id");
                $in = $inRes[0]['group_concat(todo_id)']? $inRes[0]['group_concat(todo_id)']:'';
                if($in1 && $in){
                    $in = $in.','.$in1;
                }elseif($in1){
                    $in = $in1;
                }

                $noInRes = D()->query("select group_concat(todo_id) from todo_user_status where uid=$uid and status>0");
                $notIn = $noInRes[0]['group_concat(todo_id)']? $noInRes[0]['group_concat(todo_id)']:'';
                $where = array('Todo.id'=>array('in',$in),'Todo.status'=>1,'Todo.id '=>array('not in',$notIn), 'Todo.create_time'=>array(array('lt',$lastDay),array('gt',$firstDay)));
                $page = D('TodoView')->find_page($where, '','', 'Todo.create_time desc');
            } 

            return $page['list'];
        }
        return null;
    }

}