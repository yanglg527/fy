<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Home\Controller;
use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;

class WorkController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
        $this->setHeader('党务管理');
        $this->setTitle('党务管理');
    }

    public function index()
    {   
        $header_left['url'] = __ROOT__."/Home/Index/index";
        $this->assign('header_left', $header_left);
        $this->display();
    }

    public function pay()
    {
        $this->setHeader('党费缴纳');
        $this->setTitle('党费缴纳');
        $this->display();
    }

    /******************************** add by linjiahuan begin **********************************/

    // 各季度党费列表
    public function partyFeeList(){

        // 设置标题
        $this->setHeader('党费明细'); // 导航栏标题
        $this->setTitle('党费明细');  // 网页标签标题
        $this->display();
    }
    //
    public function standingBookEdit(){
        // 静态页面暂时变量
        $this->assign('temp',I('get.temp'));

        // 设置标题
        $this->setHeader('录入学习台账'); // 导航栏标题
        $this->setTitle('录入学习台账');  // 网页标签标题
        $this->display();
    }

    // 转正意见征集列表
    public function officialCollect(){
        $this->check_wx_redirect('official_collect');//判断是否重定向登录

        // 设置标题
        $this->setHeader('转正意见征集'); // 导航栏标题
        $this->setTitle('转正意见征集');  // 网页标签标题
        $this->display();
    }
    // 加载转正意见征集列表数据
    public function ajaxLoadingOfficialCollect(){
        $createTime = I('create_time');
        if ($createTime == 0 || $createTime == null) {
            $createTime=time();
        }

        $user = user();
        $uid = $user['uid'];
        $branchId = $user['branch_id'];

        $where = array('UserOfficialCollect.branch_id'=>$branchId, 'UserOfficialCollect.status'=>'1', 'UserOfficialCollect.create_time'=>array('lt',$createTime));
//        ajaxMsg(1,"aaaaaaaa","");
        $page = D('UserOfficialCollectView')->findPage($where, 15, 'UserOfficialCollect.create_time desc');
        if($page){
            $list = $page['list'];
            foreach($list as $index=>$item){
                $userAdvice = D('UserOfficialAdviceLog')->where(array('collect_id'=>$item['id'], 'uid'=>$uid))->find();
                if($userAdvice){
                    $item['advice'] = $userAdvice['advice'];
                }else{
                    $item['advice'] = -1;
                }
                $list[$index] = $item;
            }
            $page['list'] = $list;
        }

        D('UserOfficialCollectView')->find_page();
        ajaxMsg(0,to_json_string($page['list']),$page['list']);
    }

    // 意见征集详情
    public function officialCollectDetail(){
        // 意见征集id
        $collectId = I('get.id');

        $collectId = $collectId?$collectId:I('state');
        $this->check_wx_redirect('official_collect_detail',$collectId);//判断是否重定向登录

        $this->assign('collectId', $collectId);
        // 意见征集数据
        $userOfficialCollect = D('UserOfficialCollect')->find($collectId);
        $this->assign('detail', $userOfficialCollect);
        // 转正者
        $officialUser = D('User')->find($userOfficialCollect['uid']);
        $this->assign('officialUser', $officialUser);
        // 此支部未发表意见人数
        $branchId = $userOfficialCollect['branch_id'];
        $where = array('branch_id'=>$branchId, 'role_id'=>1);
        $partyCount = D('User')->where($where)->count();
        $this->assign('notAdviceCount', $partyCount - $userOfficialCollect['agree_count'] - $userOfficialCollect['disagree_count']);
        // 访问者的意见情况
        $accessUid = uid();
        $userOfficialAdviceLog = D('UserOfficialAdviceLog')->where(array('uid'=>$accessUid, 'collect_id'=>$collectId))->find();
        if($userOfficialAdviceLog){
            $this->assign('adviceStatus', $userOfficialAdviceLog['advice']);
        }else{
            $this->assign('adviceStatus', 2);
        }

        // 党员发展数据列表
        $uid = $userOfficialCollect['uid'];
        $list = D('UserSpace')->where("uid=$uid")->order('create_time desc')->select();
        $this->assign('list', $list);

        // 设置标题
        $this->setHeader('意见征集详情'); // 导航栏标题
        $this->setTitle('意见征集详情');  // 网页标签标题
        $this->display();
    }
    // 意见征集详情之 个人意见明细
    public function  ajaxLoadingOfficialCollectDetail(){
        $createTime = I('create_time');
        if ($createTime == 0 || $createTime == null) {
            $createTime=time();
        }

        $collectId = I('collectId');
        $where = array('collect_id'=>$collectId, 'create_time'=>array('lt', $createTime));
        $page = D('UserOfficialAdviceLogView')->findPage($where, 15, 'UserOfficialAdviceLog.create_time desc');

        ajaxMsg(0, to_json_string($page['list']), $page['list']);
    }
    // 提交转正意见
    public function ajaxSubmitOfficialAdvice()
    {
        $this->check_wx_redirect('official_collect');//判断是否重定向登录

        $advice = I('advice');
        $comment = I('comment');
        $collectId = I('id');

        // 意见征集数据
        $userOfficialCollect = D('UserOfficialCollect')->find($collectId);

        $user = user();
        $branchId = $user['branch_id'];
        $uid = uid();
        $userOfficialAdviceLog = D('UserOfficialAdviceLog')->where(array('uid'=>$uid, 'collect_id'=>$collectId))->find();
//        ajaxMsg(1,"bid = " . $branchId . " bid2 = " .  $userOfficialCollect['branch_id'] . "role = " . $user['role_id']);
        if($userOfficialAdviceLog){
            ajaxMsg(1,"你已提交过意见了，请不要重复提交");
        }else{
            if($branchId == $userOfficialCollect['branch_id'] && $user['role_id'] == 1){
                $userOfficialAdviceLog['uid'] = $uid;
                $userOfficialAdviceLog['collect_id'] = $collectId;
                $userOfficialAdviceLog['create_time'] = time();
                $userOfficialAdviceLog['advice'] = $advice;
                $userOfficialAdviceLog['comment'] = $comment;
                D('UserOfficialAdviceLog')->add($userOfficialAdviceLog);

                // 累计同意人数或反对人数
                if($advice == 1){ // 同意
                    D('UserOfficialCollect')->where(array("id"=>$collectId))->setInc("agree_count");
                }else{
                    D('UserOfficialCollect')->where(array("id"=>$collectId))->setInc("disagree_count");
                }

                // 标记待办事项为已办
                add_todo_user_status($collectId);

                ajaxMsg(0,"发表成功");
            }else{
                ajaxMsg(1,"对不起,只有同一支部的党员才可提交意见");
            }
        }
    }

    /******************************** add by linjiahuan end **********************************/

}