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
use Common\Util\AuthUtils;

class StudyController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
        $this->setHeader('学习交流'); // 导航栏标题
        $this->setTitle('学习交流');  // 网页标签标题
    }

    public function index()
    {
        $this->check_wx_redirect("study_index");
        $header_left['url'] = __ROOT__."/Home/Index/index";
        $this->assign('header_left', $header_left);
        $this->setHeader('学习交流'); // 导航栏标题
        $this->setTitle('学习交流');  // 网页标签标题
        $this->display();
    }

    public function forum()
    {
        $this->check_wx_redirect("study_forum");
        $this->setHeader('学习交流'); // 导航栏标题
        $this->setTitle('学习交流');  // 网页标签标题
        $this->display();
    }

    public function weixinde_list(){
        $this->check_wx_redirect("study_weixinde_list");
        $this->setHeader('微心得'); // 导航栏标题
        $this->setTitle('微心得');  // 网页标签标题
        $this->display();
    }

    public function weixinde_detail(){
        $this->check_wx_redirect("study_weixinde_detail");
        $this->setHeader('微心得详情'); // 导航栏标题
        $this->setTitle('微心得详情');  // 网页标签标题
        $this->display();
    }


    /******************************** add by linjiahuan begin **********************************/
    // 微心得列表数据
    public function ajaxLoadingWeixinde(){
        $this->check_wx_redirect();
        // 获取分页参数(上一次的最后一条数据的排序字段)
        $published_at=I('post.published_at');
        if ($published_at == 0 || $published_at == null) {
            $published_at=time();
        }
//        ajaxMsg(1,'aaaaaaaaaaaaaa','');
        $where = array('StudyStandingBook.content_type'=>2, 'StudyStandingBook.create_time'=>array('lt', $published_at));
        $page = D('StudyStandingBookView')->findPage($where, 15, 'StudyStandingBook.create_time desc');

        ajaxMsg(0,to_json_string($page['list']),$page['list']);
    }

    /**
     * 备份旧版学习台账
     */
