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

class PartyMemberController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
    }


    public function index()
    {

        $this->assign('active',4);
        $this->check_wx_redirect('mob_party_member_index');//判断是否重定向登录
        $user = user();
        if($user['branch_id']){
            $this->assign('branch',D('PartyBranch')->where(array('id'=>$user['branch_id']))->find());
        }else{
            $this->assign('branch',D('PartyBranch')->where(array('is_main'=>1))->find());
        }

        $score = $user['score'];
        $sort = D('User')->where(array('score'=>array('gt',$score)))->count();//获取用户积分排名
        $tz_count = D('Taizhang')->where(array('uid'=>$user['uid'],'type'=>1,'status'=>array('neq',1),'status'=>array('gt',-1)))->count();
        $user['score_pm'] = $sort == 0? 1:$sort+1;
        $user['tz_count'] = $tz_count;

        $this->assign('taizhang1',
            D('TaizhangView')->where(array('type'=>1,'Taizhang.status'=>array('neq',1),'Taizhang.status'=>array('gt',-1)))->order(array('create_time'=>'desc','dz_count'=>'desc'))->limit(20)->select());

//        $this->assign('taizhang2',
//            D('TaizhangView')->where(array('type'=>1,'Taizhang.uid'=>$user['uid'],'type2'=>0,'Taizhang.status'=>array('gt',-1)))->order(array('dz_count'=>'desc','create_time'=>'desc'))->limit(3)->select());



        $this->assign('user',$user);
        $this->assign('users',D('UserView')->where(array('status'=>1,'uid'=>array('neq',1)))->order('User.sort desc')->limit(20)->select());
        $this->assign('tags', D('TaizhangTags')->order('title asc')->select());
        $this->assign('norms',D('TaizhangNorms')->where(array('type'=>1))->order('id asc')->select());
        $this->setWebTitle("党员先锋");
        $this->display();
    }

    public function zone()
    {
        $this->check_wx_redirect('mob_party_member_zone');//判断是否重定向登录
        $uid = I('uid');
        $user = D('UserView')->where(array('User.uid'=>$uid))->find();
        $score = $user['score'];
        $sort = D('User')->where(array('score'=>array('gt',$score)))->count();//获取用户积分排名
        $tz_count = D('Taizhang')->where(array('uid'=>$user['uid'],'type'=>1,'status'=>array('neq',1),'status'=>array('gt',-1)))->count();
        $user['score_pm'] = $sort == 0? 1:$sort+1;
        $user['tz_count'] = $tz_count;
        $friend = D('UserFriends')->where(array('uid'=>uid(),'friend_uid'=>$uid))->find();
        if($friend['status'] == 1){
            $user['is_follow'] = 1;
        }

        $this->assign('taizhang',
            D('TaizhangView')->where(array('type'=>1,'Taizhang.uid'=>$uid,'Taizhang.status'=>array('neq',1),'Taizhang.status'=>array('gt',-1)))->order('Taizhang.create_time desc')->select());
        $this->assign('norms',
            D('TaizhangNorms')->where(array('type'=>1))->order('id asc')->select());

        $this->setWebTitle("党员先锋");

        $this->assign('detail',$user);
        $this->display();
    }

    public function users()
    {
        $this->check_wx_redirect('mob_party_member_index');//判断是否重定向登录
        $sql = "select
              (select count(*) from user_friends uf where uf.uid=u.uid and uf.status=1) as uf_count,
              (select count(*) from taizhang tz where tz.uid=u.uid and tz.status=0) as tz_count,u.*
                        from user u where u.status>-1 order by u.sort DESC";
        $result = D()->query($sql);
        $this->assign('list', $result);
        $this->display();
    }

    /**
     * calvin 2019-3-11
     * 删除台账
     */
    public function ajaxDel(){
        $id = I('post.id');
        if ($id) {
            $_taizhang = M('Taizhang');
            $bool = $_taizhang->where(array('id'=>$id))->setField('status', -1);
            if (false !== $bool) ajaxMsg(0, 'success');
        }
        ajaxMsg(1, '操作失败！请稍后再试..');
    }

}
