<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Vote\Controller;
use Common\Controller\BaseController;

class IndexController extends BaseController {

    function _initialize(){
        parent::_initialize();
        $this->setHeader('投票活动');
    }

    public function index(){
        $this->check_wx_redirect('vote_index');//判断是否重定向登录
        $this->setTitle('投票活动');
        $user = user();
        $time = time();
        // 支部管理员
        $branchParam = checkPublishParameter($user['uid'], $user['branch_id']);
        // 党建办人员
        $partyBuildOffice = isPartyBuildOffice($user['uid']);
        // 支部或党建办人员可发
        if ($branchParam || $partyBuildOffice) {
            $this->assign('header_rink', array(
                'text' => '十',
                'url' => U('Index/publishVote'),
            )); // 发布权限
        }
        // 可查看角色
        $roleId = array($user['role_id']);

        // 如果是支部管理员或者党小组组长
        if ($branchParam || isPartyGroupLeader($user['uid'], $user['party_group_id'])) {
            array_push($roleId, 6);
        }
        // 看 => 条件如下 基本条件 记录是启用的并且 并且 是进行中的
        //  附加条件 如果 是指定支部的指定角色  或者 是符合可见范围条件 或者是 监督员
        $branchId = $user['branch_id']?$user['branch_id']:0;
        // a.limited_time >= '".$time."' AND
        $sql = "select a.id,a.cover,a.limited_time,a.published_time,a.title,a.uid,a.visiblerange,U.realname from vote as a LEFT JOIN user U ON a.uid=U.uid where a.`status`=1 AND
        (
         (a.id in (select b.vote_id from vote_role as b where b.role_id IN (".implode(',', $roleId).")) and (a.id in (select c.vote_id from vote_limit_branch as c where c.branch_id=".$branchId.")))
        or
        (a.id in (select d.vote_id from vote_supervisor d where d.uid = ".$user['uid']."))
        )
        order BY a.id DESC";
        // echo $sql;exit;
        $Model = new \Think\Model();
        $lists = $Model->query($sql);
        if (!empty($lists)) {
            // 过滤完成时显示的
            foreach ($lists as $k => $v) {
                // 结束后可见且进行中且已投票
                // if ($v['visiblerange'] === '2' && $v['limited_time'] > $time && checkActionLog($v['id'])) {
                //     unset($lists[$k]);
                // }
                if ($v['visiblerange'] === '3') {
                    // 如果是监督员跳过
                    if (checkSupervisor($v['id'])) {
                        continue;
                    // 不是支部管理员且无投票权
                    }elseif (!$branchParam && !checkUserIsAction($v['id'])) {
                        unset($lists[$k]);
                    }
                }elseif ($v['visiblerange'] === '4') {
                    // 监督员跳过
                    if (checkSupervisor($v['id'])) {
                        continue;
                    // 如果不是党建办人员且无投票权
                    }elseif (!$partyBuildOffice && !checkUserIsAction($v['id'])) {
                        unset($lists[$k]);
                    }
                }
            }
            //判断活动时间是否结束  6月5日修改
            foreach ($lists as $k => $item){
                if($item['limited_time'] > time()){
                    $listsing[] = $item;
                }else{
                    $listsed[] = $item;
                }
            }
        }
        $this->assign('branchId', $user['branch_id']); //
        $this->assign('listsing',$listsing);
        $this->assign('listsed',$listsed);
        $this->setTitle('投票活动');
        $this->display();
        // ajaxMsg(0, '', $lists);
    }

    // 弃用
    // public function index()
    // {
    //     $this->check_wx_redirect('vote_index');//判断是否重定向登录
    //     $user = user();
    //     $list = D('VoteRoleView')->fetchSql(true)->where(array(
    //         "Vote.id" => array('gt',50),
    //         "status"=>1,
    //         'limited_time'=>array('gt',time()),
    //         'VoteRole.role_id'=>$user['role_id'])
    //     )->group('VoteRole.vote_id')->order('published_time desc')->select();
    //     ajaxMsg(0, '', $list);
    //
    //     if (checkPublishParameter($user['uid'], $user['branch_id'])) {
    //         $this->assign('header_rink', array(
    //             'text' => '十',
    //             'url' => U('Index/publishVote'),
    //             // 'icon' => 'far fa-paper-plane',
    //         )); // 发布权限
    //     }
    //
    //     $this->assign('branchId', $user['branch_id']); //
    //     $this->assign('list',$list);
    //     $this->setTitle('投票活动');
    //     $this->display();
    // }


