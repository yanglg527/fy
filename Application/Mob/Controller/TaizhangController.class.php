<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */

namespace Mob\Controller;

use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;
use Common\Util\ImageUploadUtil;

class TaizhangController extends BaseAuthController
{

    function _initialize()
    {
        parent::_initialize();
    }
    public function tz_list_tag()
    {
        $this->check_wx_redirect('mob_party_member_index');//判断是否重定向登录
        $id = I('tag_id');
        $detail = D('TaizhangTags')->find($id);
        $this->assign('detail', $detail);
        $this->setWebTitle($detail['title']);
        $this->display();
    }
    public function tz_list_dz()
    {
        $this->check_wx_redirect('mob_party_member_index');//判断是否重定向登录
        $type = I('type');
        $search['Taizhang.type'] = $type;
        $this->assign('list', D('TaizhangView')->where($search)->order("Taizhang.dz_count desc")->limit(4)->select());
        $this->display();
    }

    public function user_dz()
    {
        $this->check_wx_redirect('mob_party_member_index');//判断是否重定向登录
        $this->assign('list', D('TaizhangDzView')->where(array('TaizhangDz.uid'=>uid()))->order("TaizhangDz.create_time desc")->select());
        $this->display();
    }

    public function user_correct()
    {
        $this->setWebTitle("我的收藏");
        $this->check_wx_redirect('mob_party_member_index');//判断是否重定向登录
        $this->assign('list', D('TaizhangCorrectView')->where(array('TaizhangCorrect.uid'=>uid()))->order("TaizhangCorrect.create_time desc")->select());
        $this->display();
    }


    public function tz_listf_norm()
    {
        $this->check_wx_redirect('mob_party_member_index');//判断是否重定向登录
        $this->assign('tags', D('TaizhangTags')->order('title asc')->select());
        $type = I('type');
        $id = I('id');
        $norm_id = I('norm_id');
        if ($type == 1) {
            $detail = D('UserView')->where(array('User.uid'=>$id))->find();
            $detail['id'] = $detail['uid'];
            $detail['type'] = 1;
        } elseif ($type == 2) {
            $detail = D('PartyOrganization')->where(array('id'=>$id))->find();
            $detail['type'] = 2;
        } elseif ($type == 3) {
            $detail = D('UserView')->where(array('User.uid'=>$id))->find();
            $detail['id'] = $detail['uid'];
            $detail['type'] = 3;
        } elseif ($type == 4) {
            $detail = D('PartyBranch')->where(array('id'=>$id))->find();
            $detail['type'] = 4;
        }
        $norm = D('TaizhangNorms')->find($norm_id);
        $this->assign('norm',$norm);
        $this->setWebTitle("指标台帐");
        $detail['norm_id'] = $norm_id;
        $this->assign('detail', $detail);
        $this->display();
    }

    //quanbu
    public function tz_listf_dangzu()
    {
        $this->check_wx_redirect('mob_party_member_index');//判断是否重定向登录
        $this->assign('dangzus', D('PartyOrganization')->order('name asc')->select());
        $this->assign('tags', D('TaizhangTags')->order('title asc')->select());
        $id = I('id');
        $type2 = I('type2');
        $norm_id = I('norm_id');
        if ($id) {
            $detail = D('PartyOrganization')->find($id);
        }
        if($norm_id > 0){
            $detail['norm_id'] = $norm_id;
        }

        if($type2 > 0){
            $type2 = $type2==2?1:0;
            $detail['type2'] = $type2;
            $this->assign('detail', $detail);
        }
        $detail['type'] = 2;
        $this->assign('norms',
            D('TaizhangNorms')->where(array('type'=>2))->order('id asc')->select());
        $this->assign('detail', $detail);
        $this->setWebTitle("台帐搜索");

        $this->display();
    }
    public function tz_listf_branch()
    {
        $this->check_wx_redirect('mob_party_member_index');//判断是否重定向登录
        $id = I('id');
        $type2 = I('type2');
        $norm_id = I('norm_id');
        if ($id) {
            $detail = D('PartyBranch')->find($id);
        }
        if($norm_id > 0){
            $detail['norm_id'] = $norm_id;
        }

        if($type2 > 0){
            $type2 = $type2==2?1:0;
            $detail['type2'] = $type2;
            $this->assign('detail', $detail);
        }
        $this->assign('branchs', D('PartyBranch')->order('name asc')->select());
        $this->assign('tags', D('TaizhangTags')->order('title asc')->select());
        $detail['type'] = 4;
        $this->assign('norms',
            D('TaizhangNorms')->where(array('type'=>4))->order('id asc')->select());
        $this->assign('detail', $detail);
        $this->setWebTitle("台帐搜索");

        $this->display();
    }
    public function tz_listf_leader()
    {
        $this->check_wx_redirect('mob_party_member_index');//判断是否重定向登录
        $this->assign('tags', D('TaizhangTags')->order('title asc')->select());
        $id = I('id');
        $type2 = I('type2');
        $norm_id = I('norm_id');
        if ($id > 0 && $id != '') {
            $detail = D('UserView')->where(array('User.uid'=>$id))->find();
        }
        if($norm_id > 0){
            $detail['norm_id'] = $norm_id;
        }

        if($type2 > 0){
            $type2 = $type2==2?1:0;
            $detail['type2'] = $type2;
            $this->assign('detail', $detail);
        }
		$detail['type'] = 3;
        $this->assign('norms',
            D('TaizhangNorms')->where(array('type'=>3))->order('id asc')->select());
        $this->assign('detail', $detail);
        $this->setWebTitle("台帐搜索");

        $this->display();
    }

