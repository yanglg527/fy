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

class SmallGroupController extends BaseAuthController
{

    function _initialize()
    {
        parent::_initialize();
    }


    //信箱列表
    public function index()
    {
        $user = user();
        $this->assign('active', 3);
        $this->check_wx_redirect('mob_party_member_index');//判断是否重定向登录
        $pg_id = I('pg_id');
        if(!$pg_id){
            $pg_id =  $user['party_group_id'];
        }
        //党小组信息
        if($pg_id){
            $detail = D('PartyGroupView')->where(array('PartyGroup.id' => $pg_id))->find();
            $taizhang = D('TaizhangView')->where(array('type' => 5, 'party_group_id' => $pg_id,'Taizhang.status'=>array('neq',1), 'Taizhang.status' => array('gt', -1)))->order(array('dz_count' => 'desc', 'create_time' => 'desc'))->limit(10)->select();
        }
        $detail['dangxiaozu_tz'] = count($taizhang);
        //如果还是空，表明该用户没加入党小组
        $sg_total = $total = D('User')->where(array('party_group_id' => $pg_id,'status'=>1))->sum('score');
        if (!$sg_total) {
            $detail['dangxiaozu_score'] = 0;
        } else {
            $detail['dangxiaozu_score'] = $sg_total;
        }
        //党小组排名
        $partyGroup = $this->get_pg_sort($pg_id);
        //小组内其他人员信息
        $users = D('user')->where(array('party_group_id' => $pg_id))->select();
        //同支部其他小组信息
        $other_party_group = D('PartyGroupView')->where(array('PartyGroup.branch_id' => $detail['branch_id'],'PartyGroup.id' =>array('neq',$pg_id)))->select();
        $this->assign('other_party_group', $other_party_group);
        $this->assign('partyGroup', $partyGroup);
        $this->assign('taizhang', $taizhang);
        $this->assign('users', $users);
        $this->assign('detail', $detail);
        $this->setWebTitle("党小组");
        $this->display();
    }

    public function zone()
    {
        $this->check_wx_redirect('mob_party_group_zone');//判断是否重定向登录
        $id = I('id');
        $group = D('PartyOrganizationView')->where(array('PartyOrganization.id' => $id))->find();
        $total = D('User')->where(array('organization_id' => $group['id']))->sum('score');
        $total = $total ? $total : 0;
        $group['score'] = $total;
        $groups = D('PartyOrganizationView')->select();

        $total_standing_book = D('TaizhangView')->where(array('Taizhang.organization_id' => $group['id'], 'type' => 2, 'status' => 0))->count();
        $group['tz'] = $total_standing_book;

        //排名
        $count = D()->query("select count(*) as count from (select sum(score) as total_score from user u  group by u.organization_id HAVING total_score>$total) c");

        $group['pm'] = $count[0]['count'] ? $count[0]['count'] : 1;

        $this->assign(
            'taizhang',
            D('TaizhangView')->where(array('Taizhang.organization_id' => $group['id'], 'type' => 2,'Taizhang.status'=>array('neq',2), 'Taizhang.status' => array('gt',-1)))->order('Taizhang.create_time desc')->limit(20)->select()
        );

        $this->assign(
            'norms',
            D('TaizhangNorms')->where(array('type' => 2))->order('id asc')->select()
        );


        $this->setWebTitle($group['name'] . '空间');
        $this->assign('group', $group);

        $this->assign('users', D('UserView')->where(array('organization_id' => $group['id']))->order('User.sort desc')->select());
        $this->display();
    }

    



    // public function users()
    // {
    //     $this->check_wx_redirect('mob_party_member_index');//判断是否重定向登录
    //     $id = I('id');
    //     $sql = "select 
    //           (select count(*) from user_friends uf where uf.uid=u.uid and uf.status=1) as uf_count,
    //           (select count(*) from taizhang tz where tz.uid=u.uid and tz.status=0) as tz_count,u.*
    //                     from user u where u.status>-1 and u.organization_id=$id order by u.sort DESC";
    //     $this->setWebTitle("党组成员");
    //     $result = D()->query($sql);
    //     $this->assign('list', $result);
    //     $this->display();
    // }

