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

class IndexController extends BaseAuthController
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function newIndex()
    {
        $this->setWebTitle("首页");
        $this->display();
    }

    //信箱列表
    public function index()
    {
        $this->check_wx_redirect('mob_index'); //判断是否重定向登录
        $user = user();

        // var_dump($user['party_group_id']);
        if (!$user['first_login_time']) {
            D('User')->where(array('uid' => $user['uid']))->save(array('first_login_time' => time()));
        }

        if ($user['organization_id'] > 0) {
            $group = D('PartyOrganizationView')->where(array('PartyOrganization.id' => $user['organization_id']))->find();
        } else {
            $group = D('PartyOrganizationView')->where(array('PartyOrganization.is_main' => 1))->find();
            $user['organization_id'] = $group['id'];
        }
        //党组积分
        $total = D('User')->where(array('organization_id' => $group['id'], 'status' => 1))->sum('score');
        $total = $total ? $total : 0;
        $user['dangzu_score'] = $total;
        //党组排名
        $count = D()->query("select count(*) as count from (select sum(score) as total_score from user u  group by u.organization_id HAVING total_score>$total) c");
        $user['dangzu_pm'] = $count[0]['count'] ? $count[0]['count'] : 1;
        //党组台账
        $total_standing_book = D('TaizhangView')->where(array('Taizhang.organization_id' => $group['id'], 'type' => 2, 'status' => array('neq', 1), 'status' => array('gt', -1),))->count();
        $user['dangzu_tz'] = $total_standing_book;

        //支部排名
        $branch_id = $user['branch_id'] ? $user['branch_id'] : 1;
        $total =  D('User')->where(array('branch_id' => $branch_id, 'status' => 1))->sum('score'); //找出支部得分=全部人分数的总和
        $total = $total ? $total : 0;
        //支部积分
        $user['pb_score'] = $total;
        //排名
        $count = D()->query("select count(*) as count from (select sum(score) as total_score from user u where u.status=1 group by u.branch_id  HAVING total_score>$total ) c");
        $user['pb_score_rank'] = $count[0]['count'] ? $count[0]['count'] : 1;
        //支部台账
        $tz =  D('Taizhang')->where(array('branch_id' => $branch_id, 'type' => 4, 'status' => array('neq', 1), 'status' => array('gt', -1)))->count();
        $user['pb_taizhang'] = $tz;
        $score = $user['score'];
        $sort = D('User')->where(array('score' => array('gt', $score),'uid'=> array('neq', 1),))->count(); //获取用户积分排名
        $tz_count = D('Taizhang')->where(array('uid' => $user['uid'], 'status' => array('neq', 1), 'status' => array('gt', -1)))->count();
        $user['score_pm'] = $sort == 0 ? 1 : $sort + 1;
        $user['tz_count'] = $tz_count;
        //党小组
        $sg_total =   $total = D('User')->where(array('party_group_id' => $user['party_group_id'], 'status' => 1))->sum('score');
        if (!$sg_total) {
            $user['dangxiaozu_score'] = 0;
        } else {
            $user['dangxiaozu_score'] = $sg_total;
        }
        //台账
        $user['dangxiaozu_tz'] = D('TaizhangView')->where(array('PartyGroup.id' => $user['party_group_id'], 'Taizhang.type' => 5))->count();
        // var_dump($user1);
        //党小组排名
        //        $user['dangxiaozu_pm'] = 0;
        //        if($user['branch_id'] && $total != ''){
        //            $count = D()->query("select count(*) as count from (select sum(score) as total_score from user  u left join party_group pg  on
        //            u.party_group_id =pg.id  where pg.branch_id = " . $user['branch_id']." and u.status=1  group by u.party_group_id  HAVING total_score>$total ) c");
        //            $user['dangxiaozu_pm'] = $count[0]['count']?$count[0]['count']+1:1;
        //        }
        //党支部排名
        $partyBranch = $this->get_branch_sort($user['branch_id']);
        if (!$partyBranch) {
            $partyBranch[0]['score_all'] = 0;
            $partyBranch[0]['pm'] = 0;
        }
        $this->assign('partyBranch', $partyBranch);

        //党小组排名
        $partyGroup = $this->get_pg_sort($user['party_group_id']);
        if (!$partyGroup) {
            $partyGroup[0]['score_all'] = 0;
            $partyGroup[0]['pm'] = 0;
        }
        $this->assign('partyGroup', $partyGroup);
        $this->assign('user', $user);

        //新版台账录入
        $this->assign('tags', D('TaizhangTags')->order('title asc')->select());
        $this->assign('norms', D('TaizhangNorms')->where(array('type' => 1))->order('id asc')->select());
        //判断账号具有什么台账录入权限 1 个人台账 2 党组台账 3 领导台账 4 支部台账
        $types = C('taizhang_type');
        //      待办任务
        $badge = D('TaskeCountView')->where(array(
            'TasksItem.status' => 2, // 只统计已发出的任务
            'TasksExecutor.exe_uid' => uid(), // 当前用户
            'TasksExecutor.exe_status' => array('in', '0,2'), // 未读和进行中
        ))->count();
        $this->assign('badge', $badge);
        $this->assign('types', $types);
        $this->setWebTitle("首页");

        //文件流转
        // $user['role_id'] $user['branch_id']
        // var_dump($user['role_id']);
        // var_dump($user['branch_id']);
        $file_total = D('UserFileWander')->where(array('status' => array('gt',0)))->order('create_time desc,id desc')->select();
        $file_wander = array();

        foreach ($file_total as $k => $v) {
            //首先是自己的可以加入
            //截取
            if ($v['uid'] == $user['uid']) {
           
                $file_wander[] = $v;
            }
            //先判断支部角色和党委角色
            else if($v['dw_id'] || $v['post_id'] ){
                $dw_flag = false;
                if($v['dw_id']){
                    //找出dw的id
                    $dw = explode(',',$v['dw_id']);   
                    
                    foreach($dw as $k=>$item){
                      $dw_flag = M('party_organization_members')->where(array('status'=>$item,'uid'=>$user['uid']))->find();   
                      if($dw_flag){
                        $dw_flag = true;
                        $file_wander[] = $v;
                        break;
                      }
                    }  
                }
                //$dw_flag == false 避免重复插入数据
                if($v['post_id'] &&  $dw_flag == false){
                    $zb = explode(',',$v['post_id']);   
                    foreach($zb as $k=>$item){
                      $dw_flag = M('party_branch_members')->where(array('status'=>$item,'uid'=>$user['uid']))->find(); 
                      if($dw_flag){
                        $dw_flag = true;
                        $file_wander[] = $v;
                        break;
                      }
                    }  
                }
            }
             else if ($v['role_id'] == '' && $v['branch_id'] == '') {
              
                $file_wander[] = $v;
            } else if ($v['role_id'] != '' && $v['branch_id'] == '') {
              
                if (strpos($v['role_id'], $user['role_id']) !== false ) {
                  
                    $file_wander[] = $v;
                }
            } else if ($v['role_id'] == '' && $v['branch_id'] != '') {
                if (strpos($v['branch_id'], $user['branch_id']) !== false) {
                   
                    $file_wander[] = $v;
                }
            } else {
                if (strpos($v['branch_id'], $user['branch_id']) !== false) {
                    if (strpos($v['role_id'], $user['role_id']) !== false) {
                       
                        $file_wander[] = $v;
                    }
                }
            }
            
        }
        foreach($file_wander as $k=>$v){
           $file_wander[$k]['content'] = mb_substr($v['content'],0,30,'utf-8').'...'; 
        }
        $this->assign('file_wander', $file_wander);

        //根据是否是支部管理员来判断是否有权限
        $mission_auth = checkPublishParameter( $user['uid'], $user['branch_id'] );
        $this->assign('mission_auth', $mission_auth);
        $this->display();
    }


    /**
     * 获取该党小组的相关信息,得到党小组的排名
     */
    public function get_pg_sort($pg_id)
    {
        //获取全部党小组信息
        $partyGroup = D('PartyGroup')->field('id,name,cover,branch_id')->select();
        //获取党小组积分
        foreach ($partyGroup as &$item) {
            $score_count = D('User')->where(array('party_group_id' => $item['id'], 'status' => 1))->sum('score'); //积分总数
            $member_count  = D('User')->where(array('party_group_id' => $item['id'], 'status' => 1))->count(); //人数
            $score = round($score_count / $member_count, 2);
            if ($score > 0) {
                $item['score'] = $score;
                $item['score_all'] = $score_count;
            } else {
                $item['score'] = 0;
                $item['score_all'] = 0;
            }
        }
        //按积分排序
        foreach ($partyGroup as $key => $v) {
            $newArr[$key]['score'] = $v['score'];
        }
        array_multisort($newArr, SORT_DESC, $partyGroup); //SORT_DESC为降序，SORT_ASC为升序
        //获取排名
        foreach ($partyGroup as $index => $vo) {
            if ($vo['id'] == $pg_id) {
                $vo['pm'] = $index + 1;
                $data[] = $vo;
            }
        }
        return $data;
    }

    /**
     * 获取该支部的相关信息,得到排名
     */
    public function get_branch_sort($branch_id)
    {
        //获取全部党支部信息
        $PartyBranch = D('PartyBranch')->where('id != 318 ')->field('id,name,cover')->select();
        //获取党支部积分
        foreach ($PartyBranch as &$item) {
            $score_count = D('User')->where(array('branch_id' => $item['id'], 'status' => 1))->sum('score'); //积分总数
            $member_count  = D('User')->where(array('branch_id' => $item['id'], 'status' => 1))->count(); //人数
            $score = round($score_count / $member_count, 2);
            if ($score > 0) {
                $item['score'] = $score;
                $item['score_all'] = $score_count;
            } else {
                $item['score'] = 0;
                $item['score_all'] = 0;
            }
        }
        //按积分排序
        foreach ($PartyBranch as $key => $v) {
            $newArr[$key]['score'] = $v['score'];
        }
        array_multisort($newArr, SORT_DESC, $PartyBranch); //SORT_DESC为降序，SORT_ASC为升序
        //获取排名
        foreach ($PartyBranch as $index => $vo) {
            if ($vo['id'] == $branch_id) {
                $vo['pm'] = $index + 1;
                $data[] = $vo;
            }
        }
        return $data;
    }


    //根据不同类型改变指标
    public function ajaxNorms()
    {
        $type = I('type');
        $norms = D('TaizhangNorms')->where(array('type' => $type))->order('id asc')->select();
        ajaxMsg(0, '', $norms);
    }

    public function index2()
    {
        $this->check_wx_redirect('mob_index'); //判断是否重定向登录
        $user = user();

        $score = $user['score'];
        $sort = D('User')->where(array('score' => array('gt', $score)))->count(); //获取用户积分排名
        $tz_count = D('Taizhang')->where(array('uid' => $user['uid']))->count();
        $user['score_pm'] = $sort == 0 ? 1 : $sort;
        $user['tz_count'] = $tz_count;
        $this->assign('user', $user);
        $this->setWebTitle("首页");
        $this->display();
    }


    public function ajaxArticleLoading()
    {
        $type_name = I('type_name');
        $type_name = $type_name ? $type_name : 0;
        $publish_time = I('publish_time');
        $where["hidden"] = 1;
        $where["status"] = 1;
        if ($publish_time > 0) {
            $where["published_at"] = array('lt', $publish_time);
        }
        if ($type_name == 0) {
            // $where["type_name"]=$type_name;
            $where["tag_id"] = 20;
            $articles = D('ArticlesView')->where($where)->order('published_at desc')->limit(10)->select();
        } else {
            $where["tag_id"] = 15;
            $articles = D('ArticlesView')->where($where)->order('published_at desc')->limit(10)->select();
        }

        foreach ($articles as $index => $article) {
            $articles[$index]['content'] = sub_str_text($article['content']);
        }
        // echo to_json_string($articles);
        ajaxMsg(0, '', $articles);
    }

    public function ajaxNeWIndexLoading()
    {
        $type_name = I('type_name');
        $last_id = I('last_id');
        $type_name = $type_name ? $type_name : 0;
        $publish_time = I('publish_time');
        $where["hidden"] = 1;
        $where["status"] = 1;
        if ($publish_time > 0) {
            $where["published_at"] = array('lt', $publish_time);
        }
        if ($last_id) {
            $where["id"] = array('lt', $last_id);
        }
        // if ($type_name == 0) {
        //     $where["type_name"]=$type_name;
        //     $articles=D('Articles')->where($where)->order('published_at desc')->limit(10)->select();
        // }else{
        $where["tag_id"] = $type_name;
        $articles = D('ArticlesView')->where($where)->order('published_at desc,id desc')->limit(10)->select();
        // }

        foreach ($articles as $index => $article) {
            $articles[$index]['content'] = sub_str_text($article['content']);
        }
        // echo to_json_string($articles);
        ajaxMsg(0, '', $articles);
    }


    public function pei()
    {
        $total = D('user_score_log')->sum('score');
        $xxjl = D('user_score_log')->where(array('type' => 5))->sum('score'); //学习交流
        $dyfz = D('user_score_log')->where(array('type' => 3))->sum('score'); //党员发展
        $qtzz = D('user_score_log')->where(array('type' => 2))->sum('score'); //群团组织
        $sgjz = D('user_score_log')->where(array('type' => 1))->sum('score'); //四个机制
        $dyfw = D('user_score_log')->where(array('type' => 4))->sum('score'); //党员服务
        $ptqd = D('user_score_log')->where(array('type' => 7))->sum('score'); //平台签到
        $wdtz = D('user_score_log')->where(array('type' => 9))->sum('score'); //我的通知
        $jx = D('user_score_log')->where(array('type' => 8))->sum('score'); //绩效
        $hdcy = D('user_score_log')->where(array('type' => 6))->sum('score'); //活动参与
        $xxjl = $xxjl ? $xxjl : 0;
        $dyfz = $dyfz ? $dyfz : 0;
        $qtzz = $qtzz ? $qtzz : 0;
        $sgjz = $sgjz ? $sgjz : 0;
        $dyfw = $dyfw ? $dyfw : 0;
        $ptqd = $ptqd ? $ptqd : 0;
        $wdtz = $wdtz ? $wdtz : 0;
        $jx   = $jx ? $jx : 0;
        $hdcy = $hdcy ? $hdcy : 0;
        $this->assign('total', $total);
        $this->assign('xxjl', $xxjl);
        $this->assign('dyfz', $dyfz);
        $this->assign('qtzz', $qtzz);
        $this->assign('sgjz', $sgjz);
        $this->assign('dyfw', $dyfw);
        $this->assign('ptqd', $ptqd);
        $this->assign('wdtz', $wdtz);
        $this->assign('jx', $jx);
        $this->assign('hdcy', $hdcy);

        $this->display();
    }

    /**
     * 新版新增台帐页面
     */
    public function newTaizhang()
    {

        //spec_id="+spec_id+"&tag_id="+tag_id+"&group_id="+group_id+"&type_id="+type_id;
        //先不考虑待办事项台账录入问题
        // $spec_id = I('spec_id');
        $tag_id = I('tag_id');
        $group_id = I('group_id');
        $type_id = I('type_id');
        $mission_id = I('mission_id', 0);

        $this->assign('mission_id', $mission_id);
        $this->assign('spec_id', $spec_id);
        $this->assign('tag_id', $tag_id);
        $this->assign('group_id', $group_id);
        $this->assign('type_id', $type_id);
        $types = C('taizhang_type');
        foreach ($types as $v) {
            if ($type_id == $v['type_id']) {
                $type_name = $v['type_name'];
            }
        }
        $this->assign('type_name', $type_name);
        $this->assign('tag_name', D('TaizhangTags')->where(array('id' => $tag_id))->getField('title'));
        //组装wxjs

        //       var_dump( C('WEIXINQY_CONFIG')['CORP_ID']);
        // unset($_SESSION['wxpic']);
        if (!isset($_SESSION['wxpic']['access_token']) || !isset($_SESSION['wxpic']['expire_time']) || (time() - $_SESSION['wxpic']['expire_time'] > 7200)) {
            $_SESSION['wxpic']['expire_time'] = time();
            $token = get_qytoken(C('WEIXINQY_CONFIG')['CORP_ID'], C('WEIXINQY_CONFIG')['SECRET']);
            //            var_dump($token);
            if ($token) {
                $_SESSION['wxpic']['access_token'] = $token;

                $ticket = get_qyticket($token); //存到session

                if ($ticket) {
                    $_SESSION['wxpic']['ticket'] = $ticket;
                    $signature = makeSignature($ticket, $_SESSION['wxpic']['expire_time'], 'Wm3WZYTjyhhh0wzccnW', curPageURL());
                    $_SESSION['wxpic']['signature'] = $signature;
                    $_SESSION['wxpic']['appId'] = C('WEIXINQY_CONFIG')['CORP_ID'];
                    $_SESSION['wxpic']['noncestr'] = 'Wm3WZYTjyhhh0wzccnW';
                    $_SESSION['wxpic']['url'] = curPageURL();
                }
            }
        }

        //var_dump($_SESSION);

        $this->assign('wxpic_session', $_SESSION['wxpic']);
        $list =  D('User')->field('uid,realname,branch_id,headimgurl')->where(array('uid' => array("neq", uid()), 'status' => 1))->order("branch_id asc")->select();
        $this->assign('list', $list);
        $branch_list = D('PartyBranch')->select();

        foreach ($list as $k => $v) {
            foreach ($branch_list as $key => $value) {
                if ($value['id'] == $v['branch_id']) {
                    $branch_list[$key]['member'][] = $v;
                }
            }
            //其他没有支部的人
            $other_people[] = $v;
        }
        $index = count($branch_list);
        $branch_list[$index]['name'] = '其他';
        $branch_list[$index]['member'] = $other_people;
        $this->assign('branch_list', $branch_list);
        $this->display();
    }

    public function ajaxSaveTaizhang()
    {
        $user = user();
        // $spec_id = I('spec_id');
        $tag_id = I('tag_id');
        $more_people = I('more_people');
        $type_id = I('type_id');
        $title  = I('title');
        $content = I('content');
        $address = I('address');
        $branch_name = I('branch_name');
        $branch_id = I('branch_id');
        $imgistrue = I('imgistrue', 0);
        $publishName = I('publish_name');
        $mission_id = I('mission_id');
        $publishDate = I('publish_date');
        $compere = I('compere', '');
        if (!$publishDate) {
            $publishDate = date('Y-m-d', time());
            $create_time = time();
        } else {
            $create_time = strtotime($publishDate);
        }
        $userId = $user["uid"];
        $content = htmlspecialchars_decode($content);

        // file_put_contents('./taizhang.txt',var_export($tztouser,true));

        //判断是否有任务
        if ($mission_id) {
            $mission_info = M('mission_user')->alias('MU')->field('MU.id,MU.uid,MU.admin_uid,MU.status,MU.s_time,MU.e_time,MU.mission_id,MS.title,MS.content,MS.assist_unit_id,MS.mission_score,MS.push_person,MS.person_adm_id,MS.standard_id')->join('mission as MS on MS.id=MU.mission_id')->where(array('MU.id' => $mission_id))->find();
            if ($mission_info['admin_uid'] == $userId) {
                $userId = $mission_info['uid'];
            }
        }

        //得到图片路径后存入数据库 ,配置问题后面再改
        $tz_id = D("Taizhang")->add(array(
            "uid" => $userId, "title" => $title, "publish_time"
            => $publishDate, "type" => $type_id, "tag_id" => $tag_id, "address" => $address, 'status' => 0, "party_name" => $branch_name, "template_id" => 3, 'publish_name' => $publishName, 'tz_content' => $content,
            "organization_id" => $user['organization_id'], "branch_id" => $user['branch_id'], "add_uid" => $user['uid'], "create_time" => $create_time, "mission_id" => $mission_id
        )); //,'compere'=>$compere
        if (!$tz_id) {
            ajaxMsg(0, '信息上传失败');
        }
        //台账推给多个人
        if ($more_people) {
            $tztouser = explode(',', $more_people);
            foreach ($tztouser as $v) {
                $touser = D('userView')->where(array('uid' => $v))->find();
                if ($touser) {
                    $tz_id = D("Taizhang")->add(array(
                        "uid" => $v, "title" => $title, "publish_name" => $touser['realname'], "publish_time"
                        => $publishDate, "type" => $type_id, "tag_id" => $tag_id, "address" => $address, 'status' => 0, "party_name" => $touser['branch_name'], "template_id" => 3, 'tz_content' => $content,
                        "organization_id" => $user['organization_id'], "branch_id" => $touser['branch_id'], "add_uid" => $v, "create_time" => $create_time, "mission_id" => $mission_id
                    ));
                    if (!$tz_id) {
                        ajaxMsg(0, '信息上传失败');
                    }
                } else {
                    ajaxMsg(0, '用户不存在');
                }
            }
        }
        //台帐存成功,修改任务状态
        if ($tz_id && $mission_info) {
            //提取任务台帐信息
            $tz_info = M('taizhang')->where(array('mission_id' => $mission_id))->find();

            //更新任务表
            $save['finish_time'] = time();
            $save['taizhang_id'] = $tz_info['id'];

            if ($tz_info['create_time'] > $mission_info['e_time']) {
                //逾期完成
                $save['status'] = 2;
            } else {
                $save['status'] = 1;
            }

            M('mission_user')->where(array('id' => $mission_id))->save($save);
        }

        if ($imgistrue) {
            $imgarr = $_SESSION['wxpic']['pic_path_arr']; //记录上传的图片路径
            for ($i = 0; $i < count($imgarr); $i++) {
                //图片上传
                $tzc_res = D('TaizhangContents')->add(array(
                    "type" => $type_id, "image" => $imgarr[$i],
                    "taizhang_id" => $tz_id
                ));
                if (!$tzc_res) {
                    ajaxMsg(0, '图片数据上传失败');
                }
                if ($i == 0) {
                    //设置默认封面
                    D('Taizhang')->where(array('id' => $tz_id))->save(array('cover' => $imgarr[$i]));
                }
            }
        }
        //到这里就插入数据库完成
        //加分系统(先不插入)
        if ($mission_info['mission_score']) {
            //未逾期才有积分
            if ($save['status'] == 1) {
                update_user_score($userId, $mission_info['mission_score'], 1, '上传任务台账');
            }
        } else {
            update_user_score($userId, 5, 1, '上传台账');
        }

        ajaxMsg(1, '新增台账成功');
    }

    /**
     * 模板3台账图片上传
     * @return
     */
    public function ajaxGetTaiZhangImg()
    {
        $or_path_arr = array();
        $imgarr = $_POST['imgarr'];
        //   $type_id = I('type_id',1);
        // $tztype = C('tztype')[$type_id];
        $path = C('tzpath');
        //  file_put_contents('./wx3.txt',var_export($imgarr,true));
        if ($imgarr) {
            foreach ($imgarr as $k => $v) {
                $res = downloadWxImg($v, $path);
                //  file_put_contents('./Uploads/wx3.txt',var_export($res,true));
                if ($res['flag']) {
                    $or_path = $res['path'];
                    //                file_put_contents('./wx2.txt',var_export($or_path,true));

                    //还要生成缩略图
                    $image = new \Think\Image();
                    $start_str = substr($or_path, 0, strlen($or_path) - 4);
                    $last_str = substr($or_path, -4, 4);
                    $thumb_path = $start_str . '_thumb' . $last_str;
                    $image->open($or_path);
                    $image->thumb(240, 250, \Think\Image::IMAGE_THUMB_CENTER)->save($thumb_path);

                    //这里是存入数据库的操作，去除 ./uploads 前的那个点。
                    $tzpath = substr($res['path'], 1, strlen($res['path']));
                    $or_path_arr[] = $tzpath;
                } else {
                    ajaxMsg(0, $res['message']);
                }
            }
            $_SESSION['wxpic']['pic_path_arr'] = $or_path_arr;
            ajaxMsg(1002, 'ok', $or_path_arr);
        } else {
            ajaxMsg(1000, 'no data');
        }
    }

    public function branchList()
    {
        # code...

        $this->assign('list', D('UserView')->where(array('uid' => array("neq", uid()), 'status' => 1))->order("branch_id asc")->select());

        $this->display();
    }
    /**
     * 文件流转功能详细页
     */
    public function fileWanderPage()
    {
        $this->display();
    }

    /**
     * 文件流转功能添加编辑页面
     */
    public function addFileWander()
    {
        $file_id = I('file_id', 1);
        $detail = array();
        $branch_data = getBranchInfo();
        $role_data = C('ROLE_GROUP');
        $this->assign('branch_data', $branch_data);
        $this->assign('role_data', $role_data);
        if ($file_id) {
            //编辑
            //需要组装这几个数组branch_text role_text '{$detail.branch_arr}''{$detail.branch_text_arr}''{$detail.role_arr}''{$detail.role_text_arr}'
            //branch_id = 1,2,3
            $info = D('UserFileWander')->where(array('id' => $file_id))->find();

            $detail['branch_arr'] = $info['branch_id']; //input的value
            $detail['role_arr']  = $info['role_id']; //input的value
            $detail['branch_id_arr'] = explode(',', $info['branch_id']); //变换时的数组
            $detail['role_id_arr'] = explode(',', $info['role_id']); //变换时的数组
            foreach ($detail['branch_id_arr'] as $v) {
                //字符串
                $branch_name = M('party_branch')->where(array('id' => $v))->getField('name');
                $branch_text_arr[] = $branch_name; //支部名字数组
                $branch_text = $branch_text . $branch_name . ','; //显示在input中的名字
            }
            $detail['branch_text'] = trim($branch_text, ',');
            $detail['branch_text_arr'] = $branch_text_arr;
            foreach ($detail['role_id_arr'] as  $v) {
                //$v = 2  ==  $role_data = 1
                $key = $v - 1;
                $role_text_arr[] = $role_data[$key]['name']; //角色名字数组
                $role_text = $role_text . $role_data[$key]['name'] . ',';
                //字符串
                // $branch_name = M('party_branch')->where(array('id'=>$v))->getField('name');
                // $branch_text =$branch_text.',' .$branch_name.',';
                // $branch_text_arr[] = $branch_name;
            }
            $detail['role_text'] = trim($role_text, ',');
            $detail['role_text_arr'] = $role_text_arr;
            // var_dump($detail);
        } else {
            //新增
            $user = user();
            $detail['uid'] = $user['uid'];
            $detail['add_name'] = $user['realname'];
        }
        $this->assign('detail', $detail);
        // var_dump($detail);
        $this->display();
    }
}