    public function tz_listf_pmember()
    {
        $this->check_wx_redirect('mob_party_member_index');//判断是否重定向登录
        $this->assign('tags', D('TaizhangTags')->order('title asc')->select());
        $id = I('id');
        $type2 = I('type2');
        $norm_id = I('norm_id');
        if ($id) {
            $detail = D('UserView')->where(array('User.uid'=>$id))->find();
        }
        if($norm_id > 0){
            $detail['norm_id'] = $norm_id;
        }

        if($type2 > 0){
            $type2 = $type2==2?1:0;
            $detail['type2'] = $type2;
            $this->assign('detail', $detail);
        }
		$detail['type'] = 1;
        $this->assign('norms',
            D('TaizhangNorms')->where(array('type'=>1))->order('id asc')->select());
        $this->assign('detail', $detail);
        $this->setWebTitle("台帐搜索");

        $this->display();
    }


    public function tz_comment(){
        $id = I('id');
        $detail = D('TaizhangView')->where(array('Taizhang.id'=>$id))->find();
//           echo '<pre>';
//     echo print_r($detail);
        $tag_name =  D('TaizhangTags')->where(array('id'=>$detail['tag_id']))->field('title')->find();
        $detail['tag_name'] = $tag_name['title'];
        $detail['pl_count'] = D('TaizhangComment')->where(array('taizhang_id'=>$detail['id'],'status'=>1))->count();
        $this->assign('detail', $detail);
        $this->assign('comments',D('TaizhangCommentView')->where(array('taizhang_id'=>$id,'TaizhangComment.status'=>1))->order('TaizhangComment.create_time asc')->select());
        $this->setWebTitle("台帐评论");

        $this->display();
    }

    public function ajax_comment(){
        $uid = uid();
        $id = I('id');
        $taizhang = D('Taizhang')->where(array('id'=>$id,'status'=>2))->find();
        if ($taizhang) {
            D('TaizhangComment')->add(array('taizhang_id'=>$id,'uid'=>$uid,'create_time'=>time(),'content'=>I('content'),'taizhang_uid'=>$taizhang['uid']));
            D('Taizhang')->where(array('id'=>$id))->setInc('pl_count');
            ajaxMsg(0,'success');
        }else{
            ajaxMsg(1,'该台账不存在或已被删除');
        }

    }
    public function ajax_correct(){
        $uid = uid();
        $id = I('id');
        $correct = D('TaizhangCorrect')->where(array('taizhang_id'=>$id,'uid'=>$uid))->find();
        if($correct){//取消收藏
            D('TaizhangCorrect')->where(array('taizhang_id'=>$id,'uid'=>$uid))->delete();
            D('Taizhang')->where(array('id'=>$id))->setDec('sc_count');
            $detail = D('Taizhang')->find($id);
            ajaxMsg(0,'已取消收藏',array('is_correct'=>0,'detail'=>$detail));
        }else{
            D('TaizhangCorrect')->add(array('taizhang_id'=>$id,'uid'=>$uid,'create_time'=>time()));
            D('Taizhang')->where(array('id'=>$id))->setInc('sc_count');
            $detail = D('Taizhang')->find($id);
            ajaxMsg(0,'收藏成功',array('is_correct'=>1,'detail'=>$detail));
        }
    }

    public function ajax_dz(){
        $uid = uid();
        $id = I('id');
        $correct = D('TaizhangDz')->where(array('taizhang_id'=>$id,'uid'=>$uid))->find();
        if($correct){//
            D('TaizhangDz')->where(array('taizhang_id'=>$id,'uid'=>$uid))->delete();
            D('Taizhang')->where(array('id'=>$id))->setDec('dz_count');
            $detail = D('Taizhang')->find($id);
            ajaxMsg(0,'已取消点赞',array('is_dz'=>0,'detail'=>$detail));
        }else{
            $taizhang = D('Taizhang')->where(array('id'=>$id,'status'=>0))->find();
            if (condition) {
                D('TaizhangDz')->add(array('taizhang_id'=>$id,'uid'=>$uid,'create_time'=>time(),'taizhang_uid'=>$taizhang['uid']));
                D('Taizhang')->where(array('id'=>$id))->setInc('dz_count');
                $detail = D('Taizhang')->find($id);
                ajaxMsg(0,'点赞成功',array('is_dz'=>1,'detail'=>$detail));
            }else{
                ajaxMsg(1,'台账不存在或已被删除');
            }

        }
    }
    function ajaxUploadCover()
    {
        ajaxMsg(0, "success", ImageUploadUtil::upload("taizhang/",'file',array('width'=>480,'height'=>360),array('width'=>180,'height'=>120)));
    }

