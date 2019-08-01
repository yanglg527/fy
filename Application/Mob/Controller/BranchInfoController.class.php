<?php

/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Mob\Controller;

use Common\Controller\BaseAuthController;

class BranchInfoController extends BaseAuthController
{

    function _initialize()
    {
        parent::_initialize();
    }
    /**
     * 
     */
    public function infoList()
    {
        $this->display();
    }
    public function branchDetail(){
        $id = I('id');
        $this->assign('id',$id);
        $this->display();
    }

       // 组织信息之 城市列表
       public function index()
       {
           // $this->assign('count_dangwei', D('PartyBranchHq')->where(array('is_main' => 1))->count());
           // $this->assign('count_hq', D('PartyBranchHq')->where(array('is_main' => 0))->count());
            $partybranchId = C('ROLE_PARTY_ID');
          //党委数量
            $party_organization = D('party_organization')->where(' id != 21')->count();
            $this->assign('party_organization',$party_organization);
            //党员数量
            $party_member_count = D('User')->where("role_id=$partybranchId and status=1 and branch_id!=318")->count();
            $this->assign('party_member_count',$party_member_count);
            //党小组数量
           $group_count = D('PartyGroup')->count();
           $this->assign('group_count', $group_count);  
           //支部数量
           $branch_count = D('PartyBranch')->where('id != 318 ')->count();
            //积极分子数量
           $activist = D('User')->where(array('role_id' => 4,'status'=>1))->count();
           $this->assign('activist', $activist);
           //重点发展对象
           $keyobj = D('User')->where(array('role_id' => 3,'status'=>1))->count();
           $this->assign('keyobj', $keyobj);
           //预备党员
           $promember = D('User')->where(array('role_id' => 2,'status'=>1))->count();
           $this->assign('promember', $promember);
           //群众
           $masses = D('User')->where(array('role_id' => 5,'status'=>1))->count();
           $this->assign('masses', $masses);


           $dwsj = D('PartyOrganizationMembers')->where(array('organization_id'=>1,'status'=>1))->field('realname')->find();//党委书记
           $dwwy = D('PartyOrganizationMembers')->where(array('organization_id'=>1))->field('realname')->select();//党委委员
           foreach($dwwy as $key=>$item){
               if($item['realname'] == $dwsj['realname']){  //如果这个委员  还是书记  把他放前面
                   array_unshift($newwy,$item['realname']);
               }else{
                   $newwy[$key] = $item['realname'];
               }
           }
           if($dwwy){
               $dwwy = array_unique($newwy);//去除名字相同的元素，只留一个（存在场景：一个人同时担任多个职务）
           }else{
               $dwwy = null;//如果成员表没有数据  则为空
           }
           $jwsj = D('PartyOrganizationMembers')->where(array('organization_id'=>1,'status'=>14))->field('realname')->find();//纪委书记
           $this->assign('dwwy', $dwwy);
           $this->assign('dwsj', $dwsj['realname']);
           $this->assign('jwsj', $jwsj['realname']);
           $this->assign('branch_count', $branch_count);
           $this->setHeader('支部地区');
           $this->setTitle('支部地区');
           
           $this->display();
       }

       /**
        * 
        */
        public function organizationList()
        {
            # code...
            $this->assign('title','在职党支部');
            $branch = D('PartyBranch')->where('id != 318 ')->select();
            
            foreach($branch as $k=>$v){
                //插入书记名称
                $sj = D('PartyBranchMembers')->where(array('status'=>'1','branch_id'=>$v['id']))->find();
                $branch[$k]['sj_realname'] = $sj['realname'];
                //插入委员名称
                $newwy = array();
                $wy = D('PartyBranchMembers')->where(array('branch_id'=>$v['id']))->field('realname')->select();
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
                $branch[$k]['wy'] = $realname_list;


                $small_group = D('PartyGroupView')->where(array('PartyBranch.id'=>$v['id']))->select();
                //支部党员数
                $branch_mumber_count = D('UserView')->where(array('User.branch_id'=>$v['id'],'User.status'=>1))->count();
                $branch[$k]['branch_mumber_count'] = $branch_mumber_count;
                $sjUser = D('UserView')->where(array('post_id'=>1,'branch_id'=>$v['id']))->find();
//                $branch[$k]['sj_realname'] = $sjUser['realname'];
                if($small_group){
                    $branch[$k]['small_group_count'] = count($small_group);
                    $branch[$k]['small_group'] = $small_group;
                }else{
                    $branch[$k]['small_group'] = '';
                }


            }
            
         //  var_dump($branch);
            $this->assign('branch', $branch);
            $this->display();
        }
    // 组织信息之 城市列表
    // public function citys()
    // {
    //     // $this->assign('count_dangwei', D('PartyBranchHq')->where(array('is_main' => 1))->count());
    //     // $this->assign('count_hq', D('PartyBranchHq')->where(array('is_main' => 0))->count());
    //      $partybranchId = C('ROLE_PARTY_ID');
    //      $this->assign('count_party', D('User')->where("role_id=$partybranchId and status=1 and branch_id!=318")->count());
    //     $group_count = D('PartyGroup')->count();
    //     $count = D('PartyBranch')->where('id != 318 ')->count();
    //     $this->assign('group_count', $group_count);
    //     $this->assign('count', $count);
    //     $this->setHeader('支部地区');
    //     $this->setTitle('支部地区');
    //     $this->display();
    // }

