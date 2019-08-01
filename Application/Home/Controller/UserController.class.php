<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午7:05
 */
namespace Home\Controller;
use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;

class UserController extends BaseAuthController {

	private $prefix = 'suite_';
	private $page = null;
	private $active = 'am-icon-check';

    function _initialize(){
        parent::_initialize();
        $this->setHeader('个人中心');
//        $this->check_wx_redirect('user_info');//判断是否重定向登录
    }


    public function ajax_popup($type){
        $func = $this->page = $this->prefix.$type;
        $html = $this->$func();
        $this->ajaxMsg(0,null,$html);

    }



    public function index(){
        $this->display();
    }

    public function suite_secretaryBox(){
        $this->assign();
        return $this->fetch("User:".$this->page);
    }

    public function suite_payRecord(){
        $this->assign();
        return $this->fetch("User:".$this->page);
    }



    public function personal(){

    	$this->assign(array(
    		'birth'=>'2016-11-1'
    	));
    	$this->display();
    }

    public function suite_portail(){

    	$this->assign();
    	return $this->fetch("User:".$this->page);

    }

    public function suite_nickname(){

    	$this->assign('nickname', 'issac');
    	return $this->fetch("User:".$this->page);

    }

    public function suite_name(){

    	$this->assign('name', '甘宝华');
    	return $this->fetch("User:".$this->page);

    }

    public function suite_sex(){

    	// male: 男，female：女
    	$this->assign(array(
    		'female'=>$this->active,
    		'value'=>'女'
    	));
    	return $this->fetch("User:".$this->page);

    }

	public function suite_company(){

    	$this->assign('company', 'ebyte网络有限公司');
    	return $this->fetch("User:".$this->page);

    }

    public function suite_position(){

    	$this->assign('name', 'issac宝华');
    	return $this->fetch("User:".$this->page);

    }

    public function suite_branch(){

    	// branch1、branch2……
    	$this->assign(array(
    		'branch2'=> $this->active,
    		'value'=>'branch2'
    	));
    	return $this->fetch("User:".$this->page);

    }

    public function suite_resume(){


    	return $this->fetch("User:".$this->page);

    }


    public function ajax_personal(){

    	$this->ajaxMsg(0, json_encode($_POST), null);

    }

    public function ajax_changeDate(){
    	// $this->ajaxMsg(1, 'wrong', null);
    	$this->ajaxMsg(0, null, null);
    }

    public function issac(){

    	$this->display();

    }


    /******************************** add by linjiahuan begin **********************************/
    //每日签到页面
    public function sign_in(){
        $this->setHeader('签到');
        $this->setTitle('签到');
        $this->display();
    }

    //抽奖页面
    public function prize(){
        $this->setHeader('抽奖');
        $this->setTitle('抽奖');
        $this->display();
    }

    // 积分记录
    public function scoreLog(){
        $uid=uid();
        $page = D('UserScoreLog')->findPage("uid=$uid", 25, 'create_time desc');
        $this->assign('page', $page);
        $this->setHeader('积分记录');
        $this->setTitle('积分记录');
        $this->display("scoreLog");
    }

    // 个人资料
    public function info(){
        $header_left['url'] = __ROOT__."/Home/Index/index";
        $this->assign('header_left', $header_left);
        $this->setHeader('个人资料');
        $this->display();
    }

    // 性别编辑页
    public function editSex(){
        $header_left['url'] = __ROOT__."/Home/User/info";
        $this->assign('header_left', $header_left);
        $this->setHeader('性别');
        $this->display();
    }
    // 保存性别
    public function ajaxSaveGender(){
        $gender = I('gender');
        $uid = uid();
        if($uid){
            // 保存到数据库
            D('User')->where("uid=$uid")->save(array('gender'=>$gender));
            // 保存到session
            $user = user();
            $user['gender'] = $gender;
            resession_user($user);
            ajaxMsg(0, '修改成功');
        }else{
            ajaxMsg(0, '登录已过期，请重新登录。');
        }
    }