    public function ajax_loading_tz()
    {
        $type = I('type');
		 $id = I('id');
        // if($type == 1){//个人
        //     if ($id) {
        //         $search['Taizhang.uid'] = $id;
        //     }
        // }elseif($type == 2){//党组
        //     if ($id) {
        //         $search['Taizhang.organization_id'] = $id;
        //     }
        // }
        // elseif($type == 4){//支部
        //     if ($id) {
        //         $search['Taizhang.branch_id'] = $id;
        //     }
        // }
        // if($type){
        // 	$search['Taizhang.type'] = $type;
        // }

        // $type2 = I('type2');//创新之星什么的
        // if ($type2 != null && $type2!= '' && $type2 >-1 ) {
        //     $search['Taizhang.type2'] = $type2;
        // }
        $keyword = I('keyword');
        $tag_id = I('tag_id');//类型
        $sort = I('sort', 'create_time desc');
//        $order_time = I('order_time');
		$lastItem=I('lastItem',0);
        if ($tag_id) {//标签
            $search['Taizhang.tag_id'] = array('in', ''.$tag_id);
        }
        if ($keyword) {
            $search['Taizhang.title'] = array('like', "%$keyword%");
        }

		$date = I('date');

		if($date){
			$search['Taizhang.publish_time'] = $date;
		}

        $search['Taizhang.status'] = array('neq',1);
        // array('gt',-1);

//		->limit($lastItem,20)
        $taizhang = D('TaizhangView')->where($search)->where(array('Taizhang.status' => array('gt', -1)))->order($sort)->select();
         foreach($taizhang as $key=>$v){
             $taizhang[$key]['pl_count'] = D('TaizhangComment')->where(array('taizhang_id'=>$v['id'],'status'=>1))->count();
             $dz = D('TaizhangDz')->where(array('taizhang_id'=>$v['id'],'uid'=>$v['uid']))->find();
             $taizhang[$key]['is_dz'] = $dz?true:false;
         }
        ajaxMsg(0, ($lastItem + 20), $taizhang);
    }


    public function ajax_loading_member_tz()
    {
        // $type = I('type');
         $id = I('id');

        $search['Taizhang.uid'] = $id;
        $type2 = I('type2');//创新之星什么的
        if ($type2 != null && $type2!= '' && $type2 >-1 ) {
            $search['Taizhang.type2'] = $type2;
        }
        $keyword = I('keyword');
        $tag_id = I('tag_id');//类型
        $sort = I('sort', 'create_time desc');
        $lastItem=I('lastItem',0);
        if ($tag_id) {//标签
            $search['Taizhang.tag_id'] = array('in', ''.$tag_id);
        }
        if ($keyword) {
            $search['Taizhang.title'] = array('like', "%$keyword%");
        }
        $date = I('date');

        if($date){
            $search['Taizhang.publish_time'] = $date;
        }

        $search['Taizhang.status'] = array('gt',1);
//      ->limit($lastItem,20)
        ajaxMsg(0, ($lastItem + 20), D('TaizhangView')->where($search)->order($sort)->select());
    }




    public function add(){
        $this->check_wx_redirect('mob_index');//判断是否重定向登录
        $tag_id=I('get.tag_id');
        $norm_id=I('get.group_id');
        $type=I('get.type');
        $spec_id=I('get.spec_id');
        $temp_id=I('get.temp_id');
        $notice_id=I('get.notice_id');

        if ($notice_id) {
            $notice=D('NoticeUserStatusView')->where(array('id'=>$notice_id,'status'=>1))->find();

            if ($notice) {
                $type=$notice['notice_taizhang_type'];
            }else{
                redirect(__ROOT__."/error3.html");
            }
        }
        $tag_id=$tag_id?$tag_id:1;
        $norm_id=$norm_id?$norm_id:1;
        $type=$type?$type:1;
        $spec_id=$spec_id?$spec_id:1;
        $temp_id=$temp_id?$temp_id:1;
        $notice_id=$notice_id?$notice_id:-1;
        $this->assign('tag_id', $tag_id);//标签id
        $this->assign('norm_id', $norm_id);//指标id
        $this->assign('type', $type);//type是台账类型
        $this->assign('spec_id', $spec_id);//动作类型
        $this->assign('temp_id', $temp_id);//选择模板id
        $this->assign('notice_id', $notice_id);
        $this->assign('now', time());

        if ($temp_id == 2) {
            $this->display('add2');
        }elseif ($temp_id == 3) {
            $this->display('add3');
        }else{
            $this->display('add1');
        }

    }