    public function vlist()
    {
        $this->setHeader('投票列表');
        $this->setTitle('投票列表');
        $this->display();
    }

    public function ajaxVote(){
        $this->check_wx_redirect();//判断是否重定向登录
        $id = I('id');
        $detail = D('Vote')->find($id);
        if($detail['status'] != 1){
            ajaxMsg(1,"抱歉，找不到该投票项目");
        }

        $timeNow = time();
        if($timeNow > $detail['limited_time']){
            ajaxMsg(1,"抱歉，投票已过期");
        }
        $select = I('vote');
        //判断是否投过票
        $logCount = D('VoteLogView')->where(array('VoteItem.vote_id'=>$id,'VoteLog.uid'=>uid()))->count();

        if($logCount>0){
            ajaxMsg(1,"抱歉，你已投过票了");
        }

        $roles = D('VoteRole')->where(array('vote_id'=>$id))->select();
        $count = 0;
        $user = user();
        foreach($roles as $role){
            if($role['role_id'] == $user['role_id'] || $role['role_id'] === '6'){
                $count ++;
            }
        }

        if($count == 0){
            ajaxMsg(1,"你没有该项目投票权限");
        }

        if($select){
            if(sizeof($select) > $detail['multiply_count']){
                ajaxMsg(1,"最多只能选择".$detail['multiply_count']."选项");
            }
            $_VoteItem = D('VoteItem');
            $_VoteLog = D('VoteLog');
            $voteItem = array();
            $i= 0;
            $time = time();
            foreach($select as $itemId){
                $_VoteItem->where(array('vote_id'=>$id,'id'=>$itemId))->setInc("count");
                $voteItem[$i]['uid'] = uid();
                $voteItem[$i]['item_id'] = $itemId;
                $voteItem[$i]['created_time'] = $time;
                $i++;
            }
            $_VoteLog->addAll($voteItem);
            ajaxMsg(0,"success",$voteItem);
        }else{
            ajaxMsg(1,"请先选择投票选项");
        }


    }

    public function detail()
    {
        $user = user();
        $id = I('id');
        $id = $id?$id:I('state');
        $this->check_wx_redirect('vote_detail',$id);//判断是否重定向登录
        D('Vote')->where(array('id'=>$id))->setInc("visited_count");

        $detail = D('VoteView')->find($id);
        // 多少人参与投票 弃用
        // $totalCount = D('VoteLogView')->where(array('VoteItem.vote_id'=>$id))->count();
        // $people = D('VoteLogView')->where(array('VoteItem.vote_id'=>$id))->group('VoteLog.uid')->count();
        // echo "<pre>";
        // var_dump($totalCount, $people, $res);exit;
        // $people = $people == 0?1:$people;
        // $detail['people_count'] = $totalCount/$people;
        // $detail['total_count'] = $totalCount;
        // 多少人参与投票
        $part = D('PeopleCountView')->where(array('VoteItem.vote_id' => $id))->getField('uid',true);
        // 去重
        $participate = array_unique($part);
        $detail['people_count'] = count($part);
        $detail['count'] = count($participate);

        $admGroup = array(
            'isSupervisor' => false, // 监督员
            'branchParam' => false,// 支部
            'partyBuildOffice' => false, // 党建办
            'isAction' => checkUserIsAction($id)
        );

        //输出条件限制
        $role = array();
        $initRoles = array(
            1 => '党员',
            2 => '预备党员',
            3 => '拟发展对象',
            4 => '积极分子',
            5 => '群众',
            6 => '党务工作者',
        );
        $roles = M('VoteRole')->where(array('vote_id'=>$id))->getField('role_id', true);
        foreach ($roles as $value) {
            $role[] = $initRoles[$value];
        }
        $detail['role_limit'] = implode('、', $role);

        $supervisor = D('VoteSupervisorView')->where(array(
            'VoteSupervisor.vote_id' => $id))->getField('realname', true);
        $detail['supervisor'] = implode('、', $supervisor);
        $voteLogs = D('VoteLogView')->where(array('VoteLog.uid'=>uid(),'VoteItem.vote_id'=>$id))->group('VoteLog.id')->select();
        $this->assign('voteLogs',$voteLogs);
        if(sizeof($voteLogs) > 0){
            $this->assign('voteLog',true);
        }

        $isSupervisor = M('VoteSupervisor')->where(array(
            'vote_id' => $id,
            'uid' => uid()
        ))->count();
        // 如果有记录则是 监督员
        if ($isSupervisor > 0) {
            $admGroup['isSupervisor'] = true;
        }

        // 支部管理员
        $admGroup['branchParam'] = checkPublishParameter($user['uid'], $user['branch_id']);
        // 党建办人员
        $admGroup['partyBuildOffice'] = isPartyBuildOffice($user['uid']);

        // echo "<pre>";
        // var_dump($admGroup);
        // exit;
        $this->assign('detail',$detail);
        // 管理员可见
        $this->assign('isPersonAdm', isPartyBuildOffice($user['uid']));
        $this->assign('admGroup', $admGroup);
        //获取投票选项
        $list = D('VoteItem')->where(array('vote_id'=>$id))->order("sort asc")->select();

        $this->assign('list',$list);
        $this->assign('visiblerange', array(
            1 => '正常完成时显示',
            2 => '投票结束后显示',
            3 => '管理员可见',
            4 => '党建办可见',
        ));
        $this->setHeader($detail['title']);
        $this->setTitle($detail['title']);
        $this->display();
    }