//    public function standingBook(){
//        $header_left['url'] = __ROOT__."/Home/Study/index";
//        $this->assign('header_left', $header_left);
//
//        // 获取台账类型
//        $type = I('get.type');
//        $type = $type?$type:I('state');
//        $this->check_wx_redirect("study_standingBook",$type);
//        $this->assign('type', $type);
//        if($type == 0){
//            // 设置导航栏右上角功能
//            $header_rink['url']=__ROOT__.'/Home/Study/standingBookEdit?type=0';
//            $header_rink['icon']='am-icon-pencil-square-o';
//            $header_rink['text']='录入';
//            $this->assign('header_rink',$header_rink);
//
//            $this->setHeader('党员先锋台账'); // 导航栏标题
//            $this->setTitle('党员先锋台账');  // 网页标签标题
//        }else if($type == 1){
//            // 设置导航栏右上角功能
//            $header_rink['url']=__ROOT__.'/Home/Study/standingBookEdit?type=1';
//            $header_rink['icon']='am-icon-pencil-square-o';
//            $header_rink['text']='录入';
//            $this->assign('header_rink',$header_rink);
//
//            $this->setHeader('支部堡垒台账'); // 导航栏标题
//            $this->setTitle('支部堡垒台账');  // 网页标签标题
//        }else if($type == 2){
//            // 设置导航栏右上角功能
//            $header_rink['url']=__ROOT__.'/Home/Study/standingBookEdit?type=2';
//            $header_rink['icon']='am-icon-pencil-square-o';
//            $header_rink['text']='录入';
//            $this->assign('header_rink',$header_rink);
//
//            $this->setHeader('领导表率台账'); // 导航栏标题
//            $this->setTitle('领导表率台账');  // 网页标签标题
//        }else{
//            // 设置导航栏右上角功能
//            $header_rink['url']=__ROOT__.'/Home/Study/standingBookEdit?type=3';
//            $header_rink['icon']='am-icon-pencil-square-o';
//            $header_rink['text']='录入';
//            $this->assign('header_rink',$header_rink);
//
//            $this->setHeader('党组带头台账'); // 导航栏标题
//            $this->setTitle('党组带头台账');  // 网页标签标题
//        }
//
//        $this->display();
//    }

    // 个人台账(党员先锋台账)
    public function standingBook(){

        // 获取台账类型
        $type = I('get.type');
        $type = $type?$type:I('state');
        $this->check_wx_redirect("study_standingBook",$type);
        $this->assign('type', $type);
        if($type == 0){
            $entry = I('get.entry');
            if($entry == 'info'){
                $header_left['url'] = __ROOT__."/Home/User/info";

                $header_rink['url']=__ROOT__.'/Home/Study/standingBookEdit?type=0&entry=info';
            }else{
                $header_left['url'] = __ROOT__."/Home/Study/index";

                $header_rink['url']=__ROOT__.'/Home/Study/standingBookEdit?type=0';
            }
            // 设置导航栏左上角功能
            $this->assign('header_left', $header_left);

            $header_rink['icon']='am-icon-pencil-square-o';
            $header_rink['text']='录入';
            // 设置导航栏右上角功能
            $this->assign('header_rink',$header_rink);

            $this->setHeader('党员先锋台账'); // 导航栏标题
            $this->setTitle('党员先锋台账');  // 网页标签标题
        }else if($type == 1){
            $header_left['url'] = __ROOT__."/Home/Study/index";
            $this->assign('header_left', $header_left);

            // 设置导航栏右上角功能
            $header_rink['url']=__ROOT__.'/Home/Study/standingBookEdit?type=1';
            $header_rink['icon']='am-icon-pencil-square-o';
            $header_rink['text']='录入';
            $this->assign('header_rink',$header_rink);

            $this->setHeader('支部堡垒台账'); // 导航栏标题
            $this->setTitle('支部堡垒台账');  // 网页标签标题
        }else if($type == 2){
            $header_left['url'] = __ROOT__."/Home/Study/index";
            $this->assign('header_left', $header_left);

            // 设置导航栏右上角功能
            if(AuthUtils::check_adm_post(user(), 1)){
                $header_rink['url']=__ROOT__.'/Home/Study/standingBookEdit?type=2';
                $header_rink['icon']='am-icon-pencil-square-o';
                $header_rink['text']='录入';
                $this->assign('header_rink',$header_rink);
            }

            $this->setHeader('领导表率台账'); // 导航栏标题
            $this->setTitle('领导表率台账');  // 网页标签标题
        }else{

            // 设置导航栏右上角功能
            $header_rink['url']=__ROOT__.'/Home/Study/standingBookEdit?type=3';
            $header_rink['icon']='am-icon-pencil-square-o';
            $header_rink['text']='录入';
            $this->assign('header_rink',$header_rink);

            $this->setHeader('党组带头台账'); // 导航栏标题
            $this->setTitle('党组带头台账');  // 网页标签标题
        }

        $this->display();
    }

    // 支部台账之 总支列表
    public function standingBookBranch(){
        $hqs = D('PartyBranchHqView')->group("PartyBranchHq.id")->order('PartyBranchHq.id asc')->select();

        $list = D('PartyBranchView')->where('PartyBranch.branch_hq_id is null')->group("PartyBranch.id")->order('PartyBranch.id asc')->select();

        $partybranchId = C('ROLE_PARTY_ID');
        $counts = D('User')->where("role_id=$partybranchId and status=1")->field("count(*) as count,branch_id")->group("branch_id")->order('branch_id asc')->select();

        $countMap = array();
        foreach($counts as $count){
            $countMap[$count['branch_id'].''] = $count['count'];
        }

        $sjId = C('POST_SJ_ID');
        foreach($list as $index=>$item) {
            $count = $countMap[$item['id'].''];
            $list[$index]['party_count'] = $count?$count:0;
            $branchId = $list[$index]['id'];
            $user = D('User')->where("post_id=$sjId and branch_id=$branchId")->find();
            $list[$index]['realname']=$user['realname'];
            $list[$index]['type']='branch';
        }

        foreach($hqs as $index=>$item){
            $item['branch_count'] = D('PartyBranch')->where(array('branch_hq_id'=>$item['id']))->count();
            $item['type']='hq';
            $hqs[$index]=$item;
        }

        $this->assign('list', array_merge($hqs, $list));

        $this->setHeader('支部堡垒台账'); // 导航栏标题
        $this->setTitle('支部堡垒台账');  // 网页标签标题
        $this->display();
    }
    // 支部台账之 总支下分支列表
    public function standingBookBranch2(){
        $hq_id = I('id');
        $detail = D('PartyBranchHq')->find($hq_id);
        $this->assign('detail',$detail);
        $list = D('PartyBranchView')->where(array('branch_hq_id'=>$hq_id))->group("PartyBranch.id")->order('PartyBranch.id asc')->select();

        $partybranchId = C('ROLE_PARTY_ID');
        $counts = D('User')->where("role_id=$partybranchId and status=1")->field("count(*) as count,branch_id")->group("branch_id")->order('branch_id asc')->select();

        $countMap = array();
        foreach($counts as $count){
            $countMap[$count['branch_id'].''] = $count['count'];
        }

        $sjId = C('POST_SJ_ID');
        foreach($list as $index=>$item) {
            $count = $countMap[$item['id'].''];
            $list[$index]['party_count'] = $count?$count:0;
            $branchId = $list[$index]['id'];
            $user = D('User')->where("post_id=$sjId and branch_id=$branchId")->find();
            $list[$index]['realname']=$user['realname'];
        }
        $this->assign('list', $list);

        $this->setHeader('支部堡垒台账'); // 导航栏标题
        $this->setTitle('支部堡垒台账');  // 网页标签标题
        $this->display();
    }
    // 支部台账
    public function standingBook1(){
        $this->check_wx_redirect("study_standingBook",1);

        $this->assign('branchId', I('branchId'));

        $this->setHeader('支部堡垒台账'); // 导航栏标题
        $this->setTitle('支部堡垒台账');  // 网页标签标题

        $this->display();
    }

    // 学习台账
    public function standingBook2(){
        $header_left['url'] = __ROOT__."/Home/Study/index";
        $this->assign('header_left', $header_left);

        $tabIndex = I('get.tabIndex');
        $this->assign('tabIndex', $tabIndex);

        // 获取台账类型
        $type = I('get.type');
        $type = $type?$type:I('state');
        $this->check_wx_redirect("study_standingBook",$type);
        $this->assign('type', $type);

        // 是否为市局党组成员或班子，是才拥有添加功能
        if(AuthUtils::check_adm_post(user(),1)){
            // 设置导航栏右上角功能
            $header_rink['url']=__ROOT__.'/Home/Study/standingBookEdit?type='.$type;
            $header_rink['icon']='am-icon-pencil-square-o';
            $header_rink['text']='录入';
            $this->assign('header_rink',$header_rink);
        }

        // 获取党组列表
        $sql = "select Organization.id, Organization.name,
              (select count(*) from study_standing_book ssb where ssb.organization_id=Organization.id and ssb.type=4) standing_book_count
              from party_organization Organization order by Organization.id desc ";
        $list = $list = D()->query($sql);;
        $this->assign('list',$list);

        $this->setHeader('党组带头台账'); // 导航栏标题
        $this->setTitle('党组带头台账');  // 网页标签标题

        $this->display();
    }

    public function standingBook3(){

        $organizationId = I('get.organizationId');
        $this->assign('organizationId', $organizationId);

        $header_left['url'] = __ROOT__."/Home/Study/standingBook2?tabIndex=1";
        $this->assign('header_left', $header_left);

        $this->check_wx_redirect("study_standingBook3",1);

        $user = user();
        if($user['branch_id']){
            $partyOrganization = D('PartyOrganizationBranchs')->where(array('branch_id'=>$user['branch_id']))->find();
            if($partyOrganization['organization_id'] == $organizationId){
                // 设置导航栏右上角功能
                $header_rink['url']=__ROOT__.'/Home/Study/standingBookEdit?type=4&organizationId='.$partyOrganization['organization_id'];
                $header_rink['icon']='am-icon-pencil-square-o';
                $header_rink['text']='录入';
                $this->assign('header_rink',$header_rink);
            }
        }

        $this->setHeader('基层局领导班子台账'); // 导航栏标题
        $this->setTitle('基层局领导班子台账');  // 网页标签标题

        $this->display();
    }

    public function ajaxStandingBookLoading(){
        $this->check_wx_redirect();
//        $attendTime = I('attend_time');
        $createTime = I('create_time');
        $type = I('type');
        $uid = I('uid');

        if(!$uid){ // 为空时，表明是查看自己的学习台账
            $uid = uid();
        }

        if ($createTime == 0 || $createTime == null) {
            $createTime=time();
        }

//        $where = array('StudyStandingBook.uid'=>$uid, 'StudyStandingBook.type'=>$type, 'StudyStandingBook.create_time'=>array('lt',$createTime));
//        $page = D('StudyStandingBookView')->findPage($where, 15, 'StudyStandingBook.create_time desc');

         if($type == 0){ // 个人学习台账
             $where = array('StudyStandingBook.uid'=>$uid, 'StudyStandingBook.type'=>0, 'StudyStandingBook.create_time'=>array('lt',$createTime));
             $page = D('StudyStandingBookView')->findPage($where, 15, 'StudyStandingBook.create_time desc');
         }else if($type == 1){ // 支部学习台账
             $branchId = I('branchId');
             $where = array('StudyStandingBook.type'=>0, 'StudyStandingBook.branch_id'=>$branchId, 'StudyStandingBook.create_time'=>array('lt',$createTime));
             $page = D('StudyStandingBookView')->findPage($where, 15, 'StudyStandingBook.create_time desc');
//             ajaxMsg(1,to_json_string($page['list']),$page['list']);
         }else if($type == 2){ // 领导表率台账
             $where = array('StudyStandingBook.type'=>2, 'StudyStandingBook.create_time'=>array('lt',$createTime));
             $page = D('StudyStandingBookView')->findPage($where, 15, 'StudyStandingBook.create_time desc');
         }else if($type == 3){ // 领导表率台账
             $where = array('StudyStandingBook.type'=>3, 'StudyStandingBook.create_time'=>array('lt',$createTime));
             $page = D('StudyStandingBookView')->findPage($where, 15, 'StudyStandingBook.create_time desc');
         }else if($type == 4){ // 领导表率台账
             $organizationId = I("organizationId");
             $where = array('StudyStandingBook.type'=>4, 'StudyStandingBook.organization_id'=>$organizationId, 'StudyStandingBook.create_time'=>array('lt',$createTime));
             $page = D('StudyStandingBookView')->findPage($where, 15, 'StudyStandingBook.create_time desc');
         }

        ajaxMsg(0,to_json_string($page['list']),$page['list']);

    }

    // 录入学习台账
    public function standingBookDetail(){
        $id = I('get.id');
        $id = $id?$id:I('state');
        $this->check_wx_redirect("study_standingBook",$id);
        $studyStandingBook = D('StudyStandingBookView')->find($id);
        $this->assign('studyStandingBook', $studyStandingBook);

        $isOthers = I('get.isOthers');
        if($isOthers != 1){ // 访问时没有强制设置不可编辑(支部台账访问时强制不可编辑)
            if(uid() != $studyStandingBook['uid']){
                $isOthers = 1;
            }else{
                $isOthers = 0;
            }
        }
        $this->assign('isOthers', $isOthers);

        // 设置标题
        $this->setHeader('学习台账详情'); // 导航栏标题
        $this->setTitle('学习台账详情');  // 网页标签标题
        $this->display();
    }

    // 录入学习台账
    public function standingBookEdit(){
        $entry = I('get.entry');
        $this->assign('entry', $entry);
        $id = I('get.id');
        $organizationId = I('get.organizationId');
        $this->assign('organizationId', $organizationId);

        if($id){
            $studyStandingBook = D('StudyStandingBookView')->find($id);
            
            // 设置标题
            $this->setHeader('修改学习台账'); // 导航栏标题
            $this->setTitle('修改学习台账');  // 网页标签标题
        }else{
            $type = I('get.type');
            $studyStandingBook['type'] = $type;
            // 设置标题
            $this->setHeader('录入学习台账'); // 导航栏标题
            $this->setTitle('录入学习台账');  // 网页标签标题
        }
        $this->assign('studyStandingBook', $studyStandingBook);
        
        $this->display();
    }

    // 删除学习台账
    public function ajaxDeleteStandingBook(){
        $this->check_wx_redirect();
        $id = I('id');
        D('StudyStandingBook')->where("id=$id")->delete();
        ajaxMsg(0,"success");
    }

    // 保存学习台账
    public function ajaxSaveStandingBook(){
        $this->check_wx_redirect();
        $theme = I('theme');
        $host = I('host');
        $attendTime = I('attendTime');
        $attendance = I('attendance');
        $attendee = I('attendee');
        $type = I('type');
        $contentType = I('content_type');
        $classify = I('classify');
        // ajaxMsg(1,"type = ".$type);
        $content = $_POST['content'];
        if(!$theme){
            ajaxMsg(1, "请填写主题");
        }
        if(!$attendTime){
            ajaxMsg(1, "请填写举办日期");
        }
        if($classify == 1 && !$attendance){
            ajaxMsg(1, "请填写出席人数");
        }
        if(!$attendee){
            ajaxMsg(1, "请填写出席名单");
        }

        $id = I('id');
        $user = user();
        $branchId = $user['branch_id'];
        $uid = $user['uid'];
        if($id){ // 修改
            $item = array('classify'=>$classify, 'content_type'=>$contentType, 'theme'=>$theme,'host'=>$host,'attend_time'=>strtotime($attendTime), 'attendance'=>$attendance, 'attendee'=>$attendee, 'content'=>$content);
            D('StudyStandingBook')->where(array('id'=>$id))->save($item);
        }else{ // 新增
            $item = array('classify'=>$classify, 'content_type'=>$contentType, 'type'=>$type, 'uid'=>$uid, 'branch_id'=>$branchId, 'create_time'=>time(),'theme'=>$theme,'host'=>$host,'attend_time'=>strtotime($attendTime), 'attendance'=>$attendance, 'attendee'=>$attendee, 'content'=>$content);
            $item['organization_id'] = D('PartyOrganizationBranchs')->where(array('branch_id'=>$branchId))->find()['organization_id'];
            D('StudyStandingBook')->add($item);
        }
        ajaxMsg(0,"success");
    }

    /******************************** add by linjiahuan end **********************************/

}