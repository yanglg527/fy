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

class BranchController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
    }


    //信箱列表
    public function index()
    {

        //var_dump(show_img('/Uploads/Img/Event/cover/2019-01-25/5c4a74fd8c23222223e.jpg'));
        $this->assign('active',2);
        $this->check_wx_redirect('mob_party_member_index');//判断是否重定向登录
        $user = user();
        $branch_id = $user['branch_id']?$user['branch_id']:1;

        $branch = D('PartyBranch')->find($branch_id);

        $total =  D('User')->where(array('branch_id'=>$branch_id,'status'=>1))->sum('score');//找出支部得分=全部人分数的总和
        $total = $total?$total:0;
        $branch['score'] = $total;
        //台帐
        $tz =  D('TaizhangView')->where(array('branch_id'=>$branch_id,'type'=>4,'status'=>array('neq',1),'status'=>array('gt',-1)))->count();
        $branch['tz'] = $tz;
        //排名
        $count = D()->query("select count(*) as count from (select sum(score) as total_score from user u where u.status=1  group by u.branch_id  HAVING total_score>$total ) c");

        $branch['pm'] = $count[0]['count']?$count[0]['count']:1;

        //党员人数
        $branch['member_count'] = D('User')->where(array('branch_id'=>$branch_id,'status'=>1))->count();
        $this->assign('branch',$branch);

        $this->assign('taizhang1',
            D('TaizhangView')->where(array('branch_id'=>$branch_id,'type'=>4,'Taizhang.status'=>array('neq',1),'Taizhang.status'=>array('gt',-1)))->order(array('dz_count'=>'desc','create_time'=>'desc'))->limit(10)->select());
        //多余，考虑是否删除
        // $this->assign('taizhang2',
        //     D('TaizhangView')->where(array('type'=>4,'type2'=>0,'Taizhang.status'=>array('gt',-1)))->order(array('dz_count'=>'desc','create_time'=>'desc'))->limit(3)->select());

        $sj = D('PartyBranchMembers')->where(array('status'=>'1','branch_id'=>$branch_id))->find();
        //插入委员名称
        $newwy = array();
        $wy = D('PartyBranchMembers')->where(array('branch_id'=>$branch_id))->field('realname')->select();
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

        //获取支部成员信息
        $branch_sj = D('PartyBranchMembers')->where(array('branch_id'=>$branch_id,'status'=>1))->find();//书记
        $branch_fsj = D('PartyBranchMembers')->where(array('branch_id'=>$branch_id,'status'=>2))->find();//副书记
        $branch_member['sj'] = $branch_sj['realname'];
        $branch_member['fsj'] = $branch_fsj['realname'];
        $branchs = D('PartyBranch')->order('sort desc')->select();
        $this->assign('branchs',$branchs);
        $this->assign('branch_id',$branch_id);
        $this->assign('branch_member',$branch_member);
        $this->assign('realname_list',$realname_list);
        $this->assign('tags', D('TaizhangTags')->order('title asc')->select());
        $this->assign('norms',D('TaizhangNorms')->where(array('type'=>4))->order('id asc')->select());
        $this->setWebTitle("支部堡垒");
        $this->display();
    }

    public function zone()
    {
        $this->check_wx_redirect('mob_party_member_zone');//判断是否重定向登录
        $id = I('id');
        $branch = D('PartyBranch')->find($id);
        //支部成员只显示党员
        $users = D('UserView')->where(array(
            'User.branch_id'=>$id,
            'User.role_id'=>1,
            'User.status'=>1,
        ))->order('User.sort desc')->select();

        $total =  D('User')->where(array('branch_id'=>$id,'status'=>1))->sum('score');
        $total = $total?$total:0;
        $branch['score'] = $total;
        //台帐
        $tz =  D('Taizhang')->where(array('branch_id'=>$id,'type'=>4,'status'=>0))->count();
        $branch['tz'] = $tz;
        //排名
        $count = D()->query("select count(*) as count from (select sum(score) as total_score from user u where u.status=1 group by u.branch_id HAVING total_score>$total) c");
        $branch['pm'] = $count[0]['count']?$count[0]['count']+1:1;
        $this->assign('branch',$branch);

        //党支部排名
        $partyBranch = $this->get_branch_sort($id);
        if(!$partyBranch){
            $partyBranch[0]['score_all'] =0;
            $partyBranch[0]['pm'] =0;
        }
        $this->assign('partyBranch',$partyBranch);

        $this->assign('taizhang',
            D('TaizhangView')->where(array('Taizhang.branch_id'=>$id,'type'=>4,'Taizhang.status'=>array('neq',1),'Taizhang.status'=>array('gt',-1)))->order('Taizhang.create_time desc')->select());

        $this->assign('norms',
            D('TaizhangNorms')->where(array('type'=>4))->order('id asc')->select());

        $this->setWebTitle($branch['name']);

        //支部下所属党小组
        $this->assign('party_group',D('PartyGroup')->where(array('branch_id'=>$id))->select());
        $this->assign('detail',$branch);
        $this->assign('users',$users);
        $this->display();
    }


    /**
     * 获取该党支部的相关信息,得到党支部的排名
     */
    function get_branch_sort($branch_id){
        //获取全部党小组信息
        $partyBranch = D('PartyBranch')->where('id !=318')->field('id,name,cover')->select();
        //获取党小组积分
        foreach ($partyBranch as &$item){
            $score_count = D('User')->where(array('branch_id'=>$item['id'],'status'=>1))->sum('score');//积分总数
            $member_count  = D('User')->where(array('branch_id'=>$item['id'],'status'=>1))->count();//人数
            $score = round($score_count/$member_count,2);
            if($score > 0){
                $item['score'] = $score;
                $item['score_all'] = $score_count;
            }else{
                $item['score'] = 0;
            }
        }
        //按积分排序
        foreach ($partyBranch as $key=>$v){
            $newArr[$key]['score'] = $v['score'];
        }
        array_multisort($newArr,SORT_DESC,$partyBranch);//SORT_DESC为降序，SORT_ASC为升序
        //获取本支部的信息与排名
        foreach($partyBranch as $index=>$vo){
            if($vo['id'] == $branch_id){
                $vo['pm'] = $index+1;
                $data[] = $vo;
            }
        }
        return $data;
    }

    public function honor()
    {
        $honorList = D('PartyBranchHonorView')->where(array('status'=>1))->order('create_time desc')->select();
        $this->assign('honorList',$honorList);
        $this->setWebTitle("支部荣誉");
        $this->display();
    }
    public function branchIntegralShow(){


        $this->display();
    }
    public function branchMemberRank(){
       $branch_id = I('id');
        $branch = M('party_branch')->where(array('id'=>$branch_id))->find();

        if(!$branch){
            return $this->error('支部id不存在',$_COOKIE['referer']);
        }
        $this->assign("branch", $branch);
     $list = M('user')->where(array('status'=>1,'branch_id'=>$branch_id))->order('score desc')->select();
        $this->assign("list", $list);

        $this->display();
    }
}