    // 所属支部编辑页
    public function editBranch(){
        // 获取支部列表
        $partyBranchList = D('PartyBranch')->select();
        $this->assign('branchList', $partyBranchList);

        $header_left['url'] = __ROOT__."/Home/User/info";
        $this->assign('header_left', $header_left);
        $this->setHeader('所属支部');
        $this->display();
    }
    // 保存所属支部
    public function ajaxSaveBranch(){
        $branchId = I('branchId');
        $branch = D('PartyBranch')->find($branchId);
        if($branch){

            $user = user();
            // 更新到数据库
            if($user['branch_id'])
                $oldBranch = D('PartyBranch')->find($user['branch_id']);
            else{
                $oldBranch['name'] = "未选择支部";
            }

            if($branchId)
                $branch = D('PartyBranch')->find($branchId);
            else{
                $branch['name'] = "未选择支部";
            }

            $status = relation_change_log($user,2,$branchId,array('org_branch_name'=>$oldBranch['name'],'branch_name'=>$branch['name']));

            // 更新到session


            if($status){
                $user['branch_id'] = $branchId;
                $user['branch_name'] = $branch['name'];
                resession_user($user);
                ajaxMsg(0, "修改成功");
            }else{
                ajaxMsg(1, "调动失败");
            }
        }else{
            ajaxMsg(1, '支部不存在，请重新设置');
        }
    }

    // 出生日期编辑页
    public function editBirthday(){
        $header_left['url'] = __ROOT__."/Home/User/info";
        $this->assign('header_left', $header_left);
        $this->setHeader('出生日期');
        $this->display();
    }
    // 保存出生日期
    public function ajaxSaveBirthday(){
        $birthday = I('birthday');
        $uid = uid();
        // 更新到数据库
        $user['birthday'] = $birthday;
        D('User')->where("uid=$uid")->save($user);
        // 更新到session
        $user = user();
        $user['birthday'] = $birthday;
        resession_user($user);
        ajaxMsg(0, '修改成功');
    }

    // 单位编辑页
    public function editWorkUnit(){

        // $this->setHeader('单位');
        // $this->display();

        // 获取工作单位列表
        $workUnitList = D('WorkUnit')->select();
        $this->assign('workUnitList', $workUnitList);

        $header_left['url'] = __ROOT__."/Home/User/info";
        $this->assign('header_left', $header_left);
        $this->setHeader('工作单位');
        $this->display();
    }
    // 保存单位
    public function ajaxSaveWorkUnit(){
        // $workUnit = I('workUnit');
        // $uid = uid();
        // // 更新到数据库
        // $user['work_unit'] = $workUnit;
        // D('User')->where("uid=$uid")->save($user);
        // // 更新到session
        // $user = user();
        // $user['work_unit'] = $workUnit;
        // resession_user($user);
        // ajaxMsg(0, '修改成功');

        $workUnitId = I('workUnitId');
        $workUnit = D('workUnit')->find($workUnitId);
        $uid = uid();
        // 更新到数据库
        $user['work_unit_id'] = $workUnitId;
        D('User')->where("uid=$uid")->save($user);
        // 更新到session
        $user = user();
        $user['work_unit_Id'] = $workUnitId;
        $user['work_unit_name'] = $workUnit['name'];
        resession_user($user);
        ajaxMsg(0, '修改成功');
    }

    // 职务编辑页
    public function editPosition(){
        $header_left['url'] = __ROOT__."/Home/User/info";
        $this->assign('header_left', $header_left);
        $this->setHeader('职务');
        $this->display();
    }
    // 保存职务
    public function ajaxSavePosition(){
        $position = I('position');
        $uid = uid();
        // 更新到数据库
        $user['position'] = $position;
        D('User')->where("uid=$uid")->save($user);
        // 更新到session
        $user = user();
        $user['position'] = $position;
        resession_user($user);
        ajaxMsg(0, '修改成功');
    }

    // 月薪编辑页
    public function editWage(){
        $header_left['url'] = __ROOT__."/Home/User/info";
        $this->assign('header_left', $header_left);
        $this->setHeader('月薪');
        $this->display();
    }
    // 保存月薪
    public function ajaxSaveWage(){
        $wage = I('wage');
        $uid = uid();
        // 更新到数据库
        $user['wage'] = $wage;
        D('User')->where("uid=$uid")->save($user);
        // 更新到session
        $user = user();
        $user['wage'] = $wage;
        resession_user($user);
        ajaxMsg(0, '修改成功');
    }

