<?php
namespace Admin\Controller;

use Admin\Model\AdminAuthGroupAccessViewModel;
use Admin\Model\AdminAuthRuleViewModel;
use Common\Controller\BaseController;
use Think\Controller;
use Admin\Util\AdminUtil;

/**
 * 系统设置
 * Class SystemController
 * @package Admin\Controller
 */
class SystemController extends BaseAdminController
{
    function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->set_sidebar_module('System');
    }

    public function index()
    {
        $this->ruleList();
    }

    /****模块 start******/
    //规则列表
    public function moduleList()
    {

        $this->set_sidebar_sub('moduleList');
        $keyword = I('keyword');
        if($keyword){
            $page = D('AdminModulesView')->findPage("concat(modulename) like '%$keyword%'",15,'id desc');
            $this->assign('keyword', $keyword);
        }else{
            $page = D('AdminModulesView')->findPage('',15,'id desc');
        }
       // echo curPageURL();
//        unset($_SESSION['wxpic']);
//        var_dump($_SESSION['wxpic']);
////       var_dump( C('WEIXINQY_CONFIG')['CORP_ID']);
//        if(!isset($_SESSION['wxpic']['access_token'])||!isset($_SESSION['wxpic']['expire_time'])||(time()-$_SESSION['wxpic']['expire_time']>7200)){
//            $_SESSION['wxpic']['expire_time'] =time();
//            $token = get_qytoken(C('WEIXINQY_CONFIG')['CORP_ID'],C('WEIXINQY_CONFIG')['SECRET']);
////            var_dump($token);
//            if($token){
//                $_SESSION['wxpic']['access_token'] = $token;
//
//                $ticket = get_qyticket($token);//存到session
//                if($ticket){
//
//                    $signature = makeSignature($ticket,$_SESSION['wxpic']['expire_time'],'Wm3WZYTjyhhh0wzccnW',curPageURL());
//                    $_SESSION['wxpic']['signature'] =$signature;
//                    $_SESSION['wxpic']['appId']=C('WEIXINQY_CONFIG')['CORP_ID'];
//                    $_SESSION['wxpic']['noncestr']='Wm3WZYTjyhhh0wzccnW';
//                }
//            }
//        }
//        $this->assign('wxpic_session',$_SESSION['wxpic']);
        $this->assign('page', $page);
        //读取模块信息
        $this->display();
    }
    public  function ajaxSaveTaizhang(){
        $user = user();
        $spec_id = I('spec_id',0);
        $tag_id = I('tag_id',24);
        $norm_id = I('group_id',14);
        $type_id = I('type_id',1);
        $title  = I('title','woshihoutaiceshi');
        $content = I('content','woshicontent');
        $address = I('address','suibianbba');
        $branch_name =I('branch_name','dangzhibu');
        $branch_id =I('branch_id','56');
        $imgarr =$_POST['imgarr'];
        $publishName = I('publish_name');
        file_put_contents('./',var_export($_POST,true));
        exit;
        //得到图片路径后存入数据库 ,配置问题后面再改
        $tz_id = D("Taizhang")->add(array("uid"=>$user["uid"],"title"=>$title,"publish_name"=>$user["name"],"publish_time"
        =>date('Y-m-d',time()),"type"=>$type_id,"type2"=>$spec_id,"address"=>$address,'status'=>'-1',
            "tag_id"=>$tag_id,"norm_id"=>$norm_id,"party_name"=>$branch_name,"template_id"=>3,'publish_name'=>$publishName,'tz_content'=>$content,
            "organization_id"=>$user['organization_id'],"branch_id"=>$user['branch_id'],"add_uid"=>$user['uid'],"create_time"=>time()));
        if(!$tz_id){
            ajaxMsg(0,'信息上传失败');
        }
        if($imgarr){
            $tztype = C('tztype')[$type_id];

            $path =C('tzpath').$tztype;
            $tzpath=array();
//            foreach($imgarr as $k=>$v){
//                $res = testimg( $v,$path);
//
//
//                if($res['flag']){
//                    $or_path = $res['path'];
//                    //还要生成缩略图
//                    $image = new \Think\Image();
//                    $start_str = substr($or_path,0,strlen($or_path)-4);
//                    $last_str = substr($or_path,-4,4);
//                    $thumb_path = $start_str.'_thumb'.$last_str;
//                    $image->open($or_path);
//                    $image->thumb(240, 250,\Think\Image::IMAGE_THUMB_CENTER)->save($thumb_path);
//
//                    //这里是存入数据库的操作，不过多了个点，布置服务器是不需要这个点的,到时候再改一下
//                    $tzpath= substr($res['path'],1,strlen($res['path']));
//                    //图片上传
//                    $tzc_res = D('TaizhangContents')->add(array("type"=>4,"image"=>$tzpath,
//                        "taizhang_id"=>$tz_id));
//                    if(!$tzc_res){
//                        ajaxMsg(0,'图片数据上传失败');
//                    }
//                }
//                else{
//                    ajaxMsg(0,$res['message']);
//                }
//                if($k==0){
//                    //设置默认封面
//                    D('Taizhang')->where(array('id'=>$tz_id))->save(array('cover'=>$tzpath));
//                }
//            }
            //到这里就插入数据库完成
            //加分系统(先不插入) update_user_score($uid, 5, 1,'上传台账');
        }
        ajaxMsg(1,'新增台账成功');

    }
    public function ajaxGetTaiZhangImg(){

        $or_path_arr = array();
        $imgarr =$_POST['imgarr'];
        $type_id = I('type_id',1);
        $tztype = C('tztype')[$type_id];
        $path =C('tzpath').$tztype;

        foreach($imgarr as $k=>$v){

            $res = downloadWxImg($v,$path);
            file_put_contents('./wx1.txt',var_export($res,true));
            if($res['flag']){
                $or_path = $res['path'];
                file_put_contents('./wx2.txt',var_export($or_path,true));

                //还要生成缩略图
                $image = new \Think\Image();
                $start_str = substr($or_path,0,strlen($or_path)-4);
                $last_str = substr($or_path,-4,4);
                $thumb_path = $start_str.'_thumb'.$last_str;
                $image->open($or_path);
                $image->thumb(240, 250,\Think\Image::IMAGE_THUMB_CENTER)->save($thumb_path);

                //这里是存入数据库的操作，去除 ./uploads 前的那个点。
                $tzpath= substr($res['path'],1,strlen($res['path']));
                $or_path_arr[] = $tzpath;


            }
            else{
                ajaxMsg(0,$res['message']);
            }
        }
        $_SESSION['wxpic']['pic_path_arr'] = $or_path_arr;
        echo json_encode(1002,'ok',$or_path_arr);

    }
    //添加规则页面
    public function moduleAdd()
    {
        $this->set_sidebar_sub('moduleList');
        $id = I('get.id');
        if($id){//如果是编辑
            $detail=D('AdminModules')->find($id);
            $this->assign('detail', $detail);
        }

        $this->display();
    }
    //添加规则
    public function ajaxSaveModule(){
        $modulename = I('modulename');
        $id = I('id');
        if($modulename){
            //查找是否有存在的规则
            $rule = array(
                'moduleName'=>$modulename,
            );

            //如果是保存
            if($id){
                D('AdminModules')->where(array('id' => $id))->save($rule);
            }else{
                D('AdminModules')->add($rule);
            }
            ajaxMsg(0,'保存成功');
        }else{
            ajaxMsg(1, "请填写模块名称");
        }

    }

    //添加规则
    public function ajaxDecModule(){
        $id = I('id');
        $count = D('AdminAuthRule')->where("mid=$id")->count();
        if($count > 0 ){
            ajaxMsg(1, "规则列表还有在使用该模块的，删除失败");
        }

        D('AdminModules')->where(array('id'=>$id))->delete();
        ajaxMsg(0, "已删除");

    }

    /**** 模块 end*****/



    /****规则 start******/
    //规则列表
    public function ruleList()
    {
        $this->set_sidebar_sub('ruleList');
        $keyword = I('keyword');
        if($keyword){
            $page = D('AdminAuthRuleView')->findPage("concat(name,title,modulename) like '%$keyword%'",15,'id desc');
            $this->assign('keyword', $keyword);
        }else{
            $page = D('AdminAuthRuleView')->findPage('',15,'id desc');
        }

        $this->assign('page', $page);
        //读取模块信息
        $this->display('ruleList');
    }

    //添加规则页面
    public function ruleAdd()
    {
        $this->set_sidebar_sub('ruleList');
        $id = I('get.id');
        if($id){//如果是编辑
            $detail=D('AdminAuthRule')->find($id);
            $this->assign('detail', $detail);
        }

        $this->assign('p', I('get.p',1));
       $modules= D('AdminModules')->select();
        $this->assign('modules', $modules);
        $this->display();
    }
    //添加规则
    public function ajaxSaveRule(){
        $name = I('name');
        $id = I('id');


        if($name){
            //查找是否有存在的规则
            $ruleT = D('AdminAuthRule')->where(array('name' => $name))->find();


            $rule = array(
                'name'=>$name,
                'mid'=>I('mid'),
                'title'=>I('title'),
                'status'=>I('status',1)
                );

            //如果是保存
            if($id){
                if($ruleT['id'] != $id){
                    ajaxMsg(1,'规则标识已存在，请检查规则列表');
                }
                D('AdminAuthRule')->where(array('id' => $id))->save($rule);

            }else{
                if($ruleT){
                    ajaxMsg(1,'规则标识已存在，请检查规则列表');
                }

                $rule['createtime']=time();//设置创建时间
                D('AdminAuthRule')->add($rule);
            }
            ajaxMsg(0,'保存成功');
        }else{
            ajaxMsg(1, "请填写规则标识");
        }

    }
    public function  ajaxSaveRuleStatus(){
        $id = I('id');
        $rule = D('AdminAuthRule')->find($id);
        if($rule){
            $status = $rule['status'];
            if($status == 1){
                $rule['status'] = 0;
            }else{
                $rule['status'] = 1;
            }
            D('AdminAuthRule')->where(array('id'=>$id))->save($rule);
            ajaxMsg(0,'保存成功');
        }else{
            ajaxMsg(1,'该权限已经删除了');
        }

    }

    //添加规则
    public function ajaxDecRule(){
        $id = I('id');

        D('AdminAuthRule')->where(array('id'=>$id))->delete();
        ajaxMsg(0, "已删除");

    }

 /**** 规则 end*****/



    /**** 角色 start*****/
    public function groupList(){
        $this->set_sidebar_sub('groupList');
        $keyword = I('keyword');
        if($keyword){
            $page = D('AdminAuthGroupView')->findPage("concat(title) like '%$keyword%'",15,'id desc');
            $this->assign('keyword', $keyword);
        }else{
            $page = D('AdminAuthGroupView')->findPage('',15,'id desc');
        }

        $this->assign('page', $page);
        //读取模块信息
        $this->display();
    }
    //添加规则页面
    public function groupAdd()
    {
        $this->set_sidebar_sub('groupList');
        $id = I('get.id');
        if($id){//如果是编辑
            $detail=D('AdminAuthGroup')->find($id);
            $ruleids = explode(",",$detail['rules']);
            $ruleMap = array();
            foreach($ruleids as $rule){
                $ruleMap[$rule.''] = 1;
            }
            $this->assign('rule_map', $ruleMap);
            $this->assign('detail', $detail);
        }

        $this->assign('p', I('get.p',1));
        $rules= D('AdminAuthRuleView')->order('AdminAuthRule.mid asc')->select();
        $this->assign('rules', $rules);
        $this->display();
    }
    //添加规则
    public function ajaxSaveGroup(){
        $title = I('title');
        $id = I('id');

        if($title){
            //查找是否有存在的规则

            $rule = array(
                'describe'=>I('describe'),
                'rules'=>implode(",",I('rules')),
                'title'=>$title,
                'status'=>I('status',1),
                'type'=>I('type',1)
            );

            //如果是保存
            if($id){
//                ajaxMsg(1, to_json_string($rule));
                D('AdminAuthGroup')->where(array('id' => $id))->save($rule);
            }else{
//                $rule['createtime']=time();//设置创建时间
                D('AdminAuthGroup')->add($rule);
            }
            ajaxMsg(0,'保存成功');
        }else{
            ajaxMsg(1, "请填写角色名称");
        }

    }

    public function changePass(){
        $this->set_sidebar_sub('changePass');
        $this->display();
    }

    //添加规则
    public function ajaxChangePass(){
        $old_password = I('old_password');
        $password = I('password');
        $re_password = I('re_password');
        if(!$old_password){
            ajaxMsg(1,"请先填写原密码");
        }

        if(!$password){
            ajaxMsg(1,"请先填写新密码");
        }
        if($re_password != $password){
            ajaxMsg(1,"确认密码不一致");
        }



        $user = D('User')->where(array('uid'=>admin_uid()))->find();
        $old_password = md5($old_password);
        if($old_password != $user['password']){
            ajaxMsg(1,"原密码错误");
        }
        $size = strlen($re_password);
        if($size < 6 || $size >15){
            ajaxMsg(1,"请输入6~15位的密码");
        }

        D('User')->where(array('uid'=>admin_uid()))->save(array('password'=>md5($password),'update_time'=>time()));
        ajaxMsg(0,'success');

    }
    public function  ajaxSaveGroupStatus(){
        $id = I('id');
        $rule = D('AdminAuthGroup')->find($id);
        if($rule){
            $status = $rule['status'];
            if($status == 1){
                $rule['status'] = 0;
            }else{
                $rule['status'] = 1;
            }
            D('AdminAuthGroup')->where(array('id'=>$id))->save($rule);
            ajaxMsg(0,'保存成功');
        }else{
            ajaxMsg(1,'该权限已经删除了');
        }

    }

    //删除角色
    public function ajaxDecGroup(){
        $id = I('id');
        $count = D('AdminAuthGroupAccess')->where("group_id=$id")->count();
        if($count > 0 ){
            ajaxMsg(1, "还有用户在使用该角色，删除失败");
        }


        D('AdminAuthGroup')->where(array('id'=>$id))->delete();
        ajaxMsg(0, "已删除");

    }
    /**** 角色 end*****/


    /**** 管理员 start*****/
    public function adminList()
    {
        $this->set_sidebar_sub('adminList');
        $keyword = I('keyword');
        if ($keyword) {
            $page = D('AdminAuthGroupAccessView')->findPage("AdminAuthGroup.type = 0 and User.status>-1 and concat(User.realname,User.username) like '%$keyword%'", 15, 'User.uid desc');
            $this->assign('keyword', $keyword);
        } else {
            $page = D('AdminAuthGroupAccessView')->findPage("AdminAuthGroup.type = 0 and User.status>-1", 15, 'User.uid desc');
        }

        $this->assign('page', $page);
        //读取模块信息
        $this->display();
    }

    /**** 市局管理员 start*****/
    public function adminCity()
    {
        $this->set_sidebar_sub('adminCity');
        $keyword = I('keyword');
        if ($keyword) {
            $page = D('AdminAuthGroupAccessView')->findPage("AdminAuthGroup.id = 1 and User.status>-1 and concat(User.realname,User.username) like '%$keyword%'", 15, '');
            $this->assign('keyword', $keyword);
        } else {
            $page = D('AdminAuthGroupAccessView')->findPage("AdminAuthGroup.id = 1 and User.status>-1", 15, '');
        }

        $this->assign('page', $page);
        //读取模块信息
        $this->display();
    }

    public function  ajaxGetUsers()
    {
        $keyword = I('keyword', '');
        $list = D('UserOrganizationView')->where("User.status=1 and User.realname like '%$keyword%'")->group('User.uid')->order('User.realname asc')->limit(0, 50)->select();
        foreach ($list as $index => $item) {
            $uid = $item["uid"];
            $realname = $item['realname'];
            $item['html'] = "<a style='color: black;cursor: pointer;'><div class='item canselect' data-id='$uid' data-name='$realname'>$realname</div></a>";
            $list[$index] = $item;
        }

        ajaxMsg(0, 'success', $list);
    }

    public function ajaxAddCityAdmin(){
        $auth = session_auth();
        if ($auth != 1) {
            $auth = AdminUtil::auth();
        }
        if($auth == 1){
            $uid = I('uid');

            $user = D('User')->where(array("uid"=>$uid, 'status'=>array('gt', -1)))->find();
            if($user){
                $groupAccess = D('AdminAuthGroupAccess')->where(array("uid"=>$uid))->find();
                //
                if($groupAccess){
                    if($groupAccess['group_id'] == 3){
                        ajaxMsg(1,'添加失败,该成员是支部管理员，不能设为市局管理员');
                    }elseif($groupAccess['group_id'] == 1){
                        ajaxMsg(1,'添加失败,该成员是总管理员，不能设为市局管理员');
                    }
                    // elseif($groupAccess['group_id'] == 2){
                    //     ajaxMsg(1,'添加失败,该成员已经是市局管理员了，请选择其它人员');
                    // }
                    else{
                        $groupAccess['group_id'] = 1;
                        D('AdminAuthGroupAccess')->where(array("uid"=>$uid))->save($groupAccess);
                        ajaxMsg(0,'添加市局管理员成功');
                    }
                }else{
                    $groupAccess['uid'] = $uid;
                    $groupAccess['group_id'] = 1;
                    D('AdminAuthGroupAccess')->where(array("uid"=>$uid))->add($groupAccess);
                    ajaxMsg(0,'添加市局管理员成功');
                }
            }else{
                ajaxMsg(1,'用户不存在，添加失败');
            }
        }else{
            ajaxMsg(1,'权限不足，添加失败');
        }
    }

    public function ajaxDecCityAdmin(){
        $auth = session_auth();
        if($auth == 1){
            $uid = I('id');

            $groupAccess = D('AdminAuthGroupAccess')->where(array("uid"=>$uid, 'group_id'=>1))->find();
            if($groupAccess){
                D('AdminAuthGroupAccess')->where(array("uid"=>$uid, 'group_id'=>1))->save(array('group_id'=>4));
                ajaxMsg(0,'已删除');
            }else{
                ajaxMsg(1,'删除失败，该市局管理员不存在');
            }
        }else{
            ajaxMsg(1,'删除失败，权限不足');
        }
    }


    //添加规则页面
    public function adminAdd()
    {
        $this->set_sidebar_sub('adminList');
        $id = I('get.id');
        if($id){//如果是编辑
            $detail=D('AdminAuthGroupAccessView')->where("AdminAuthGroupAccess.uid=$id")->find();
            $this->assign('detail', $detail);
        }
        $this->assign('p', I('get.p',1));
        $groups= D('AdminAuthGroupView')->where("type=0 and status=1")->select();
        $this->assign('groups', $groups);

        $branchs = D('PartyBranch')->select();
        $hqs = D('PartyBranchHq')->select();
        $this->assign('branchs', $branchs);
        $this->assign('hqs', $hqs);

        $this->display();
    }

    //添加规则
    public function ajaxSaveAdmin(){
        $username = I('username');
        $id = I('id');
        $group_id = I('group_id');
        if(!$group_id){
            ajaxMsg(1, "请填选择管理员角色");
        }
        $hq_id = I('hq');
        $branch_id = I('branch');
        if($group_id == 4){//支部管理员
            if(!$branch_id){
                ajaxMsg(1,"请选择需要管理的支部");
            }
        }
        if($group_id == 8){//支部管理员
            if(!$hq_id){
                ajaxMsg(1,"请选择需要管理的总支");
            }
        }
        if($username){
            //查找是否有存在的规则

            $user = array(
                'username'=>$username,
                'realname'=>I('realname'),
                'update_time'=>time(),
                'status'=>I('status',1)
            );
            $password = I('password');
            if($password){
                $user['password'] = md5($password);
            }
            $user['admin_branch_id']=$branch_id?$branch_id:null;
            $user['admin_branch_hq_id']=$hq_id?$hq_id:null;

            //如果是保存
            if($id){
                D('AdminAuthGroupAccess')->where("uid=$id")->save(array('group_id'=>$group_id));
//                ajaxMsg(1, to_json_string($rule));
                D('User')->where(array('uid' => $id))->save($user);
            }else{

                $user['create_time']=time();//设置创建时间
                $uid = D('User')->add($user);
                D('AdminAuthGroupAccess')->add(array('uid'=>$uid, 'group_id'=>$group_id));
            }
            ajaxMsg(0,'保存成功');
        }else{
            ajaxMsg(1, "请填写帐号");
        }

    }
    public function  ajaxSaveAdminStatus(){
        $id = I('id');
        $user = D('User')->find($id);
        if($user){
            $status = $user['status'];
            if($status == 1){
                $user['status'] = 0;
            }else{
                $user['status'] = 1;
            }
            $user['update_time'] = time();
            D('User')->where(array('uid'=>$id))->save($user);
            ajaxMsg(0,'保存成功');
        }else{
            ajaxMsg(1,'该用户已被删除了');
        }

    }

    //删除角色
    public function ajaxDecAdmin(){
        $id = I('id');
        D('User')->where(array('uid'=>$id))->save(array('status'=>-1,'update_time'=>time()));
        ajaxMsg(0, "已删除");

    }
    /**** 管理员 end*****/


    //设置   1 、工委 2、党委  3、支部 4、党小组
    public function set_member()
    {
        $party_post_data = D('PartyPost')->select();
        $party_post['working_status_id'] = $party_post_data;
        $party_post['org_status_id'] = $party_post_data;
        $party_post['branch_status_id'] = $party_post_data;
        $party_post['group_status_id'] = $party_post_data;
        $set_members = D('SetMembers')->select();
        foreach ($set_members as $key=>$v){
            $status_id[$v['type']] = explode(",", $v['status_id']);
        }
        foreach ($party_post as $key=>$item){
            foreach ($item as $keys=>$value){
                if(in_array($value['status_id'], $status_id[$key])){
                    $party_post[$key][$keys]['choose'] =1;
                }
            }
        }
        $this->assign('party_post', $party_post);
        $this->display();
    }

    public function ajaxSetMember(){
        $data = I();
        if(count($data)<3){
            ajaxMsg(1, "成员设置不能为空");
        }
        foreach($data as $key=>$v){
            $postname = array();
            foreach ($v as $item){
                $postdata = D('PartyPost')->where(array('status_id'=>$item))->find();
                $postname[] = $postdata['name'];
            }
            $array = array(
                'type'=>$key,
                'status_id' => implode(',', $v),
                'name'=>implode(',', $postname),
                'edit_time'=> time()
            );
            $result = D('SetMembers')->where(array('type'=>$key))->save($array);
        }
            if($result){
                ajaxMsg(0, "修改成功");
            }
        }
}