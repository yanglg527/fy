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

class LeaderController extends BaseAuthController
{

    function _initialize()
    {
        parent::_initialize();
    }


    //信箱列表
    public function index()
    {
        $this->check_wx_redirect('mob_party_member_index');//判断是否重定向登录
        $detail = user();

        if ($detail['is_leader'] == 1) {//如果是领导

        } else {
            $detail = D('UserView')->where(array('User.status' => 1, 'User.is_leader' => 1))->order('User.sort desc')->limit(1)->find();
        }
        $uid = $detail['uid'];
        if ($uid) {
            $total_standing_book = D('TaizhangView')->where(array('Taizhang.uid' => $uid,'status'=>0))->count();
            $detail['tz'] = $total_standing_book;

            //排名
            $count = D()->query("select count(*) as count  from user where status = 1 and is_leader = 1 and score>{$detail['score']}");
            $detail['pm'] = $count[0]['count'] ? $count[0]['count']+1 : 1;

            $this->assign('detail', $detail);

        }

        $this->assign('users', D('UserView')->where(array('User.status' => array('gt',-1), 'User.is_leader' => 1))->order('User.sort desc')->limit(20)->select());

        $this->assign('taizhang1',
            D('TaizhangView')->where(array('type'=>3,'Taizhang.status'=>array('gt',1)))->order(array('create_time'=>'desc','dz_count'=>'desc'))->limit(10)->select());
//        $this->assign('taizhang2',
//            D('TaizhangView')->where(array('type'=>3,'type2'=>0,'Taizhang.status'=>array('gt',-1)))->order(array('dz_count'=>'desc','create_time'=>'desc'))->limit(10)->select());
//
        $this->assign('tags', D('TaizhangTags')->order('title asc')->select());
        $this->assign('norms',D('TaizhangNorms')->where(array('type'=>3))->order('id asc')->select());
        $this->setWebTitle("领导表率");
        $this->display();
    }

    public function leaders()
    {
        $this->check_wx_redirect('mob_party_member_index');//判断是否重定向登录
        $sql = "select 
              (select count(*) from user_friends uf where uf.uid=u.uid and uf.status=1) as uf_count,
              (select count(*) from taizhang tz where tz.uid=u.uid and tz.status=0) as tz_count,u.*
                        from user u where u.status>-1 and u.is_leader=1 order by u.sort DESC";
        $this->setWebTitle("领导成员");
        $result = D()->query($sql);
        $this->assign('list', $result);
        $this->display();
    }

    public function zone()
    {
        $this->check_wx_redirect('mob_party_member_zone');//判断是否重定向登录
        $uid = I('uid');
        $detail = D('Common/UserView')->where(array('User.uid' => $uid))->find();
        $total_standing_book = D('TaizhangView')->where(array('Taizhang.uid' => $uid,'status'=>array('gt',1)))->count();
        $detail['tz'] = $total_standing_book;

        //排名
//        $count = D()->query("select count(*) as count  from user  where status = 1 and score>{$detail['score']}");
//        $detail['pm'] = $count[0]['count'] ? $count[0]['count']+1 : 1;
        $dwdata =  D('PartyOrganizationMembers')->select();//获取党委领导成员
        //按积分排名
        $scoreData = score_order($dwdata);
        //拿出此人的排名
        foreach ($scoreData as $key=>$item){
            if($item['uid'] == $uid){
                $detail['pm'] = $key+1;
                $_SESSION['leader_score_order'] = $detail['pm'];
            }
        }
        $friend = D('UserFriends')->where(array('uid'=>uid(),'friend_uid'=>$uid))->find();
        if($friend['status'] == 1){
            $detail['is_follow'] = 1;
        }

        $this->assign('taizhang',
            D('TaizhangView')->where(array('Taizhang.uid'=>$uid,'Taizhang.status'=>array('gt',1)))->order('Taizhang.create_time desc')->select());
        $this->assign('norms',
            D('TaizhangNorms')->where(array('type'=>3))->order('id asc')->select());

        $this->setWebTitle("领导表率");
        
        $this->assign('detail', $detail);
        $this->display();
    }

}