    // 组织信息之 地区组织列表
    public function organizations()
    {
        $hqs = D('PartyBranchHqView')->group("PartyBranchHq.id")->order('PartyBranchHq.sort desc')->select();

        $list = D('PartyBranchView')->where('PartyBranch.branch_hq_id is null and PartyBranch.id !=318 ')->group("PartyBranch.id")->order('PartyBranch.sort desc')->select();

        $partybranchId = C('ROLE_PARTY_ID');
        $counts = D('User')->where("role_id=$partybranchId and status=1")->field("count(*) as count,branch_id")->group("branch_id")->order('branch_id asc')->select();


        $countMap = array();
        foreach ($counts as $count) {
            $countMap[$count['branch_id'] . ''] = $count['count'];
        }


        $sjId = C('POST_SJ_ID');
        foreach ($list as $index => $item) {
            $count = $countMap[$item['id'] . ''];
            $list[$index]['party_count'] = $count ? $count : 0;
            $branchId = $list[$index]['id'];
            $user = D('User')->where("post_id=$sjId and branch_id=$branchId")->find();
            $list[$index]['realname'] = $user['realname'];
            $list[$index]['type'] = 'branch';
        }


        foreach ($hqs as $index => $item) {
//             $li = D('UserView')->where("User.role_id=$partybranchId and User.status=1 and PartyBranch.branch_hq_id={$item['id']}")->group("User.uid")->select();
			
//      	$count = $countHqMap[$item['id'].''];
//             $item['party_count'] = count($li);
            $item['party_count'] = D('user')->where("status=1 and role_id=$partybranchId")->count();
            $item['branch_count'] = D('PartyBranch')->where(" id != 318")->count();
            $item['type'] = 'hq';
            $hqs[$index] = $item;
        }

        $this->assign('list', array_merge($hqs, $list));




        $countArray = array();
        $total = D('UserView')->where("User.status=1 and role_id=$partybranchId")->count();
        $womenCount = D('UserView')->where("User.status=1 and role_id=$partybranchId and User.gender=0")->count();

        $countArray['total'] = $total;
        $countArray['gender_women'] = $womenCount;
        $countArray['gender_man'] = $total - $womenCount;


        $date_30 = date('Y-m-d', time() - 3600 * 24 * 365 * 30);
        $date_31 = date('Y-m-d', time() - 3600 * 24 * 365 * 31);
        $date_40 = date('Y-m-d', time() - 3600 * 24 * 365 * 40);
        $date_41 = date('Y-m-d', time() - 3600 * 24 * 365 * 41);
        $date_50 = date('Y-m-d', time() - 3600 * 24 * 365 * 50);
        $date_51 = date('Y-m-d', time() - 3600 * 24 * 365 * 51);
//        $date_60 = date('Y-m-d', time()-3600*24*365*60);
        $date_61 = date('Y-m-d', time() - 3600 * 24 * 365 * 61);

        $ageCount_30_less = D('UserView')->where("User.status=1 and role_id=$partybranchId and birthday>'$date_31'")->count();
        $ageCount_30_40 = D('UserView')->where("User.status=1 and role_id=$partybranchId and birthday>'$date_41' and birthday<'$date_30'")->count();
        $ageCount_40_50 = D('UserView')->where("User.status=1 and role_id=$partybranchId and birthday>'$date_51' and birthday<'$date_40'")->count();
        $ageCount_50_60 = D('UserView')->where("User.status=1 and role_id=$partybranchId and birthday>'$date_61' and birthday<'$date_50'")->count();

        $countArray['age_30_less'] = $ageCount_30_less;
        $countArray['age_30_40'] = $ageCount_30_40;
        $countArray['age_40_50'] = $ageCount_40_50;
        $countArray['age_50_60'] = $ageCount_50_60;
        $countArray['age_60_more'] = $total - $ageCount_30_less - $ageCount_30_40 - $ageCount_40_50 - $ageCount_50_60;




        $dangling_5 = time() - 3600 * 24 * 365 * 5;
        $dangling_5l = $dangling_5 + 1;
        $dangling_10 = time() - 3600 * 24 * 365 * 10;
        $dangling_10l = $dangling_10 + 1;
        $dangling_15 = time() - 3600 * 24 * 365 * 15;
        $dangling_15l = $dangling_15 + 1;
        $dangling_20 = time() - 3600 * 24 * 365 * 21;
        $dangling_20m = $dangling_20 + 1;


        $danglingCount_5_less = D('UserView')->where("User.status=1 and role_id=$partybranchId and  official_date>$dangling_5")->count();
        $danglingCount_5_10 = D('UserView')->where("User.status=1 and role_id=$partybranchId and  official_date>$dangling_10 and official_date<$dangling_5l")->count();
        $danglingCount_10_15 = D('UserView')->where("User.status=1 and role_id=$partybranchId and  official_date>$dangling_15 and official_date<$dangling_10l")->count();
        $danglingCount_15_20 = D('UserView')->where("User.status=1 and role_id=$partybranchId and  official_date>$dangling_20 and official_date<$dangling_15l")->count();
        $danglingCount_20_more = D('UserView')->where("User.status=1 and role_id=$partybranchId and  official_date<$dangling_20m")->count();
        $countArray['danglingCount_5_less'] = $danglingCount_5_less;
        $countArray['danglingCount_5_10'] = $danglingCount_5_10;
        $countArray['danglingCount_10_15'] = $danglingCount_10_15;
        $countArray['danglingCount_15_20'] = $danglingCount_15_20;
        $countArray['danglingCount_20_more'] = $danglingCount_20_more;



        $ageCount_xx = D('UserView')->where("User.status=1 and role_id=$partybranchId and education='小学'")->count();
        $ageCount_cz = D('UserView')->where("User.status=1 and role_id=$partybranchId and education='初中'")->count();
        $ageCount_gz = D('UserView')->where("User.status=1 and role_id=$partybranchId and education='高中'")->count();
        $ageCount_zz = D('UserView')->where("User.status=1 and role_id=$partybranchId and education='中专'")->count();
        $ageCount_dz = D('UserView')->where("User.status=1 and role_id=$partybranchId and education='大专'")->count();
        $ageCount_dx = D('UserView')->where("User.status=1 and role_id=$partybranchId and education='大学'")->count();
        $ageCount_yjs = D('UserView')->where("User.status=1 and role_id=$partybranchId and education='研究生'")->count();
        $ageCount_qt = D('UserView')->where("User.status=1 and  role_id=$partybranchId and education is null")->count();

        $countArray['ageCount_xx'] = $ageCount_xx;
        $countArray['ageCount_cz'] = $ageCount_cz;
        $countArray['ageCount_gz'] = $ageCount_gz;
        $countArray['ageCount_zz'] = $ageCount_zz;
        $countArray['ageCount_dz'] = $ageCount_dz;
        $countArray['ageCount_dx'] = $ageCount_dx;
        $countArray['ageCount_yjs'] = $ageCount_yjs;
        $countArray['ageCount_qt'] = $ageCount_qt;



        $this->assign('countArray', $countArray);


        $this->setHeader('支部党员');
        $this->setTitle('支部党员');
        $this->display();
    }