    // 工作简历列表
    public function jobResume(){
        $entry = I('get.entry');
        if($entry == 'info'){
            $header_left['url'] = __ROOT__."/Home/User/info";
            $this->assign('header_left', $header_left);
        }

        $uid = I('get.uid');
        if(!$uid){
            $uid=uid();
            $header_rink['url']=__ROOT__.'/Home/User/editJobResume?entry='.$entry;
            $header_rink['icon']='am-icon-pencil-square-o';
            $header_rink['text']=' 添加';
            $this->assign('header_rink',$header_rink);
        }

        $list = D('UserJobResumeView')->where("UserJobResume.uid=$uid")->order('UserJobResume.create_time desc')->select();
        $this->assign('list', $list);

        $this->setHeader('工作简历');
        $this->display();
    }
    // 工作简历编辑页
    public function editJobResume(){
        $entry = I('get.entry');
        $this->assign('entry', $entry);

        $list = D('WorkUnit')->select();
        $this->assign('list', $list);

        $id = I('get.id');
        if($id){
            $this->setHeader('工作经历详情');
            $userJobResume = D('UserJobResume')->find($id);
            $this->assign('detail', $userJobResume);
        }else{
            $this->setHeader('添加工作经历');
        }

        $this->display();
    }
    // 工作简历详情页
    public function jobResumeDetail(){
        $id = I('get.id');
        if($id){
            $userJobResume = D('UserJobResumeView')->find($id);
            $this->assign('detail', $userJobResume);
        }
        $this->setHeader('工作经历详情');
        $this->display();
    }
    // 工作单位选择页
    public function selectWorkUnit(){
        $list = D('WorkUnit')->select();
        $this->assign('list', $list);

        $header_left['url'] = __ROOT__."/Home/User/info";
        $this->assign('header_left', $header_left);
        $this->setHeader('选择工作单位');
        $this->display();
    }
    // 保存工作简历
    public function ajaxSaveJobResume(){
        $position = I('position');
        if(!$position){
            ajaxMsg(1, '请填写职务');
        }
        $startDate = I('startDate');
        if(!$startDate){
            ajaxMsg(1, '请填写入职日期');
        }
        $endDate = I('endDate');
        if(!$endDate){
            ajaxMsg(1, '请填写离职日期');
        }
        $workUnitId = I('workUnitId');
        if(!$workUnitId){
            ajaxMsg(1, '请填选择工作单位');
        }

        $userJobResume['uid'] = uid();
        $userJobResume['work_unit_id'] = $workUnitId;
        $userJobResume['position'] = $position;
        $userJobResume['start_date'] = strtotime($startDate);
        $userJobResume['end_date'] = strtotime($endDate);
        $userJobResume['create_time'] = time();
        D('UserJobResume')->add($userJobResume);
        ajaxMsg(0, '已保存');
    }
    // 保存工作简历
    public function ajaxDeleteJobResume(){
        $id = I('id');
        if($id){
            D('UserJobResume')->delete($id);
        }
        ajaxMsg(0, '已删除');
    }


    // 奖励情况列表
    public function rewardSituation(){
        $entry = I('get.entry');
        if($entry == 'info'){
            $header_left['url'] = __ROOT__."/Home/User/info";
            $this->assign('header_left', $header_left);
        }

        $uid = I('get.uid');
        if(!$uid){
            $uid=uid();
            $header_rink['url']=__ROOT__.'/Home/User/editRewardSituation?entry='.$entry;
            $header_rink['icon']='am-icon-pencil-square-o';
            $header_rink['text']=' 添加';
            $this->assign('header_rink',$header_rink);
        }

        $list = D('UserRewardSituation')->where("uid=$uid"." and type=0")->order('create_time desc')->select();
        $this->assign('list', $list);
//        exit(to_json_string($list));

        $this->setHeader('奖励情况');
        $this->display();
    }
    // 奖励情况编辑页
    public function editRewardSituation(){
        $entry = I('get.entry');
        $this->assign('entry', $entry);
        $this->setHeader('添加获奖项');
        $this->display();
    }
    // 奖励情况详情页
    public function rewardSituationDetail(){
        $id = I('get.id');
        if($id){
            $userRewardSituation = D('UserRewardSituation')->find($id);
            $this->assign('detail', $userRewardSituation);
        }
        $this->setHeader('获奖项详情');
        $this->display();
    }
    // 保存奖励情况
    public function ajaxSaveRewardSituation(){
        $name = I('name');
        if(!$name){
            ajaxMsg(1, '请填写奖励名称');
        }
        $rewardDate = I('rewardDate');
        if(!$rewardDate){
            ajaxMsg(1, '请填写获奖日期');
        }
        $level = I('level');
//            ajaxMsg(1, '请选择奖励等级');
//        }

        $userRewardSituation['uid'] = uid();
        $userRewardSituation['name'] = $name;
        $userRewardSituation['type'] = 0;
        $userRewardSituation['level'] = $level;
        $userRewardSituation['reward_date'] = strtotime($rewardDate);
        $userRewardSituation['create_time'] = time();
        D('UserRewardSituation')->add($userRewardSituation);
        ajaxMsg(0, '已保存');
    }
    // 删除奖励情况
    public function ajaxDeleteRewardSituation(){
        $id = I('id');
        if($id){
            D('UserRewardSituation')->delete($id);
        }
        ajaxMsg(0, '已删除');
    }


