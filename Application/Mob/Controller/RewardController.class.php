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

class RewardController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
    }

    //奖励机制
    public function index()
    {
        $user= user();
        //支部 党小组名称
        if($user['branch_id']){
            $branch_name = D('PartyBranch')->where(array('id'=>$user['branch_id']))->field('name')->find();
        }
        if($user['party_group_id']){
            $group_name = D('PartyGroup')->where(array('id'=>$user['party_group_id']))->field('name')->find();
        }
        //判断是否进行过兑换  如果没有 初始化可用积分为 个人总积分
        $result= D('RewardList')->where(array('uid'=>$user['uid']))->find();
        if(!$result){
            $data['able_score'] = $user['score'];
            $res = D('User')->where(array('uid'=>$user['uid']))->save($data);
        }
        //状态判读显示哪一个   1可兑换 -1已兑换 0我已申请
        $type = I('type',1);
        //可兑换
        $allow = D('Reward')->where(array('status'=>1,'uid'=>$user['uid']))->select();
        foreach ($allow as &$item){
            //已申请人数
            if($item['id']){
                $item['apply_count'] = D('RewardList')->where(array('good_id'=>$item['id'],'uid'=>$user['uid'],'status'=>0))->count();
                //已兑换成功人数
                $item['reward_count'] = D('RewardList')->where(array('good_id'=>$item['id'],'uid'=>$user['uid'],'status'=>1))->count();
            }
            //库存
            $item['stock'] = $item['total_count'] - $item['apply_count'] - $item['reward_count'];
        }
        //RewardList 表  ststus  2 领取兑换成功 1 审核通过 0未审核  -1申请驳回  -2放弃领取
        //已兑换
        $already = D('RewardList')->where(array('status'=>2,'uid'=>$user['uid']))->select();
        //我的申请
        $map['status'] = array('between', array(-2, 1));
        $map['uid'] = $user['uid'];
        $apply = D('RewardList')->where($map)->order('create_time desc')->select();

        $this->assign('branch_name',$branch_name);
        $this->assign('group_name',$group_name);
        $this->assign('allow',$allow);
        $this->assign('already',$already);
        $this->assign('apply',$apply);
        $this->assign('type',$type);
        $this->assign('user',$user);
        $this->display();
    }

    //兑换
    public function ajaxReward(){
        $user = user();
        $id = I('id');
        $Reward = D('Reward')->where(array('id'=>$id))->find();
        //先查库存
//        $map['stock'] = array('ELT',0);//库存小于等于0
//        $map['id'] = array('EQ',$id);//id 等于 $id
//        $hasStock = D('Reward')->where($map)->find();
        //已申请或者以兑换的数量
        $over_count = D('RewardList')->where(array('id'=>$id))->count();
        //库存数量
        $stock = $Reward['total_count'] - $over_count;
        if($stock < 1){
            ajaxMsg(1,"库存不足");
        }
        if($user['able_score'] < $Reward['point']){
            ajaxMsg(2,"积分不足");
        }
        //提交审核 ‘0’  2 领取兑换成功 1 审核通过 0未审核  -1申请驳回  -2放弃领取
        $status = '0';
        $name = $Reward['name'];
        $point = $Reward['point'];
        $good_id = $Reward['id'];
        $img_url = $Reward['img_url'];
        $array = array(
            'realname'=>$user['realname'],
            'good_id'=>$good_id,
            'uid'=>$user['uid'],
            'name'=>$name,
            'point'=>$point,
            'status'=>$status,
            'img_url'=>$img_url,
            'create_time'=>time()
        );
        //扣减可用积分
        $over_score = $user['able_score'] - $point;
        $over_score = array('able_score'=>$over_score);
        $result = D('User')->where(array('uid'=>$user['uid']))->save($over_score);
        if($result){
            $id = D('RewardList')->add($array);
        }
        ajaxMsg(0,"兑换成功");
    }

}