    public function branchs()
    {

        $hq_id = I('id');
        $detail = D('PartyBranchHq')->find($hq_id);
        $this->assign('detail', $detail);
        $list = D('PartyBranchView')->where(array('branch_hq_id' => $hq_id))->group("PartyBranch.id")->order('PartyBranch.sort desc')->select();

        $readyId = C('ROLE_READY_ID');
        $partybranchId = C('ROLE_PARTY_ID');
        $counts = D('User')->where("(role_id=$partybranchId or role_id=$readyId) and status=1")->field("count(*) as count,branch_id")->group("branch_id")->order('branch_id asc')->select();

        $countMap = array();
        foreach ($counts as $count) {
            $countMap[$count['branch_id'] . ''] = $count['count'];
        }

        $sjId = C('POST_SJ_ID');
        foreach ($list as $index => $item) {
            $count = $countMap[$item['id'] . ''];
            $list[$index]['party_count'] = $count ? $count : 0;
            $branchId = $list[$index]['id'];
            $user = D('User')->where("post_id=$sjId and branch_id=$branchId")->find();
            $list[$index]['realname'] = $user['realname'];
        }
        $this->assign('list', $list);


        $countArray = array();
        $total = D('UserView')->where("User.status=1 and PartyBranch.branch_hq_id=$hq_id and role_id=$partybranchId")->count();
        $womenCount = D('UserView')->where("User.status=1 and PartyBranch.branch_hq_id=$hq_id and role_id=$partybranchId and User.gender=0")->count();

        $countArray['total'] = $total;
        $countArray['gender_women'] = $womenCount;
        $countArray['gender_man'] = $total - $womenCount;


        $date_30 = date('Y-m-d', time() - 3600 * 24 * 365 * 30);
        $date_31 = date('Y-m-d', time() - 3600 * 24 * 365 * 31);
        $date_40 = date('Y-m-d', time() - 3600 * 24 * 365 * 40);
        $date_41 = date('Y-m-d', time() - 3600 * 24 * 365 * 41);
        $date_50 = date('Y-m-d', time() - 3600 * 24 * 365 * 50);
        $date_51 = date('Y-m-d', time() - 3600 * 24 * 365 * 51);
//        $date_60 = date('Y-m-d', time()-3600*24*365*60);
        $date_61 = date('Y-m-d', time() - 3600 * 24 * 365 * 61);

        $ageCount_30_less = D('UserView')->where("User.status=1 and PartyBranch.branch_hq_id=$hq_id and role_id=$partybranchId and birthday>'$date_31'")->count();
        $ageCount_30_40 = D('UserView')->where("User.status=1 and PartyBranch.branch_hq_id=$hq_id and role_id=$partybranchId and birthday>'$date_41' and birthday<'$date_30'")->count();
        $ageCount_40_50 = D('UserView')->where("User.status=1 and PartyBranch.branch_hq_id=$hq_id and role_id=$partybranchId and birthday>'$date_51' and birthday<'$date_40'")->count();
        $ageCount_50_60 = D('UserView')->where("User.status=1 and PartyBranch.branch_hq_id=$hq_id and role_id=$partybranchId and birthday>'$date_61' and birthday<'$date_50'")->count();

        $countArray['age_30_less'] = $ageCount_30_less;
        $countArray['age_30_40'] = $ageCount_30_40;
        $countArray['age_40_50'] = $ageCount_40_50;
        $countArray['age_50_60'] = $ageCount_50_60;
        $countArray['age_60_more'] = $total - $ageCount_30_less - $ageCount_30_40 - $ageCount_40_50 - $ageCount_50_60;

        $ageCount_xx = D('UserView')->where("User.status=1 and PartyBranch.branch_hq_id=$hq_id and role_id=$partybranchId and education='小学'")->count();
        $ageCount_cz = D('UserView')->where("User.status=1 and PartyBranch.branch_hq_id=$hq_id and role_id=$partybranchId and education='初中'")->count();
        $ageCount_gz = D('UserView')->where("User.status=1 and PartyBranch.branch_hq_id=$hq_id and role_id=$partybranchId and education='高中'")->count();
        $ageCount_zz = D('UserView')->where("User.status=1 and PartyBranch.branch_hq_id=$hq_id and role_id=$partybranchId and education='中专'")->count();
        $ageCount_dz = D('UserView')->where("User.status=1 and PartyBranch.branch_hq_id=$hq_id and role_id=$partybranchId and education='大专'")->count();
        $ageCount_dx = D('UserView')->where("User.status=1 and PartyBranch.branch_hq_id=$hq_id and role_id=$partybranchId and education='大学'")->count();
        $ageCount_yjs = D('UserView')->where("User.status=1 and PartyBranch.branch_hq_id=$hq_id and role_id=$partybranchId and education='研究生'")->count();
        $ageCount_qt = D('UserView')->where("User.status=1 and PartyBranch.branch_hq_id=$hq_id and role_id=$partybranchId and education is null")->count();

        $countArray['ageCount_xx'] = $ageCount_xx;
        $countArray['ageCount_cz'] = $ageCount_cz;
        $countArray['ageCount_gz'] = $ageCount_gz;
        $countArray['ageCount_zz'] = $ageCount_zz;
        $countArray['ageCount_dz'] = $ageCount_dz;
        $countArray['ageCount_dx'] = $ageCount_dx;
        $countArray['ageCount_yjs'] = $ageCount_yjs;
        $countArray['ageCount_qt'] = $ageCount_qt;



        $this->assign('countArray', $countArray);


        $this->setHeader($detail['name']);
        $this->setTitle($detail['name']);
        $this->display();
    }