    public function add1(){
        $this->check_wx_redirect('mob_index');//判断是否重定向登录
        $tag_id=I('get.tag_id');
        $norm_id=I('get.norm_id');
        $type=I('get.type');
        $spec_id=I('get.spec_id');
        $notice_id=I('get.notice_id');
        $tag_id=$tag_id?$tag_id:1;
        $norm_id=$norm_id?$norm_id:1;
        $type=$type?$type:1;
        $spec_id=$spec_id?$spec_id:1;
        $notice_id=$notice_id?$notice_id:-1;
        $this->assign('tag_id', $tag_id);
        $this->assign('norm_id', $norm_id);
        $this->assign('type', $type);
        $this->assign('spec_id', $spec_id);
        $this->assign('notice_id', $notice_id);
        $this->display();
    }

    public function add2(){
        $this->check_wx_redirect('mob_index');//判断是否重定向登录
        $tag_id=I('get.tag_id');
        $norm_id=I('get.norm_id');
        $type=I('get.type');
        $spec_id=I('get.spec_id');
        $notice_id=I('get.notice_id');
        $tag_id=$tag_id?$tag_id:1;
        $norm_id=$norm_id?$norm_id:1;
        $type=$type?$type:1;
        $spec_id=$spec_id?$spec_id:1;
        $notice_id=$notice_id?$notice_id:-1;
        $this->assign('tag_id', $tag_id);
        $this->assign('norm_id', $norm_id);
        $this->assign('type', $type);
        $this->assign('spec_id', $spec_id);
        $this->assign('notice_id', $notice_id);
        $this->display();
    }

    public function ajaxadd(){
        $this->check_wx_redirect('mob_index');//判断是否重定向登录
        $user = user();
        $uid = $user['uid'];
        $tz = I("post.tz");
        $contents = I("post.contents");
        $image_path = null;
        foreach($contents as $content){
           if ($content["image"] != null && $content["image"] != '') {
               $image_path = $content["image"];
               break;
           }
        }
        $organization_id=$user['organization_id'];//党组id
        $branch_id=$user['branch_id'];

        //这里是录入待办事项的台账
        if ($tz['notice_id']>0) {
            $notice=D('NoticeUserStatusView')->where(array('id'=>$tz['notice_id'],'status'=>1))->find();
            if ($notice) {
                if ($notice['is_main'] == 1) {
                    $user = D('UserView')->where(array('uid'=>$notice['uid']))->find();
                    $organization_id=$user['organization_id'];
                    $branch_id=$user['branch_id'];
                }else{
                    if ($notice['type_id']==1) {
                        $party_organization_sj=D('PartyOrganizationSj')->where(array('organization_id'=>$notice['organization_id']))->find();
                        if ($party_organization_sj) {
                            $user = D('UserView')->where(array('uid'=>$party_organization_sj['uid']))->find();
                            $notice=D('NoticeUserStatusView')->where(array('notice_id'=>$notice['notice_id'],'uid'=>$party_organization_sj['uid'],'is_main'=>1,'status'=>1))->find();
                            if ($notice) {
                                if (!$user) {
                                $notice=D('NoticeUserStatusView')->where(array('notice_id'=>$notice['notice_id'],'add_taizhang_uid'=>$notice['add_taizhang_uid'],'is_main'=>1,'status'=>1))->find();
                                $user = D('UserView')->where(array('uid'=>$notice['uid']))->find();
                                }
                                $organization_id=$user['organization_id'];
                                $branch_id=$user['branch_id'];
                            }else{
                                ajaxMsg(1,'您的支部还未设置书记或班子主要负责人。');
                            }

                        }
                    }else{
                        $notice=D('NoticeUserStatusView')->where(array('notice_id'=>$notice['notice_id'],'add_taizhang_uid'=>$notice['add_taizhang_uid'],'is_main'=>1,'status'=>1))->find();
                        if($notice){
                           $user = D('UserView')->where(array('uid'=>$notice['uid']))->find();
                           $organization_id=$user['organization_id'];
                           $branch_id=$user['branch_id'];
                        }
                    }

                }


            }else{
                 ajaxMsg(1,'代办事项不存在');
            }
        }
        //新增台账全过程
        $tz_id = D("Taizhang")->add(array("uid"=>$user["uid"],"title"=>$tz["title"],"publish_name"=>$tz["name"],"publish_time"
        =>$tz["time"],"type"=>$tz["type"],"type2"=>$tz["spec_id"],"template_id"=>$tz["temp_id"],"address"=>$tz["address"],
            "type2"=>0,"tag_id"=>$tz["tag_id"],"norm_id"=>$tz["norm_id"],"party_name"=>$tz["party"],"cover"=>$image_path,
            "organization_id"=>$organization_id,"branch_id"=>$branch_id,"add_uid"=>$uid,"create_time"=>time()));
        //台账内容表->里面的图片会有统一一个taizhang_id
        foreach($contents as $content){
            D('TaizhangContents')->add(array("type"=>$content["type"],"title"=>$content["title"],"image"=>$content["image"],
                "content"=>$content["content"],"taizhang_id"=>$tz_id));
        }
        if ($notice) {
            D('NoticeUserStatus')->where(array('notice_id'=>$notice['notice_id'],'add_taizhang_uid'=>
                $notice['add_taizhang_uid']))->save(array('is_add_taizhang'=>1,'finish_time'=>time(),'taizhang_id'=>$tz_id));
        }
        if ($uid) {
            update_user_score($uid, 5, 1,'上传台账');
        }
        if ($notice['type_id']==1 && $tz['notice_id']>0) {
            $organization_users = D('User')->where(array('organization_id'=>$organization_id))->select();
            foreach ($organization_users as $key => $vo) {
                update_user_score($vo['uid'], 5, 1,'上传台账');
            }
        }

        ajaxMsg(0, $tz_id);
    }

