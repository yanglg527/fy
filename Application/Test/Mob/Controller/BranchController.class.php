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
        $this->check_wx_redirect('mob_party_member_index');//判断是否重定向登录
        $user = user();
        $branch_id = $user['branch_id']?$user['branch_id']:1;

        $branch = D('PartyBranch')->find($branch_id);

        $total =  D('User')->where(array('branch_id'=>$branch_id,'status'=>1))->sum('score');//找出支部得分=全部人分数的总和
        $total = $total?$total:0;
        $branch['score'] = $total;
        //台帐
        $tz =  D('Taizhang')->where(array('branch_id'=>$branch_id,'type'=>4,'status'=>0))->count();
        $branch['tz'] = $tz;
        //排名
        $count = D()->query("select count(*) as count from (select sum(score) as total_score from user u where u.status=1  group by u.branch_id  HAVING total_score>$total ) c");

        $branch['pm'] = $count[0]['count']?$count[0]['count']+1:1;
        $this->assign('branch',$branch);

        $this->assign('taizhang1',
            D('TaizhangView')->where(array('type'=>4,'Taizhang.status'=>array('gt',-1)))->order(array('dz_count'=>'desc','create_time'=>'desc'))->limit(10)->select());
        //多余，考虑是否删除
        $this->assign('taizhang2',
            D('TaizhangView')->where(array('type'=>4,'type2'=>0,'Taizhang.status'=>array('gt',-1)))->order(array('dz_count'=>'desc','create_time'=>'desc'))->limit(3)->select());



        $branchs = D('PartyBranch')->order('sort desc')->select();
        $this->assign('branchs',$branchs);
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
        $users = D('UserView')->where(array('User.branch_id'=>$id))->order('User.sort desc')->select();

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


        $this->assign('taizhang',
            D('TaizhangView')->where(array('Taizhang.branch_id'=>$id,'type'=>4,'Taizhang.status'=>0))->order('Taizhang.create_time desc')->select());

        $this->assign('norms',
            D('TaizhangNorms')->where(array('type'=>4))->order('id asc')->select());

        $this->setWebTitle($branch['name']);
        
        $this->assign('detail',$branch);
        $this->assign('users',$users);
        $this->display();
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