    // public function broadcast(){
    //     $user = user();
    //     if($user['organization_id'] > 0){
    //         $this->assign('isGroup', true);
    //     }else{
    //         $this->assign('isGroup', false);
    //     }
    //     $this->display();
    // }

    // public function ajaxSendBroadcast(){
    //     $user = user();
    //     if($user['organization_id'] > 0){
    //         $msg = I('msg');
    //         if($msg != "" && $msg != "undefined" && $msg != null){
    //             $broadcast = array(
    //                 "uid"=>uid(),
    //                 "create_time"=>time(),
    //                 "content"=>$msg,
    //             );
    //             D('Broadcast')->add($broadcast);
    //             ajaxMsg(0, "发布成功");
    //         }else{
    //             ajaxMsg(1, "请不要发布空内容");
    //         }
    //     }else{
    //         ajaxMsg(1, "你不是党组成员，不能发布广播");
    //     }

    // }

    // public function ajaxGetBroadcastList(){
    //     $published_at=I('lastItem');
    //     if ($published_at == 0 || $published_at == null) {
    //         $published_at=time();
    //     }

    //     $where = array('Broadcast.create_time'=>array('lt', $published_at));
    //     $page = D('BroadcastView')->findPage($where, 20, 'Broadcast.create_time desc');

    //     ajaxMsg(0,to_json_string($page['list']),$page['list']);
    // }


    /**
     * 党小组积分排名
     */
    public function group_contrast(){
        $group_id = I('group_id');
        $partyGroup = $this->get_party_group_sort($group_id);
        $this->assign("list", $partyGroup);
        $this->display();
    }

    /**
     * 获取该党小组的相关信息,得到党小组的排名
     */
    function get_pg_sort($pg_id){
        //获取全部党小组信息
        $partyGroup = D('PartyGroup')->field('id,name,cover,branch_id')->select();
        //获取党小组积分
        foreach ($partyGroup as &$item){
            $score_count = D('User')->where(array('party_group_id'=>$item['id'],'status'=>1))->sum('score');//积分总数
            $member_count  = D('User')->where(array('party_group_id'=>$item['id'],'status'=>1))->count();//人数
            $score = round($score_count/$member_count,2);
            if($score > 0){
                $item['score'] = $score;
                $item['score_all'] = $score_count;
            }else{
                $item['score'] = 0;
            }
        }
        //按积分排序
        foreach ($partyGroup as $key=>$v){
            $newArr[$key]['score'] = $v['score'];
        }
        array_multisort($newArr,SORT_DESC,$partyGroup);//SORT_DESC为降序，SORT_ASC为升序
        //获取本支部的信息与排名
        foreach($partyGroup as $index=>$vo){
            if($vo['id'] == $pg_id){
                $vo['pm'] = $index+1;
                $data[] = $vo;
            }
        }
        return $data;
    }

    /**
     * 获取该党小组所在党支部的相关信息，进行排序
     */
    function get_party_group_sort($group_id){
        $branchid = D('PartyGroup')->where(array('id'=>$group_id))->field('branch_id')->find();
        //获取党小组相同支部的全部党小组信息
        $partyGroup = D('PartyGroup')->where(array('branch_id'=>$branchid['branch_id']))->field('id,name,cover')->select();
        //获取党小组积分
        foreach ($partyGroup as &$item){
            $score_count = D('User')->where(array('party_group_id'=>$item['id']))->sum('score');//积分总数
            $member_count  = D('User')->where(array('party_group_id'=>$item['id']))->count();//人数
            $score = round($score_count/$member_count,2);
            if($score > 0 ){
                $item['score'] = $score;
                $item['score_all'] = $score_count;
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

}