    public function samllGroupInfo()
    {
        # code...
        //小组人员情况
        $id = I('id',1);
        $detail = D('PartyGroupView')->where(array('id' => $id))->find();
     //   var_dump($detail);
        $this->assign('detail',$detail);

        //数
        $counts = D('user')->where("party_group_id=$id")->field("count(*) as count,role_id")->group("role_id")->order('role_id asc')->select();
        $crowdId = C('ROLE_NORMAL_ID');
        $activiteId = C('ROLE_ACTIVITE_ID');
        $tobeId = C('ROLE_TOBE_ID');
        $readyId = C('ROLE_READY_ID');
        $partyId = C('ROLE_PARTY_ID');
        $countsMap = array(
            'crowd_count' => 0,
            'activite_count' => 0,
            'tobe_count' => 0,
            'ready_count' => 0,
            'party_count' => 0
        );
        foreach ($counts as $item) {
            if ($item['role_id'] == $crowdId) {
                $countsMap['crowd_count'] = $item['count'];
            } elseif ($item['role_id'] == $activiteId) {
                $countsMap['activite_count'] = $item['count'];
            } elseif ($item['role_id'] == $tobeId) {
                $countsMap['tobe_count'] = $item['count'];
            } elseif ($item['role_id'] == $readyId || $item['role_id'] == $partyId) {
                $countsMap['party_count'] += $item['count'];
            }

        }
        $this->assign('counts',$countsMap);
        $this->display();
    }
    public function branchInfo()
    {
        $id = I('id');
        if (!$id) {
            $user = user();
            $id = $user['branch_id'];
            if (!$id)
                $id = 1;
        }

        $detail = D('PartyBranchView')->where(array('PartyBranch.id' => $id))->find();
        $counts = D('user')->where("branch_id=$id")->field("count(*) as count,role_id")->group("role_id")->order('role_id asc')->select();
        $crowdId = C('ROLE_NORMAL_ID');
        $activiteId = C('ROLE_ACTIVITE_ID');
        $tobeId = C('ROLE_TOBE_ID');
        $readyId = C('ROLE_READY_ID');
        $partyId = C('ROLE_PARTY_ID');
        $countsMap = array(
            'crowd_count' => 0,
            'activite_count' => 0,
            'tobe_count' => 0,
            'ready_count' => 0,
            'party_count' => 0
        );
        foreach ($counts as $item) {
            if ($item['role_id'] == $crowdId) {
                $countsMap['crowd_count'] = $item['count'];
            } elseif ($item['role_id'] == $activiteId) {
                $countsMap['activite_count'] = $item['count'];
            } elseif ($item['role_id'] == $tobeId) {
                $countsMap['tobe_count'] = $item['count'];
            } elseif ($item['role_id'] == $readyId || $item['role_id'] == $partyId) {
                $countsMap['party_count'] += $item['count'];
            }

        }
        $crowds = D('UserView')->where("User.status=1 and User.branch_id=$id and User.role_id=$crowdId")->order('User.sort desc')->select();
        $activites = D('UserView')->where("User.status=1 and User.branch_id=$id and User.role_id=$activiteId")->order('User.sort desc')->select();
        $tobes = D('UserView')->where("User.status=1 and User.branch_id=$id and User.role_id=$tobeId")->order('User.sort desc')->select();
//        $readys = D('UserView')->where("User.status=1 and branch_id=$id and role_id=$readyId")->select();
        $partys = D('UserView')->where("User.status=1 and User.branch_id=$id and (role_id=$partyId or role_id=$readyId)")->order('User.sort desc')->select();

        $countArray = array();
        $total = D('UserView')->where("User.status=1 and User.branch_id=$id ")->count();
        $womenCount = D('UserView')->where("User.status=1 and User.branch_id=$id and User.gender=0")->count();

        $countArray['total'] = $total;
        $countArray['gender_women'] = $womenCount;
        $countArray['gender_man'] = $total - $womenCount;


        $date_30 = date('Y-m-d', time() - 3600 * 24 * 365 * 30);
        $date_31 = date('Y-m-d', time() - 3600 * 24 * 365 * 31);
        $date_40 = date('Y-m-d', time() - 3600 * 24 * 365 * 40);
        $date_41 = date('Y-m-d', time() - 3600 * 24 * 365 * 41);
        $date_50 = date('Y-m-d', time() - 3600 * 24 * 365 * 50);
        $date_51 = date('Y-m-d', time() - 3600 * 24 * 365 * 51);
//        $date_60 = date('Y-m-d', time()-3600*24*365*60);
        $date_61 = date('Y-m-d', time() - 3600 * 24 * 365 * 61);

        $ageCount_30_less = D('UserView')->where("User.status=1 and User.branch_id=$id and birthday>'$date_31'")->count();
        $ageCount_30_40 = D('UserView')->where("User.status=1 and User.branch_id=$id and birthday>'$date_41' and birthday<'$date_30'")->count();
        $ageCount_40_50 = D('UserView')->where("User.status=1 and User.branch_id=$id and birthday>'$date_51' and birthday<'$date_40'")->count();
        $ageCount_50_60 = D('UserView')->where("User.status=1 and User.branch_id=$id and birthday>'$date_61' and birthday<'$date_50'")->count();

        $countArray['age_30_less'] = $ageCount_30_less;
        $countArray['age_30_40'] = $ageCount_30_40;
        $countArray['age_40_50'] = $ageCount_40_50;
        $countArray['age_50_60'] = $ageCount_50_60;
        $countArray['age_60_more'] = $total - $ageCount_30_less - $ageCount_30_40 - $ageCount_40_50 - $ageCount_50_60;

        $ageCount_xx = D('UserView')->where("User.status=1 and User.branch_id=$id and education='小学'")->count();
        $ageCount_cz = D('UserView')->where("User.status=1 and User.branch_id=$id and education='初中'")->count();
        $ageCount_gz = D('UserView')->where("User.status=1 and User.branch_id=$id and education='高中'")->count();
        $ageCount_zz = D('UserView')->where("User.status=1 and User.branch_id=$id and education='中专'")->count();
        $ageCount_dz = D('UserView')->where("User.status=1 and User.branch_id=$id and education='大专'")->count();
        $ageCount_dx = D('UserView')->where("User.status=1 and User.branch_id=$id and education='大学'")->count();
        $ageCount_yjs = D('UserView')->where("User.status=1 and User.branch_id=$id and education='研究生'")->count();
        $ageCount_qt = D('UserView')->where("User.status=1 and User.branch_id=$id and education is null")->count();

        $countArray['ageCount_xx'] = $ageCount_xx;
        $countArray['ageCount_cz'] = $ageCount_cz;
        $countArray['ageCount_gz'] = $ageCount_gz;
        $countArray['ageCount_zz'] = $ageCount_zz;
        $countArray['ageCount_dz'] = $ageCount_dz;
        $countArray['ageCount_dx'] = $ageCount_dx;
        $countArray['ageCount_yjs'] = $ageCount_yjs;
        $countArray['ageCount_qt'] = $ageCount_qt;



        $this->assign('countArray', $countArray);
        $this->assign('crowds', $crowds);
        $this->assign('tobes', $tobes);
        $this->assign('partys', $partys);
//        $this->assign('readys', $readys);
        $this->assign('activites', $activites);
        $this->assign('counts', $countsMap);
        $this->assign('detail', $detail);
        $this->setHeader('支部党员');
        $this->setTitle('支部党员');
        $this->display();
    }

}