    /*******/
    // 惩罚情况列表
    public function rewardBadSituation(){
        if(I('get.entry') == 'info'){
            $header_left['url'] = __ROOT__."/Home/User/info";
            $this->assign('header_left', $header_left);
        }

        $uid = I('get.uid');
        if(!$uid){
            $uid=uid();
//            $header_rink['url']=__ROOT__.'/Home/User/editRewardBadSituation';
//            $header_rink['icon']='am-icon-pencil-square-o';
//            $header_rink['text']=' 添加';
//            $this->assign('header_rink',$header_rink);
        }

        $list = D('UserRewardSituation')->where("uid=$uid"." and type=1")->order('create_time desc')->select();
        $this->assign('list', $list);

        $this->setHeader('惩罚情况');
        $this->display();
    }
    // 惩罚情况编辑页
    public function editRewardBadSituation(){
        $this->setHeader('添加惩罚项');
        $this->display();
    }
    // 惩罚情况详情页
    public function rewardBadSituationDetail(){
        $id = I('get.id');
        if($id){
            $userRewardSituation = D('UserRewardSituation')->find($id);
            $this->assign('detail', $userRewardSituation);
        }
        $this->setHeader('惩罚项详情');
        $this->display();
    }
    // 保存惩罚情况
    public function ajaxSaveRewardBadSituation(){
        $name = I('name');
        if(!$name){
            ajaxMsg(1, '请填写惩罚名称');
        }
        $rewardDate = I('rewardDate');
        if(!$rewardDate){
            ajaxMsg(1, '请填写惩罚日期');
        }
        $level = I('level');

        $userRewardSituation['uid'] = uid();
        $userRewardSituation['name'] = $name;
        $userRewardSituation['type'] = 1;
        $userRewardSituation['level'] = $level;
        $userRewardSituation['reward_date'] = strtotime($rewardDate);
        $userRewardSituation['create_time'] = time();
        D('UserRewardSituation')->add($userRewardSituation);
        ajaxMsg(0, '已保存');
    }
    // 删除奖励情况
    public function ajaxDeleteRewardBadSituation(){
        $id = I('id');
        if($id){
            D('UserRewardSituation')->delete($id);
        }
        ajaxMsg(0, '已删除');
    }


    // 个人空间
    public function mySpace(){
        $entry = I('get.entry');
        $this->assign('entry', $entry);
        if($entry == 'info'){
            $header_left['url'] = __ROOT__."/Home/User/info";
            $this->assign('header_left', $header_left);
        }elseif($entry == 'work'){
            $header_left['url'] = __ROOT__."/Home/Work/index";
            $this->assign('header_left', $header_left);
        }

        $uid = I('get.uid');
        if(!$uid){
            $uid=uid();
            $header_rink['url']=__ROOT__.'/Home/User/editMySpace?entry='.$entry;
            $header_rink['icon']='am-icon-pencil-square-o';
            $header_rink['text']='添加';
            $this->assign('header_rink',$header_rink);
        }

        $list = D('UserSpace')->where("uid=$uid")->order('create_time desc')->select();
        $this->assign('list', $list);

        $this->setHeader('党员发展');
        $this->setTitle('党员发展');
        $this->display("");
    }
    //space detail
    public function mySpaceDetail(){
        $entry = I('get.entry');
        $this->assign('entry', $entry);
        $id = I('id');
        $detail = D('UserSpace')->find($id);

        $this->assign('detail', $detail);
        $this->setHeader($detail['title']);
        $this->setTitle($detail['title']);
        $this->display();
    }
    // 添加个人空间数据
    public function editMySpace(){
        $entry = I('get.entry');
        $this->assign('entry',$entry);

        $id = I('get.id');
        if($id){ // 查看编辑
            $userSpace = D('UserSpace')->find($id);
            $this->assign('detail',$userSpace);
        }

        $this->setHeader('党员发展');
        $this->setTitle('党员发展');
        $this->display("");
    }
    public function editHead(){

        $this->setHeader('编辑头像');
        $this->setTitle('编辑头像');
        $this->display("");
    }


