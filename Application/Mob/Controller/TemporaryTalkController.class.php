<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
    namespace Mob\Controller;
use Common\Controller\BaseAuthController;

class TemporaryTalkController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
    }

    function temporary_branch(){
        $uid = uid();
        $myList = D('TemporaryBranch')->where(array('uid'=>$uid, 'status'=>1))->order('create_time desc')->select();

        $notIn = '';
        if(sizeof($myList) > 0){
            $isFirst = true;
            foreach ($myList as $item){
                if($isFirst){
                    $isFirst = false;
                    $notIn = $item['id'];
                }else{
                    $notIn = $notIn . ',' . $item['id'];
                }
            }
        }
        $where = array('TemporaryBranchMember.uid'=>$uid,'TemporaryBranch.status'=>1,'TemporaryBranchMember.branch_id '=>array('not in',$notIn));
        $joinList = D('TemporaryBranchMemberView')->where($where)->order('TemporaryBranch.create_time desc')->select();

        $this->assign('myBranchList',$myList);
        $this->assign('myBranchListCount',sizeof($myList));
        $this->assign('joinBranchList', $joinList);
        $this->assign('joinBranchListCount', sizeof($joinList));
        $this->display();
    }

    function back_temporary_member(){
        $branchName = I('branchName');

        $this->assign('branchName', $branchName);
        $this->display();
    }

    function temporary_member(){
        $branchName = I('branchName');

        $searchName = I("searchName");
        if($searchName){
            $userList = D("User")->where(array("status"=>array("gt", -1), "realname"=>array("like","%$searchName%")))->order("uid asc")->select();
        }else{
            $userList = D("User")->where(array("status"=>array("gt", -1)))->order("uid asc")->select();
        }

        $this->assign('branchName', $branchName);
        $this->assign("searchName", $searchName);
        $this->assign('userList', $userList);
        $this->display();
    }

    function ajaxGetUserList(){
        $lastUid = I('lastUid') ? I('lastUid') : 0;
        $searchName = I("searchName");
        if($searchName){
            $sql = "select uid, realname, headimgurl from user where status > -1 and uid > $lastUid and realname like '%" . $searchName ."%' order by uid asc LIMIT  20";
        }else{
            $sql = "select uid, realname, headimgurl from user where status > -1 and uid > $lastUid order by uid asc LIMIT  20";
        }
        $userList = D()->query($sql);
        ajaxMsg(0, "", $userList);
    }

    function ajaxAddTemporaryBranch(){
        $branchName = I("branchName");
        $members = I("members");
        $membersUid = explode(',',$members);

        $temporaryBranch = array(
            "uid"=>uid(),
            "branch_name"=>$branchName,
            "create_time"=>time(),
            "member_count"=>sizeof($membersUid)+1
        );
        $temporaryBranchId = D('TemporaryBranch')->add($temporaryBranch);
        D("TemporaryBranchMember")->add(array("branch_id"=>$temporaryBranchId, "uid"=>uid()));

        foreach ($membersUid as $memberUid){
            D("TemporaryBranchMember")->add(array("branch_id"=>$temporaryBranchId, "uid"=>$memberUid));
        }

        ajaxMsg(0, "成功", $temporaryBranchId);
    }

    function temporary_chat(){
        $branchId = I('branchId');
        $this->assign('branchId', $branchId);
        $this->display();
    }

    function ajaxGetLastChatList(){
        $branchId = I('branchId');
        if($branchId > 0){
            $lastChatList = D("TemporaryChatView")->where(array("status"=>0, 'branch_id'=>$branchId))->order('create_time desc')->limit(10)->select();
            ajaxMsg(0, to_json_string($lastChatList), $lastChatList);
        }
        ajaxMsg(1, "群聊不存在");
    }
    function ajaxGetBeforeChatList(){
        $branchId = I('branchId');
        $firstItem = I("firstItem");
//        ajaxMsg(1, "firstItem = ".$firstItem);
        $firstItem = $firstItem > 0 ? $firstItem : time();
        if($branchId > 0){
            $lastChatList = D("TemporaryChatView")->where(array("status"=>0, 'branch_id'=>$branchId, "create_time"=>array("lt",$firstItem)))->order('create_time desc')->limit(10)->select();
            ajaxMsg(0, to_json_string($lastChatList), $lastChatList);
        }
        ajaxMsg(1, "群聊不存在");
    }

    function ajaxSendMsg(){
        $msg = I("msg");
        $branchId = I("branchId");
        if($branchId > 0){
            $temporaryChat = array(
                "branch_id"=>$branchId,
                "uid"=>uid(),
                "content"=>$msg,
                "create_time"=>time()
            );
            D("TemporaryChat")->add($temporaryChat);
            ajaxMsg(0,"success");
        }
        ajaxMsg(1, "群聊不存在");
    }
}