      public function test(){
        D('NoticeUserStatus')->where(array('notice_id'=>144,'add_taizhang_uid'=>3789))->save(array('is_add_taizhang'=>1,'finish_time'=>time(),'taizhang_id'=>1));
      }



    public function show(){
        $id=I('get.id');
        $id = $id?$id:I('state');
        $this->check_wx_redirect('taizhang_show',$id);//判断是否重定向登录
        $user = user();
        // $t_id=I("get.t_id");
        $taizhang= D('Taizhang')->where(array('id'=>$id,'status'=>array('gt',-1)))->find();
	$taizhang['tz_content']= htmlspecialchars_decode($taizhang['tz_content']);
        if ($user && $taizhang) {
            $score = 1;
            //记录点击数
            $click_log=D('TaizhangClickLog')->where(array('uid'=>$user['uid'],'taizhang_id'=>$taizhang['id']))->find();
            if (!$click_log) {

                D('TaizhangClickLog')->add(array('uid'=>$user['uid'],'taizhang_id'=>$taizhang['id'],'score'=>$score,'create_time'=>time()));
                $check_read = update_at_read_log($user['uid'],1,$taizhang['id'],$score);
                if($check_read){
                    update_user_score($user["uid"], $score, 1,'阅读台账得分');
                }
            }

        }

        if($taizhang['template_id']==3){
            //评论
            $detail = D('TaizhangView')->where(array('Taizhang.id'=>$id))->find();
            
            $this->assign('detail', $detail);
            $this->assign('comments',D('TaizhangCommentView')->where(array('taizhang_id'=>$id,'status'=>1))->order('TaizhangComment.create_time asc')->select());
            $this->setWebTitle("台帐评论");



           $imgArr = D('TaizhangContents')->where(array('taizhang_id'=>$taizhang['id']))->select();
            $count = count($imgArr);
            $taizhang['imgcount'] = $count;
            if($count>1){
                //这里拿缩略图
                foreach($imgArr as $k=> $v){
                   $imgArr[$k]['thumb_image']= $v['image'];
                }
                $taizhang['imgArr'] = $imgArr;
            }
           else if($count==1){

               $taizhang['imgArr'] = $imgArr[0];
               $taizhang['imgArr']['thumb_image'] = $imgArr[0]['image'];
           }
            else{
                $taizhang['imgArr']='';
            }
            $tzuser = M('user')->where(array('uid'=>$taizhang['uid']))->find();
            $taizhang['headimgurl'] = $tzuser['headimgurl'];

        }

        // echo to_json_string($taizhang);
        $this->assign('item', $taizhang);
        if ($taizhang['template_id'] == 2) {
            $this->display('show2');
        }else if($taizhang['template_id'] == 3){
//            var_dump($taizhang);
            $this->display('show3');
        }
        else{
            $this->display('show1');
        }
    }

    public function articles(){
        //获取支部成员信息
        $branch = D('PartyBranch')->find(I('branch_id'));
        $branch_sj = D('PartyBranchMembers')->where(array('branch_id'=>$branch['id'],'status'=>1))->find();//书记
        $branch_fsj = D('PartyBranchMembers')->where(array('branch_id'=>$branch['id'],'status'=>2))->find();//副书记
        $branch_member['sj'] = $branch_sj['realname'];
        $branch_member['fsj'] = $branch_fsj['realname'];
        $sj = D('PartyBranchMembers')->where(array('status'=>'1','branch_id'=>$branch['id']))->find();
        $wy = D('PartyBranchMembers')->where(array('branch_id'=>$branch['id']))->field('realname')->select();
        $newwy = array();
        foreach($wy as $key=>$item){
            if($item['realname'] == $sj['realname']){  //如果这个委员  还是书记  把他放前面
                array_unshift($newwy,$item['realname']);
            }else{
                $newwy[$key] = $item['realname'];
            }
        }
        if($wy){
            $realname_list = array_unique($newwy);//去除名字相同的元素，只留一个（存在场景：一个人同时担任多个职务）
        }else{
            $realname_list = null;//如果成员表没有数据  则为空
        }
        $tag_id=I('get.id');
        $tag_id=$tag_id?$tag_id:0;
        $tag = D('Tags')->where(array("id"=>$tag_id))->find();
        $this->assign('branch',$branch);
        $this->assign('realname_list',$realname_list);
        $this->assign('branch_member',$branch_member);
        $this->assign("head_title", $tag['name']);
        $this->assign("tag_id", $tag_id);
        $this->display();
    }


