<?php
namespace Admin\Controller;
use Common\Controller\BaseController;
use Common\Util\ImageUploadUtil;
use Think\Controller;
use Weixin\Util\QYConfig;
use Admin\Util\AdminUtil;

/**
 *
 */
class PartyWorkingController extends BaseAdminController
{
    function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->set_sidebar_module('PartyWorking');

    }

    // 党区委列表页
    public function index()
    {
        $this->set_sidebar_sub('working');
        // 获得所有符合条件的支部
        $keyword = I('keyword');
        if ($keyword) {
            $page = D('PartyWorking')->findPage("name like '%$keyword%'", 15, 'sort desc');
            $this->assign('search', array('keyword'=>$keyword));
        } else {
            $page = D('PartyWorking')->findPage("", 15, 'sort desc');
        }
        $this->assign('page', $page);
        $this->display();
    }
    // 新增编辑
    public function workingEdit($id = 0)
    {
        $id = I('get.id');
        if ($id > 0) {
            $detail = D('PartyWorking')->where(array('id'=>$id))->find();
            $this->assign('detail', $detail);
        }

        $this->assign('p', I('get.p', 1));
        $this->display();

    }

    //编辑新增保存
    public function ajaxSave()
    {
        $id = I('id');
        $name = I('name');
        $sort = I('sort');
        if(!$sort){
            $sort = 0;
        }
        if (!$name) {
            ajaxMsg(1, '请先填写区委名称');
        }
        $array = array(
            'sort'=>$sort,
            'name' => $name,
        );
        if ($id) {
            D('PartyWorking')->where(array('id'=>$id))->save($array);
        }else {
            $id =  D('PartyWorking')->add($array);
//            $level = array(
//                'area_id'=>$id,
//            );
//            D('party_organization_level')->add($level);//保存数据到组织层级表
        }
        ajaxMsg(0, '保存成功');
    }

    //获取用户
    public function  ajaxGetUsers()
    {

        $id = I('id', 0);
        $keyword = I('keyword', '');
        $type = I('type','normal');
        if($keyword){ //todo
            $page = D('User')->where('realname',' like', '%陈%');//,15,'id desc'
            $this->assign('keyword', $keyword);
        }else{
            $page = D('User')->limit(0,30)->select();
        }
        foreach ($page as $index => $item) {
            $uid = $item["uid"];
            $realname = $item['realname'];
            $branch_name = $item['branch_name'];
            if($branch_name){
                $name = $item['realname'].$item['mobile'] . "[$branch_name]";
            }else{
                $name = $item['realname'].$item['mobile'];
            }
            $item['html'] = "<a style='color: black;cursor: pointer;'><div class='item canselect' data-id='$uid' data-name='$realname'>$name</div></a>";

            $page[$index] = $item;
        }

        ajaxMsg(0, 'success', $page);

    }
    //获取管理员
    public function  ajaxGetAdms()
    {
        $keyword = I('keyword', '');
        $list = D('User')->select();
        foreach ($list as $index => $item) {
            $uid = $item["uid"];
            $realname = $item['realname'];
            $branch_name = $item['branch_name'];
            if($branch_name){
                $name = $item['realname'].$item['mobile'] . "[$branch_name]";
            }else{
                $name = $item['realname'].$item['mobile'];
            }
            $item['html'] = "<a style='color: black;cursor: pointer;'><div class='item canselect' data-id='$uid' data-name='$realname'>$name</div></a>";
            if($item['branch_adm_uid']){
                // $item['html'] = "<div class='item un' data-id='$uid' >$name<div class='item-right'>[{$item['branch_name']}-管理员]</div></div>";
                $item['html'] = "<div class='item un' data-id='$uid' >$name<div class='item-right'>管理员</div></div>";
            }

            $list[$index] = $item;
        }

        ajaxMsg(0, 'success', $list);

    }

    // 删除支部
    public function ajaxDecPartyWorking()
    {
        $id = I('post.id');

//        $count = D('user')->where("branch_id=$id and status=1")->count();
//        if ($count > 0) {
//            ajaxMsg(1, "有部分用户在该分支部下");
//        }

        D('PartyWorking')->where(array('id' => $id))->delete();
        ajaxMsg(0, "已删除");
    }

    public function member(){
        $working_id = I('id');
        $Working =D('PartyWorkingMembers')->where("working_id=$working_id")->select();//取党工委成员列表
        $Working = dutyorder($Working);//按职务排序
        $Workingname =D('PartyWorking')->where("id=$working_id")->field('name')->find();//取区委名称
        $this->assign('working_id', $working_id);
        $this->assign('Working', $Working);
        $this->assign('Workingname', $Workingname);
        $this->display();
    }

    //新增成员
    public function add_member()
    {
        $working_id = I('working_id');
        $Working = D('PartyWorking')->where("id=$working_id")->find();
        //获取SetMembers表里设置好的成员角色可选项
        $working_members = D('SetMembers')->where(array('type'=>'working_status_id'))->find();
        $working_status_id =  explode(",",$working_members['status_id']);
        foreach ($working_status_id as $key =>$v){
            $post_data = D('PartyPost')->where(array('status_id'=>$v))->find();
            $set_working_members[$key]['value'] = $v;
            $set_working_members[$key]['name'] = $post_data['name'];
        }
        $this->assign('set_working_members', $set_working_members);
        $this->assign('Working', $Working);
        $this->display();
    }

    //编辑成员
    public function edit_member()
    {
        $working_id = I('working_id');
        $uid = intval(I('uid'));
        $id = intval(I('id'));
        $Working = D('PartyWorking')->where("id=$working_id")->find();
        if($working_id and $uid) {
            $Membersa = D('PartyWorkingMembers')->where(array('id'=>$id))->find();
        }
        $this->assign('Working', $Working);
        $this->assign('Membersa', $Membersa);
////        $this->assign('p', I('get.p', 1));
        $this->display();
    }
    //保存新增成员
    public function ajaxSaveMember()
    {
        $uid = intval(I('id'));
        $working_id = intval(I('working_id'));
        $status = intval(I('status',0));
        if(!$working_id){
            ajaxMsg(1, '参数有误');
        }
        if (!$uid) {
            ajaxMsg(1, '请先选择用户');
        }
        //$status    0:成员 1:书记 2:副书记 3:管理员
        if($status == 1){
            D('PartyWorkingMembers')->where(array('working_id'=>$working_id,'status'=>1))->delete();//书记就一个，添加之前删除原来的
        }
        if($uid){
            $name =  D('user')->where("uid=$uid")->field('realname')->find();
            D('PartyWorkingMembers')->add(array('working_id'=>$working_id,'uid'=>$uid,'status'=>$status,'realname'=>$name['realname']));
            ajaxMsg(0, '保存成功');
        }else{
            ajaxMsg(1, '参数错误');
        }


    }
    //保存编辑成员
    public function ajaxSaveEditMember()
    {
        //exit(to_json_string(I()));
        $id = I('id');
        $uid = I('uid');
        $working_id = intval(I('working_id'));
        $status = intval(I('status',0));
        if(!$working_id){
            ajaxMsg(1, '参数错误');
        }
        if (!$uid) {
            ajaxMsg(1, '请先选择用户');
        }
        //$status    0:成员 1:书记 2:副书记 3:管理员
        if($status == 1){
            D('PartyWorkingMembers')->where(array('working_id'=>$working_id,'status'=>1))->delete();//书记就一个，添加之前删除原来的
        }
        if($uid){
            $name =  D('user')->where("uid=$uid")->field('realname')->find();
            D('PartyWorkingMembers')->save(array('id'=>$id,'working_id'=>$working_id,'uid'=>$uid,'status'=>$status,'realname'=>$name['realname']));
            ajaxMsg(0, '保存成功');
        } else{
            ajaxMsg(1, '参数错误');
        }
    }
    //删除成员
    public function ajaxDecMember($id = 0){
        D('PartyWorkingMembers')->where("id = $id")->delete();
        ajaxMsg(0, '删除成功');
    }

    //获取用户
    public function  ajaxGetUsers_member()
    {
        $id = I('id', 0);
        $keyword = I('keyword', '');
        $type = I('type','normal');
        if($keyword){
            $user = M('User');
            $map['realname'] = array('LIKE',"%$keyword%");   //模糊查询包含此文字
            $page = $user->where($map)->select();
            $this->assign('keyword', $keyword);
        }else{
            $page = D('User')->select();//select();//limit(0,30)->select();
        }
        foreach ($page as $index => $item) {
            $uid = $item["uid"];
            $realname = $item['realname'];
            $branch_name = $item['branch_name'];
            if($branch_name){
                $name = $item['realname'].$item['mobile'] . "[$branch_name]";
            }else{
                $name = $item['realname'].$item['mobile'];
            }
            $item['html'] = "<a style='color: black;cursor: pointer;'><div class='item canselect' data-id='$uid' data-name='$realname'>$name</div></a>";

            $page[$index] = $item;
        }

        ajaxMsg(0, 'success', $page);

    }
}