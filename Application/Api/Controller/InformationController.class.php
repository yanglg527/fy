<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Api\Controller;
use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;

class InformationController extends BaseAuthController
{

    function _initialize()
    {
        parent::_initialize();

    }

    /**
     * 总支部数据
     */
    public function leftIndex(){

        $hid = I('id',90);//总支部
        $page = I('page',1);
        $limit = I('limit',10);
        if(!$hid){
            return ajaxMsg(false,'missing id',array(),true);
        }
        $branchList = array();
        //先判断是不是总支部
        $info =  M('party_branch_hq')->where(array('id'=>$hid))->find();
        if($info){
            //有 还要查党员数
           $branchList = M('party_branch')->alias('pb')->
           field('pb.id,pb.name,(select ul.realname from user ul where ul.post_id =1 and ul.branch_id = pb.id limit 1) as sjname,(select count(*) from user u where u.status=1 and u.role_id =1 and u.branch_id =pb.id ) as dycount')->
           where(array('branch_hq_id'=>$hid))->order('sort desc')->select();
            $total = count($branchList);
            $pagetotal = ceil($total/$limit);
            for($i=0;$i<$total;$i++){
                //加序号
                $branchList[$i]['rowNum']=$i;
            }
            //分页
            $list = makePage($page,$limit,$total,$branchList);
        }
        else{
            // //where(array(' '=>1,'branch_id'=>$itemid))
            $branch =  M('party_branch')->alias('pb')->
            field('pb.id,pb.name ,(select ul.realname from user ul where ul.post_id =1 and ul.branch_id = pb.id limit 1) as sjname ,
            (select count(*) from user u where u.status=1 and u.role_id =1 and u.branch_id =pb.id) as dycount')->
            where(array('id'=>$hid))->find();

        }
        //整合数据
        $data =array();
        if($info){
            $data['headtitle'] = $info['name'];
            $data['list'] = $list;
            $data['pageMessage']['page'] = (int)$page;
            $data['pageMessage']['total'] = $total;
            $data['pageMessage']['pagetotal'] = $pagetotal;
        }
        else{
            $data['headtitle'] = $branch['name'];
            $data['list'][0] =$branch;
            $data['list'][0]['rowNum']=0;
            $data['pageMessage']['page'] = (int)$page;
            $data['pageMessage']['total'] = 1;
            $data['pageMessage']['pagetotal'] = 1;
        }

        ajaxMsg('ok','message_ok',$data,true);
    }

    /**
     * 右边党员信息
     */
    public function rightIndex(){
        $id = I('id');//支部id
        $page = I('page',1);
        $limit = I('limit',10);
        if(!$id){
            return ajaxMsg(false,'missing id',array(),true);
        }

        $info = M('user')->field('r.name rolename,u.uid,u.realname name')->alias('u')->join('role r ON r.id = u.role_id ')->
        where(array('u.status'=>1,'u.branch_id'=>$id))->order('u.sort desc,r.id')->select();
        if(!$info){
            return ajaxMsg(false,'woring id',array(),true);
        }
        $total = count($info);
        $pagetotal = ceil($total/$limit);
        //分页
        $list = makePage($page,$limit,$total,$info);
        $data = array();
        $data['list'] =$list;
        $data['pageMessage']['page'] = (int)$page;
        $data['pageMessage']['total'] = $total;
        $data['pageMessage']['pagetotal'] = $pagetotal;
        $data['title'] = M('party_branch')->field('id,name')->where(array('id'=>$id))->find();

        ajaxMsg('ok','message_ok',$data,true);
    }

    public function middle(){
        $hqs = M('party_branch_hq')->count();//总支
        $branch = M('party_branch')->order('sort desc')->count();//支部
        $count = M('user')->where(' status=1 and branch_id!=318 and role_id = 1')->count();
        $data['hqcount'] =$hqs;
        $data['pbcount'] =$branch;
        $data['usercount'] =$count;
        ajaxMsg('ok','message_ok',$data,true);
    }
    /**
     *
     */
    public function middleLine(){
        $hqs = M('party_branch_hq')->field('id,name ')->order('sort desc')->select();
        $list = M('party_branch')->field('id,name')->where('id != 318')->order('sort desc')->select();
        $totallist = array_merge($hqs, $list);

        $hqs = M('party_branch_hq')->where('id != 90')->count();//总支
        $branch = M('party_branch')->where('id != 318 ')->order('sort desc')->count();//支部
        $count = M('user')->where(' status=1 and branch_id!=318 and role_id = 1')->count();
        $middle['hqcount'] =$hqs;
        $middle['pbcount'] =$branch;
        $middle['usercount'] =$count;
        $data['list'] =$totallist;

        $data['middleMessage'] = $middle;
        ajaxMsg('ok','message_ok',$data,true);

    }
}