    public function organization_contrast() {
            $sql = "select PO.id, PO.name,PO.cover,
              (select sum(u1.score) from user u1 where u1.status=1 and u1.organization_id=PO.id) score
               from  party_organization PO where PO.id != 21 order by score desc,sort desc ";
        $list = D() -> query($sql);
        $total =  D('User')->where(array('organization_id'=>1,'status'=>1))->sum('score');
        $total = $total?$total:0;
        // $value;
        // foreach ($list as $key => $vo) {
        //     $value['name'][$key]=$vo['name'].' ';
        //     if ($vo['score'] == null || $vo['score'] =='') {
        //         $value['score'][$key]=0;
        //     }else{
        //         $value['score'][$key]=$vo['score'];
        //     }

        // }
        // echo to_json_string($value);
        $this->assign("total", $total);
        $this->assign("list", $list);
        $this->display();
    }

    /**
 * 个人积分排名
 */
    public function person_contrast(){
        $sql = "select uid, headimgurl,realname,score  from user where status=1 and uid != 1 order by score desc,realname desc ";

        $list = D()-> query($sql);
        $this->assign("list", $list);
        $this->display();
    }
    /**
     * 党委领导积分排名
     */
    public function leader_contrast(){
//        $sql = "select uid, headimgurl,realname,score  from user where status=1 and uid != 1 order by score desc,realname desc ";
//
//        $list = D()-> query($sql);
        $dwdata =  D('PartyOrganizationMembers')->select();//获取党委领导成员
        //按积分排名
        $list = score_order($dwdata);
        $this->assign("list", $list);
        $this->display();
    }

    /**
     * 支部积分排名
     */
    public function branch_contrast(){

//        $sql = "select Branch.id,Branch.name,Branch.cover,
//              (select sum(u1.score) from user u1 where u1.status=1 and u1.branch_id=Branch.id) score
//              from party_branch Branch where Branch.id != 318 order by score desc,name desc ";
//
//        $list = D() -> query($sql);
        $list = $this->get_party_branch_sort();
//        //在小组名后面加支部名称
//        foreach($list as &$item){
//            $branch = D('PartyBranch')->where(array('id'=>$item['branch_id']))->field('name')->find();
//            $item['branch_name'] = $branch['name'];
//        }
        $this->assign("list", $list);
        $this->display();
    }

     /**
     * 支部积分排名
     */
    public function smallgroup_contrast(){
//        $user = user();
//        $sql = "select sg.id,sg.name,sg.cover,
//              (select sum(u1.score) from user u1 where  u1.status=1 and u1.party_group_id=sg.id) score
//              from party_group sg where sg.branch_id = ".$user['branch_id']."  order by score desc,name desc ";
//
//        $list = D() -> query($sql);
        $list = $this->get_party_group_sort();
        //在小组名后面加支部名称
        foreach($list as &$item){
            $branch = D('PartyBranch')->where(array('id'=>$item['branch_id']))->field('name')->find();
            $item['branch_name'] = $branch['name'];
        }
        $this->assign("list", $list);
        $this->display();
    }
    /**
     * 获取该党小组的相关信息,按全党小组进行排序
     */
    function get_party_branch_sort(){
        //获取全部党小组信息
        $partyBranch = D('PartyBranch')->where('id != 318')->field('id,name,cover')->select();
        //获取党小组积分
        foreach ($partyBranch as &$item){
            $score_count = D('User')->where(array('branch_id'=>$item['id'],'status'=>1))->sum('score');//积分总数
            $member_count  = D('User')->where(array('branch_id'=>$item['id'],'status'=>1))->count();//人数
            $score = round($score_count/$member_count,2);
            if($score > 0){
                $item['score'] = $score;
            }else{
                $item['score'] = 0;
            }
        }
        //按积分排序
        foreach ($partyBranch as $key=>$v){
            $newArr[$key]['score'] = $v['score'];
        }
        array_multisort($newArr,SORT_DESC,$partyBranch);//SORT_DESC为降序，SORT_ASC为升序
        return $partyBranch;
    }

