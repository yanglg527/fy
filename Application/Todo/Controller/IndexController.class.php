<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Todo\Controller;
use Common\Controller\BaseController;

class IndexController extends BaseController {

    function _initialize(){
        parent::_initialize();
        $this->setHeader('待办事项');
        $this->setTitle('待办事项');

    }

    public function index()
    {
//        logout();
//        $user = user();
//        exit('uid = '.$user['role_id']);
//        $todoList = D('TodoReceiverRoleView')->where("TodoReceiverRole.role_id=".$user['role_id'])->select();
//        exit(to_json_string($todoList));
//        $page = D('TodoReceiverRoleView')->findPage("TodoReceiverRole.role_id=".$user['role_id'], 15, 'TodoUserStatus.status asc, Todo.create_time desc');
//        exit(to_json_string($page));

//        $this->assign('page', $page);

        $this->check_wx_redirect('todo');//判断是否重定向登录
        $this->setHeader('待办事项');
        $this->setTitle('待办事项');
        $this->display();
    }

    public function ajaxLoading(){
        $this->check_wx_redirect('todo');//判断是否重定向登录
        $type_name=I('post.type_name');
        $published_at=I('post.published_at');
        if ($published_at == 0 || $published_at == null) {
            $published_at=time();
        }

        $user = user();
        $branch_id = $user['branch_id'];
        $roleId = $user['role_id'];
//        ajaxMsg(1,"bid = ".$branch_id . " rid = " . $roleId,"");
        if($branch_id){
            if($type_name > 0){ // 获取已办事项列表
                $where = array( 'Todo.status'=>1,'TodoUserStatus.uid'=>$user['uid'], 'Todo.create_time'=>array('lt',$published_at),'TodoUserStatus.status'=>array('gt',0));
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
                $where = array('Todo.id'=>array('in',$in),'Todo.status'=>1,'Todo.id '=>array('not in',$notIn), 'Todo.create_time'=>array('lt',$published_at));
                $page = D('TodoView')->find_page($where, '','', 'Todo.create_time desc');
            }

            $list = $page['list'];
            foreach($list as $index=>$item){
                $item['how_long_ago'] = howLongAgo($item['issue_time']);
                $list[$index] = $item;
            }
            $page['list'] = $list;
        }
        ajaxMsg(0,to_json_string($page['list']),$page['list']);
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

    public function ajaxConfirmAll(){
        $this->check_wx_redirect('todo');//判断是否重定向登录
        $user = user();
//        $userTodoList = D('TodoSituationView')->where('User.uid = $uid')->select();
//        $userTodoList = D('TodoReceiverRoleView')->where(array('TodoReceiverRole.role_id'=>$user['role_id'], 'Todo.status'=>1))->select();
//        ajaxMsg(1,to_json_string($userTodoList));
        // 获取所有未办事项
        $uid = $user['uid'];
        $branch_id = $user['branch_id'];
        $roleId = $user['role_id'];
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
        $where = array('Todo.id'=>array('in',$in),'Todo.status'=>1,'Todo.id '=>array('not in',$notIn));
        $userTodoList = D('TodoView')->where($where)->select();
//        ajaxMsg(1, to_json_string($userTodoList));
        foreach($userTodoList as $index=>$item){
            $todoUserStatus = D('TodoUserStatus')->where(array('todo_id'=>$item['id'], 'uid'=>$user['uid']))->find();
            if($todoUserStatus){
                if($todoUserStatus['status'] <= 0){
                    $todoUserStatus['status'] = 1;
                    D('TodoUserStatus')->save($todoUserStatus);
                }
            }else{
                $todoUserStatus['uid'] = $user['uid'];
                $todoUserStatus['todo_id'] = $item['id'];
                $todoUserStatus['create_time'] = time() + $index;
                $todoUserStatus['submit_time'] = time() + $index;
                $todoUserStatus['status'] = 1;
                D('TodoUserStatus')->add($todoUserStatus);
            }
        }

        ajaxMsg(0,"成功");

    }


    public function detail(){

        $id = I('id');
        $id = $id?$id:I('state');
        $this->check_wx_redirect('todo_detail',$id);//判断是否重定向登录
        $uid = uid();
        $todo = D('TodoView')->find($id);
        $userTodo = D('TodoUserStatus')->where("todo_id=$id and uid=$uid")->find();
        if($userTodo){
            $todo['todo_user_status'] = $userTodo['status'];
        }else{
            $todo['todo_user_status'] = 0;
        }

        $this->assign('userTodo', $userTodo);
        $this->assign('todo', $todo);
        $this->setHeader('待办事项详情');
        $this->setTitle('待办事项详情');
        $this->display();
    }

}