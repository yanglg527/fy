<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace JoinApply\Controller;
use Common\Controller\BaseController;

class IndexController extends BaseController {

    function _initialize(){
        parent::_initialize();
        $this->setHeader('入党申请');
        $this->setTitle('入党申请');
    }

    public function index()
    {

        $this->check_wx_redirect("joinApply_index");//判断是否重定向登录
        // 获取用户基本信息
        $user = user();
        $this->assign('user', $user);

        // 获取支部列表
        $partyBranchList = D('PartyBranch')->select();
        $this->assign('branchList', $partyBranchList);

        // 获取用户最近的入党申请
        $userJoinApply = D('UserJoinApply')->where('uid = ' . uid())->order('create_time desc')->limit(1)->select();
//        exit(to_json_string($userJoinApply));
        $this->assign('userJoinApply', $userJoinApply);
        if ($userJoinApply) {
            if($userJoinApply[0]['status'] == 1){
                $this->assign('btnStatus',false);
                $this->assign('tip', "申请已通过，你已经是".$user['role_name']."了。");
            }else if($userJoinApply[0]['status'] == 0){
                $this->assign('btnStatus',false);
                $this->assign('tip', "申请审核中,请耐心等待...");
            }else{
                $this->assign('btnStatus',true);
                $this->assign('tip', "很遗憾，你的申请被驳回了。");
            }
        }else if($user['role_id'] != C('ROLE_NORMAL_ID')){ // 用户已经不是群众
            $this->assign('btnStatus',false);
            $this->assign('tip', "你已经是".$user[role_name]."了,无需再申请");
        }else{
            $this->assign('btnStatus',true);
            $this->assign('tip', "温馨提示：请填写真实信息。");
        }

        $this->setHeader('入党申请');
        $this->setTitle('入党申请');
        $this->display();
    }

    public function ajaxJoinApply(){
        $this->check_wx_redirect();//判断是否重定向登录
        // 获取表单数据
        $gender = I('gender');
        $birthday = I('birthday');
        $position = I('position');
        $workUnit = I('workUnit');
        $branchId = I('branchId');
        $jobResume = $_POST['jobResume'];
        $rewardSituation = $_POST['rewardSituation'];

        // 验证表单
        if(!$birthday)
            ajaxMsg(1,'你忘了填生日了');
        if(!$position)
            ajaxMsg(1,'你忘了填职务了');
        if(!$workUnit)
            ajaxMsg(1,'你忘了填单位了');
        if(!$branchId || $branchId == 0)
            ajaxMsg(1,'你漏了选择要加入的党支部了');

        // 判断申请人身份
        $user = user();
        if($user['role_id'] != C('ROLE_NORMAL_ID')){ // 用户已经不是群众
            ajaxMsg(1,"你已经是".$user[role_name]."了,无需再申请");
        }

        // 获取用户最近的入党申请
        $userJoinApply = D('UserJoinApply')->where(array('uid'=>uid()))->order('create_time desc')->limit(1)->select();
        if($userJoinApply){
            if($userJoinApply[0]['status'] == 0){
                ajaxMsg(1,"你的申请正在审核中，请不要重复申请");
            }
        }

        // 修改用户基本信息
        $user['gender'] = $gender;
        $user['birthday'] = $birthday;
        $user['position'] = $position;
        $user['work_unit'] = $workUnit;
        $user['job_resume'] = $jobResume;
        $user['reward_situation'] = $rewardSituation;
        resession_user($user);
        D('User')->save($user);

        // 生成入党申请记录
        $this->_createUserJoinApply($branchId);

        ajaxMsg(0,"提交成功");

    }

    private function _createUserJoinApply($branchId){
        $userJoinApply['uid'] = uid();
        $userJoinApply['status'] = 0;
        $userJoinApply['create_time'] = time();
        $userJoinApply['branch_id'] = $branchId;
        D('UserJoinApply')->add($userJoinApply);
    }
}