    /**
     * 获取该党小组的相关信息,按全党小组进行排序
     */
    function get_party_group_sort(){
        //获取全部党小组信息
        $partyGroup = D('PartyGroup')->field('id,name,cover,branch_id')->select();
        //获取党小组积分
        foreach ($partyGroup as &$item){
            $score_count = D('User')->where(array('party_group_id'=>$item['id'],'status'=>1))->sum('score');//积分总数
            $member_count  = D('User')->where(array('party_group_id'=>$item['id'],'status'=>1))->count();//人数
            $score = round($score_count/$member_count,2);
            if($score > 0){
                $item['score'] = $score;
            }else{
                $item['score'] = 0;
            }
        }
        //按积分排序
        foreach ($partyGroup as $key=>$v){
            $newArr[$key]['score'] = $v['score'];
        }
        array_multisort($newArr,SORT_DESC,$partyGroup);//SORT_DESC为降序，SORT_ASC为升序
        return $partyGroup;
    }

    /**
     * 党小组饼图
     */
    public function pei_smallGroup(){
		$id = I('id');//党小组id
        $small_group = M('party_group')->where(array('id'=>$id))->find();
		$total = D('UserScoreLogView')->where(array('User.party_group_id'=>$id))->sum('UserScoreLog.score');

		$count[0] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>1,'User.party_group_id'=>$id))->sum('UserScoreLog.score');//学习交流
		$count[1] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>2,'User.party_group_id'=>$id))->sum('UserScoreLog.score');//党员发展
		$count[2] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>3,'User.party_group_id'=>$id))->sum('UserScoreLog.score');//群团组织
		$count[3] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>4,'User.party_group_id'=>$id))->sum('UserScoreLog.score');//四个机制
		$count[4] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>5,'User.party_group_id'=>$id))->sum('UserScoreLog.score');//党员服务
		$count[5] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>6,'User.party_group_id'=>$id))->sum('UserScoreLog.score');//平台签到
		$count[6] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>7,'User.party_group_id'=>$id))->sum('UserScoreLog.score');//我的通知
		$count[7] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>8,'User.party_group_id'=>$id))->sum('UserScoreLog.score');//绩效
		$countPei = array();
		foreach($count as $index=>$c){
			$countPei[$index] = 0;
			$count[$index] = $count[$index]?$count[$index]:0;
			$countPei[$index] = $count[$index]<0?0:$count[$index];
		}
        $this->assign('small_group',$small_group);
        $this->assign('total',$total);
        $this->assign('count',$count);
        $this->assign('countPei',$countPei);
        $this->assign('types',D('UserScoreType')->order("id asc")->select());
     
       // var_dump(D('UserScoreType')->order("id asc")->select());

             //六个打通数据
		//$user = user();
		//$info = M('user_system_score')->where(array('user_name'=>$user['realname']))->find();
		$branch = M('user_system_score_branch')->where(array('branch_id'=>$id))->find();
		$sixScoreList['performance_score'] = $branch['performance_score'];
		$sixScoreList['work_innovation_score'] = $branch['work_innovation_score'];
		$sixScoreList['discipline_score'] = $branch['discipline_score'];
		$sixScoreList['risk_score'] = $branch['risk_score'];
		$sixScoreList['strength_score'] = $branch['strength_score'];
		$sixScoreList['learn_innovation_score'] = $branch['learn_innovation_score'];
		// $sixScoreList['personnel_score'] = $info['personnel_score'];
		// $sixScoreList['dedication_score'] = $info['dedication_score'];
		$sixScoreList['intelligent_score'] = $branch['intelligent_score'];
        $sixScoreList['score'] = I('dangzu_score');
        $this->assign('six_data',$sixScoreList);
		$this->display();
	}
	public function pei_dangzu(){
		$id = I('id');//dangzu
        $dangzu = M('party_organization')->where(array('id'=>$id))->find();
		$total = D('UserScoreLogView')->where(array('User.organization_id'=>$id))->sum('UserScoreLog.score');

		$count[0] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>1,'User.organization_id'=>$id))->sum('UserScoreLog.score');//学习交流
		$count[1] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>2,'User.organization_id'=>$id))->sum('UserScoreLog.score');//党员发展
		$count[2] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>3,'User.organization_id'=>$id))->sum('UserScoreLog.score');//群团组织
		$count[3] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>4,'User.organization_id'=>$id))->sum('UserScoreLog.score');//四个机制
		$count[4] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>5,'User.organization_id'=>$id))->sum('UserScoreLog.score');//党员服务
		$count[5] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>6,'User.organization_id'=>$id))->sum('UserScoreLog.score');//平台签到
		$count[6] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>7,'User.organization_id'=>$id))->sum('UserScoreLog.score');//我的通知
		$count[7] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>8,'User.organization_id'=>$id))->sum('UserScoreLog.score');//绩效
		$countPei = array();
		foreach($count as $index=>$c){
			$countPei[$index] = 0;
			$count[$index] = $count[$index]?$count[$index]:0;
			$countPei[$index] = $count[$index]<0?0:$count[$index];
        }
        // var_dump($dangzu);
        $this->assign('dangzu',$dangzu);
        $this->assign('total',$total);
        $this->assign('count',$count);
        $this->assign('countPei',$countPei);
        $this->assign('types',D('UserScoreType')->order("id asc")->select());


             //六个打通数据
		//$user = user();
		//$info = M('user_system_score')->where(array('user_name'=>$user['realname']))->find();
		$branch = M('user_system_score_branch')->where(array('branch_id'=>$id))->find();
		$sixScoreList['performance_score'] = $branch['performance_score'];
		$sixScoreList['work_innovation_score'] = $branch['work_innovation_score'];
		$sixScoreList['discipline_score'] = $branch['discipline_score'];
		$sixScoreList['risk_score'] = $branch['risk_score'];
		$sixScoreList['strength_score'] = $branch['strength_score'];
		$sixScoreList['learn_innovation_score'] = $branch['learn_innovation_score'];
		// $sixScoreList['personnel_score'] = $info['personnel_score'];
		// $sixScoreList['dedication_score'] = $info['dedication_score'];
		$sixScoreList['intelligent_score'] = $branch['intelligent_score'];
        $sixScoreList['score'] = I('dangzu_score');
        $this->assign('six_data',$sixScoreList);
		$this->display();
	}

	public function pei_branch(){
		$id = I('id');
        $branch = M('party_branch')->where(array('id'=>$id))->find();
		$total = D('UserScoreLogView')->where(array('User.branch_id'=>$id))->sum('UserScoreLog.score');

		$count[0] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>1,'User.branch_id'=>$id))->sum('UserScoreLog.score');//学习交流
	    $count[1] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>2,'User.branch_id'=>$id))->sum('UserScoreLog.score');//党员发展
		// $count[2] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>3,'User.branch_id'=>$id))->sum('UserScoreLog.score');//群团组织
		$count[3] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>4,'User.branch_id'=>$id))->sum('UserScoreLog.score');//四个机制
		$count[4] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>5,'User.branch_id'=>$id))->sum('UserScoreLog.score');//党员服务
		$count[5] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>6,'User.branch_id'=>$id))->sum('UserScoreLog.score');//平台签到
		// $count[6] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>7,'User.branch_id'=>$id))->sum('UserScoreLog.score');//我的通知
		// $count[7] = D('UserScoreLogView')->where(array('UserScoreLog.type'=>8,'User.branch_id'=>$id))->sum('UserScoreLog.score');//绩效
		$countPei = array();
		foreach($count as $index=>$c){
			$countPei[$index] = 0;
			$count[$index] = $count[$index]?$count[$index]:0;
			$countPei[$index] = $count[$index]<0?0:$count[$index];
		}
        $this->assign('branch',$branch);
		$this->assign('total',$total);
		$this->assign('count',$count);
		$this->assign('countPei',$countPei);
        $this->assign('types',D('UserScoreType')->order("id asc")->select());


        //六个打通数据
		//$user = user();
		//$info = M('user_system_score')->where(array('user_name'=>$user['realname']))->find();
		// $branch = M('user_system_score_branch')->where(array('branch_id'=>$id))->find();
		// $sixScoreList['performance_score'] = $branch['performance_score'];
		// $sixScoreList['work_innovation_score'] = $branch['work_innovation_score'];
		// $sixScoreList['discipline_score'] = $branch['discipline_score'];
		// $sixScoreList['risk_score'] = $branch['risk_score'];
		// $sixScoreList['strength_score'] = $branch['strength_score'];
		// $sixScoreList['learn_innovation_score'] = $branch['learn_innovation_score'];
		// // $sixScoreList['personnel_score'] = $info['personnel_score'];
		// // $sixScoreList['dedication_score'] = $info['dedication_score'];
		// $sixScoreList['intelligent_score'] = $branch['intelligent_score'];
        // $sixScoreList['score'] = I('branch_score');
        // $this->assign('six_data',$sixScoreList);
		$this->display();
	}

    public function track(){
        $this->check_wx_redirect('mob_index');//判断是否重定向登录
        $user = user();
        $uid = I('get.id',0);
        if ($user['uid'] == $uid) {

        }else{
            $user = D('User')->where(array('uid'=>$uid,'status'=>1))->find();
            $this->assign('is_other',1);
        }
        if ($user) {
            $this->assign('now',time());
//            $this->assign('score_sort',D('User')->where(array('status'=>1,'score'=>array('gt',$user['score'])))->count() + 1);
            $this->assign('score_sort', $_SESSION['leader_score_order']);//领导排名处已经存了
            $this->assign('user',$user);
            $this->assign('taizhang',D('Taizhang')->where(array('uid'=>$user['uid'],'status'=>0))->find());
            $this->assign('dz_sort',D('TaizhangDz')->where(array('taizhang_uid'=>$user['uid']))->count());
        }else{
            // $this->redirect(__ROOT__."/error3.html");
        }
        $this->display();
    }
    // public function getuser(){
    //     echo to_json_string(D('User')->where(array('uid'=>3733))->find());
    // }
        /**
         *
         */




}