    public function pc_index()
    {
        $this->display();
    }

    public function publishVote(){
        $user = user();
        $role = M('Role')->select();
        array_push($role, array('id'=>6,'name'=>'党务工作者'));

        $this->assign('initUser', $this->getUserByBranchIds($user['branch_id']));
        // 可看角色范围
        $this->assign('branchs', getBranchInfo());
        $this->assign('roles', $role);
        $this->assign('init', array(
            'm' => date('m') . '月份优秀党员评选（'. $user['branch_name'] .'）',
            'branchName' =>  $user['branch_name'],
            'branchId' =>  $user['branch_id'],
            'cover' => '/Public/Common/img/common.png',
        ));
        $this->display();
    }


    public function getUserBybranchId(){
        $ids = I('get.branchId');
        if (!empty($ids)) {
            $branchs = $this->getUserByBranchIds($ids);
            ajaxMsg(0, 'success', $branchs);
        }
        ajaxMsg(1, '系统繁忙请稍后再试！');
    }

    /**
     * 移动端发布
     */
    public function ajaxSave(){
        $post = I('post.');
        $_vote = D('Vote');
        $res = $_vote->ajaxSave($post);
        if (isset($res['id'])) {
            ajaxMsg(0, 'success', $res);
        }else {
            ajaxMsg(1, $res);
        }
    }

    protected function getUserByBranchIds($ids){
        return M('User')->field('uid,realname')
        ->where(array(
            'status' => 1,
            'role_id' => 1,
            'branch_id' => array('in', $ids))
        )->select();
    }

    /**
     * 投票封面上传
     */
    public function ajaxSaveVoteCover(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg','bmp','tiff','SVG','svg','TIFF','BMP','JPG', 'GIF', 'PNG', 'JPEG');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = 'Img/Vote/Cover/'; // 设置附件上传（子）目录

        $info = $upload->uploadOne($_FILES['file']);
        if (!$info) {// 上传错误提示错误信息
            ajaxMsg(1, "请上传适当大小的封面");
        } else {
            $path = $info['savepath'] . $info['savename'];

            //文件上传记录
            $filel = array(
                'savename' => $info['savename'],
                'type' => $info['type'],
                'savepath' => '/' . $info['savepath'],
                'uid' => uid(),
                'ext' => $info['ext'],
                'size' => $info['size'],
                'create_time' => time()
            );
            if (M('Filelist')->add($filel)) {
                ajaxMsg(0, 'success', array(
                    'save_path' => '/' . $path,
                    'show_path' => __ROOT__ . '/Uploads/' . $path
                ));
            }
            ajaxMsg(1, '服务繁忙！请稍后重试');
        }
    }

}