    //保存个人空间
    public function ajaxSaveMySpace(){
        $content = $_POST['content'];
        $title = I('title');
        if(!$content){
            ajaxMsg(1, "请填写发布内容");
        }
        if(!$title){
            ajaxMsg(1, "请填写发布标题");
        }

        $id = I('id');
        if($id){ // 查看编辑
//            ajaxMsg(1,to_json_string($_POST));
            D('UserSpace')->where(array('id'=>$id, 'uid'=>uid()))->save(array('intro'=>$content,'title'=>$title));
        }else{ // 新增
            D('UserSpace')->add(array('uid'=>uid(),'intro'=>$content,'title'=>$title,'create_time'=>time()));
        }
        ajaxMsg(0,"success");

    }

    // 发展历程
    public function developHistory(){
        $showBtn = I('get.showBtn');
        $this->assign('showBtn', $showBtn);
//
//        $uid = I('get.uid');
//        $user = D('UserView')->where("User.uid=$uid")->find();
//        $this->assign('user', $user);
//
//
//        $list = D('UserDevelopHistory')->where("uid=$uid")->order('create_time asc')->select();
//        $this->assign('list', $list);
//        $this->setHeader('发展历程');
//        $this->setTitle('发展历程');
//        $this->display("developHistory");

        $uid = I('get.uid');
        if(!$uid){
            $user = user();
            $uid=$user['uid'];
            $header_rink['url']=__ROOT__.'/Home/User/editMySpace';
            $header_rink['icon']='am-icon-pencil-square-o';
            $header_rink['text']='添加';
            $this->assign('header_rink',$header_rink);
            $this->assign('role_id',$user['role_id']);
        }else{
            $user = D('User')->find($uid);
            $this->assign('role_id',$user['role_id']);
        }

        $this->assign('uid', $uid);

        $party = D('UserView')->where("User.uid=$uid")->find();
        $this->assign('party', $party);

        $list = D('UserSpace')->where("uid=$uid")->order('create_time desc')->select();
        $this->assign('list', $list);

        $this->setHeader('党员发展历程');
        $this->setTitle('党员发展历程');
        $this->display("");

    }

    // 积极分子转为拟发展对象
    public function ajaxTobeDevelop(){
        $uid = I('uid');
        $user = D('User')->find($uid);
        if($user){
            // 修改到数据库
            $user['role_id'] = C('ROLE_TOBE_ID');
            $user['develop_date'] = time();
            D('User')->save($user);
            // 更新到session
            $user = user();
            $user['role_id'] =  C('ROLE_TOBE_ID');
            $user['role_name'] = "拟发展对象";
            resession_user($user);
            resession_user($user);
            // 在个人空间表添加一条记录
            $userSpace['uid'] = $uid;
            $userSpace['title'] = "转为拟发展对象";
            $userSpace['type'] = 1;
            $userSpace['create_time'] = time();
            D('UserSpace')->add($userSpace);
            ajaxMsg(0,'成功转为拟发展对象');
        }else{
            ajaxMsg(1,'该用户不存在');
        }
    }

    // 拟发展对象转为预备党员
    public function ajaxTobeReady(){
        $uid = I('uid');
        $user = D('User')->find($uid);
        if($user){
            // 修改到数据库
            $user['role_id'] = C('ROLE_READY_ID');
            $user['ready_date'] = time();
            D('User')->save($user);
            // 更新到session
            $user = user();
            $user['role_id'] =  C('ROLE_READY_ID');
            $user['role_name'] = "预备党员";
            resession_user($user);
            // 在个人空间表添加一条记录
            $userSpace['uid'] = $uid;
            $userSpace['title'] = "转为预备党员";
            $userSpace['type'] = 1;
            $userSpace['create_time'] = time();
            D('UserSpace')->add($userSpace);
            ajaxMsg(0,'成功转为预备党员');
        }else{
            ajaxMsg(1,'该用户不存在');
        }
    }

    // 预备党员转正
    public function ajaxTobeOfficial(){
        $uid = I('uid');
        $user = D('User')->find($uid);
        if($user){
            // 修改到数据库
            $user['role_id'] = C('ROLE_PARTY_ID');
            $user['official_date'] = time();
            D('User')->save($user);
            // 更新到session
            $user = user();
            $user['role_id'] =  C('ROLE_PARTY_ID');
            $user['role_name'] = "党员";
            resession_user($user);
            // 在个人空间表添加一条记录
            $userSpace['uid'] = $uid;
            $userSpace['title'] = "成为党员";
            $userSpace['type'] = 1;
            $userSpace['create_time'] = time();
            D('UserSpace')->add($userSpace);
            ajaxMsg(0,'成功转为党员');
        }else{
            ajaxMsg(1,'该用户不存在');
        }
    }

    /******************************** add by linjiahuan end **********************************/

}