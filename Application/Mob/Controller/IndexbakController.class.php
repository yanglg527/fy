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

class IndexbakController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
    }
    public function test()
    {
        # code...
        exec('ls', $log, $status);
 
print_r($log);
 
print_r($status);
 
echo PHP_EOL;

    }

    //信箱列表
    public function index()
    {
        $this->check_wx_redirect('mob_index');//判断是否重定向登录
        $user = user();

        if (!$user['first_login_time']) {
            D('User')->where(array('uid'=>$user['uid']))->save(array('first_login_time'=>time()));
        }

        if($user['organization_id'] > 0){
            $group = D('PartyOrganizationView')->where(array('PartyOrganization.id'=>$user['organization_id']))->find();
        }else{
            $group = D('PartyOrganizationView')->where(array('PartyOrganization.is_main'=>1))->find();
            $user['organization_id'] = $group['id'];
        }
        $total = D('User')->where(array('organization_id'=>$group['id']))->sum('score');
        $total = $total?$total:0;
        //党组积分
        $total = D('User')->where(array('organization_id'=>$group['id']))->sum('score');
        $total = $total?$total:0;
        $user['dangzu_score'] = $total;
        //党组排名
        $count = D()->query("select count(*) as count from (select sum(score) as total_score from user u  group by u.organization_id HAVING total_score>$total) c");
        $user['dangzu_pm'] = $count[0]['count']?$count[0]['count']:1;
        //党组台账
        $total_standing_book = D('TaizhangView')->where(array('Taizhang.organization_id'=>$group['id'],'type'=>2,'status'=>0))->count();
        $user['dangzu_tz'] = $total_standing_book;

        //支部排名
        $branch_id = $user['branch_id']?$user['branch_id']:1;
        $total =  D('User')->where(array('branch_id'=>$branch_id,'status'=>1))->sum('score');//找出支部得分=全部人分数的总和
        $total = $total?$total:0;
        //支部积分
        $user['pb_score'] = $total;
        //排名
        $count = D()->query("select count(*) as count from (select sum(score) as total_score from user u where u.status=1  group by u.branch_id  HAVING total_score>$total ) c");
        $user['pb_score_rank'] = $count[0]['count']?$count[0]['count']+1:1;
        //支部台账
        $tz =  D('Taizhang')->where(array('branch_id'=>$branch_id,'type'=>4,'status'=>0))->count();
        $user['pb_taizhang'] = $tz;

        $score = $user['score'];
        $sort = D('User')->where(array('score'=>array('gt',$score)))->count();//获取用户积分排名
        $tz_count = D('Taizhang')->where(array('uid'=>$user['uid'],'status'=>0))->count();
        $user['score_pm'] = $sort == 0? 1:$sort+1;
        $user['tz_count'] = $tz_count;
        $this->assign('user',$user);

        //新版台账录入
        $this->assign('tags', D('TaizhangTags')->order('title asc')->select());
        $this->assign('norms',D('TaizhangNorms')->where(array('type'=>1))->order('id asc')->select());
        //判断账号具有什么台账录入权限 1 个人台账 2 党组台账 3 领导台账 4 支部台账
        $types = C('taizhang_type');
//        if(!isset($user['organization_id'])){
//            unset($types[1]);
//        }
//        if(!isset($user['organization_id'])&&!isset($user['leader_id'])){
//            unset($types[2]);
//        }
//        if(!isset($user['branch_id'])){
//            unset($types[3]);
//        }
        $this->assign('types',$types);

        $this->setWebTitle("首页");
        $this->display();
    }

    //根据不同类型改变指标
    public function ajaxNorms(){
        $type = I('type');
        $norms = D('TaizhangNorms')->where(array('type'=>$type))->order('id asc')->select();
        ajaxMsg(0,'',$norms);
    }

    public function index2()
    {
        $this->check_wx_redirect('mob_index');//判断是否重定向登录
        $user = user();

        $score = $user['score'];
        $sort = D('User')->where(array('score'=>array('gt',$score)))->count();//获取用户积分排名
        $tz_count = D('Taizhang')->where(array('uid'=>$user['uid']))->count();
        $user['score_pm'] = $sort == 0? 1:$sort;
        $user['tz_count'] = $tz_count;
        $this->assign('user',$user);
        $this->setWebTitle("首页");
        $this->display();
    }

    public function ajaxArticleLoading(){
        $type_name=I('type_name');
        $type_name=$type_name?$type_name:0;
        $publish_time=I('publish_time');
        $where["hidden"]=1;
        $where["status"]=1;
        if ($publish_time > 0) {
            $where["published_at"]=array('lt',$publish_time);
        }
        if ($type_name == 0) {
            $where["type_name"]=$type_name;
            $articles=D('Articles')->where($where)->order('published_at desc')->limit(10)->select();
        }else{
            $where["tag_id"]=15;
            $articles=D('ArticlesView')->where($where)->order('published_at desc')->limit(10)->select();
        }
        
        foreach($articles as $index=>$article){
            $articles[$index]['content'] = sub_str_text($article['content']);
        }
        // echo to_json_string($articles);
        ajaxMsg(0,'',$articles);
    }
	
	public function pei(){
		$total = D('user_score_log')->sum('score');
		$xxjl = D('user_score_log')->where(array('type'=>5))->sum('score');//学习交流
		$dyfz = D('user_score_log')->where(array('type'=>3))->sum('score');//党员发展
		$qtzz = D('user_score_log')->where(array('type'=>2))->sum('score');//群团组织
		$sgjz = D('user_score_log')->where(array('type'=>1))->sum('score');//四个机制
		$dyfw = D('user_score_log')->where(array('type'=>4))->sum('score');//党员服务
		$ptqd = D('user_score_log')->where(array('type'=>7))->sum('score');//平台签到
		$wdtz = D('user_score_log')->where(array('type'=>9))->sum('score');//我的通知
		$jx = D('user_score_log')->where(array('type'=>8))->sum('score');//绩效
		$hdcy = D('user_score_log')->where(array('type'=>6))->sum('score');//活动参与
		$xxjl = $xxjl?$xxjl:0;
		$dyfz = $dyfz?$dyfz:0;
		$qtzz = $qtzz?$qtzz:0;
		$sgjz = $sgjz?$sgjz:0;
		$dyfw = $dyfw?$dyfw:0;
		$ptqd = $ptqd?$ptqd:0;
		$wdtz = $wdtz?$wdtz:0;
		$jx   = $jx?$jx:0;
		$hdcy = $hdcy?$hdcy:0;
		$this->assign('total',$total);
		$this->assign('xxjl',$xxjl);
		$this->assign('dyfz',$dyfz);
		$this->assign('qtzz',$qtzz);
		$this->assign('sgjz',$sgjz);
		$this->assign('dyfw',$dyfw);
		$this->assign('ptqd',$ptqd);
		$this->assign('wdtz',$wdtz);
		$this->assign('jx',$jx);
		$this->assign('hdcy',$hdcy);
		
		$this->display();
	}

    /**
     * 新版新增台帐页面
     */
    public function newTaizhang(){
        //spec_id="+spec_id+"&tag_id="+tag_id+"&group_id="+group_id+"&type_id="+type_id;
        //先不考虑待办事项台账录入问题
        $spec_id = I('spec_id');
        $tag_id = I('tag_id');
        $group_id = I('group_id');
        $type_id = I('type_id');
        $mission_id = I('mission_id',0);
     
        $this->assign('mission_id',$mission_id);
        $this->assign('spec_id',$spec_id);
        $this->assign('tag_id',$tag_id);
        $this->assign('group_id',$group_id);
        $this->assign('type_id',$type_id);

        //组装wxjs

//       var_dump( C('WEIXINQY_CONFIG')['CORP_ID']);
unset($_SESSION['wxpic']);
        if(!isset($_SESSION['wxpic']['access_token'])||!isset($_SESSION['wxpic']['expire_time'])||(time()-$_SESSION['wxpic']['expire_time']>7200)){
            $_SESSION['wxpic']['expire_time'] =time();
            $token = get_qytoken(C('WEIXINQY_CONFIG')['CORP_ID'],C('WEIXINQY_CONFIG')['SECRET']);
//            var_dump($token);
            if($token){
                $_SESSION['wxpic']['access_token'] = $token;

                $ticket = get_qyticket($token);//存到session
                if($ticket){

                    $signature = makeSignature($ticket,$_SESSION['wxpic']['expire_time'],'Wm3WZYTjyhhh0wzccnW',curPageURL());
                    $_SESSION['wxpic']['signature'] =$signature;
                    $_SESSION['wxpic']['appId']=C('WEIXINQY_CONFIG')['CORP_ID'];
                    $_SESSION['wxpic']['noncestr']='Wm3WZYTjyhhh0wzccnW';
                }
            }
        }
//        var_dump($_SESSION['wxpic']);
        $this->assign('wxpic_session',$_SESSION['wxpic']);

        $this->display();
    }

    public  function ajaxSaveTaizhang(){
        $user = user();
        $spec_id = I('spec_id');
        $tag_id = I('tag_id');
        $norm_id = I('group_id');
        $type_id = I('type_id');
        $title  = I('title');
        $content = I('content');
        $address = I('address');
        $branch_name =I('branch_name');
        $branch_id =I('branch_id');
        $imgistrue =I('imgistrue',0);
        $publishName = I('publish_name');
        $mission_id = I('mission_id');
        $userId = $user["uid"];
        //判断是否有任务
        if($mission_id){
            $mission_info = M('mission_user')->alias('MU')->field('MU.id,MU.uid,MU.admin_uid,MU.status,MU.s_time,MU.e_time,MU.mission_id,MS.title,MS.content,MS.assist_unit_id,MS.mission_score,MS.push_person,MS.person_adm_id,MS.standard_id')->join('mission as MS on MS.id=MU.mission_id')->where(array('MU.id'=>$mission_id))->find();
            if($mission_info['admin_uid'] == $userId){
                $userId = $mission_info['uid']; 
            }
        }

        //得到图片路径后存入数据库 ,配置问题后面再改
        $tz_id = D("Taizhang")->add(array("uid"=>$userId,"title"=>$title,"publish_name"=>$user["name"],"publish_time"
        =>date('Y-m-d',time()),"type"=>$type_id,"type2"=>$spec_id,"address"=>$address,'status'=>0,
            "tag_id"=>$tag_id,"norm_id"=>$norm_id,"party_name"=>$branch_name,"template_id"=>3,'publish_name'=>$publishName,'tz_content'=>$content,
            "organization_id"=>$user['organization_id'],"branch_id"=>$user['branch_id'],"add_uid"=>$user['uid'],"create_time"=>time(),"mission_id"=>$mission_id));
        if(!$tz_id){
            ajaxMsg(0,'信息上传失败');
        }

        //台帐存成功,修改任务状态
        if($tz_id&&$mission_info){
            //提取任务台帐信息
            $tz_info = M('taizhang')->where(array('mission_id'=>$mission_id))->find();

            //更新任务表
            $save['finish_time'] = time();
            $save['taizhang_id'] = $tz_info['id'];

            if($tz_info['create_time'] > $mission_info['e_time']){
                //逾期完成
                $save['status'] = 2;
            }else{
                $save['status'] = 1;
            }

            M('mission_user')->where(array('id'=>$mission_id))->save($save);
            
        }
        
        if($imgistrue){
            $imgarr = $_SESSION['wxpic']['pic_path_arr']; //记录上传的图片路径
            for($i = 0;$i<count($imgarr);$i++){
                //图片上传
                $tzc_res = D('TaizhangContents')->add(array("type"=>$type_id,"image"=>$imgarr[$i],
                    "taizhang_id"=>$tz_id));
                if(!$tzc_res){
                    ajaxMsg(0,'图片数据上传失败');
                }
                if($i==0){
                    //设置默认封面
                    D('Taizhang')->where(array('id'=>$tz_id))->save(array('cover'=>$imgarr[$i]));
                }
            }


        }
        //到这里就插入数据库完成
        //加分系统(先不插入)
        if($mission_info['mission_score']){
            //未逾期才有积分
            if($save['status'] == 1){
                update_user_score($userId, $mission_info['mission_score'], 1,'上传任务台账'); 
            }
        }else{
            update_user_score($userId, 5, 1,'上传台账');
        }
       
        ajaxMsg(1,'新增台账成功');

    }

    /**
     * 模板3台账图片上传
     * @return
     */
    public function ajaxGetTaiZhangImg(){

        $or_path_arr = array();
        $imgarr =$_POST['imgarr'];
        $type_id = I('type_id',1);
        $tztype = C('tztype')[$type_id];
        $path =C('tzpath').$tztype;
      //  file_put_contents('./wx3.txt',var_export($imgarr,true));
        if($imgarr){
            foreach($imgarr as $k=>$v){

                $res = downloadWxImg($v,$path);
                file_put_contents('./Uploads/wx3.txt',var_export($res,true));
                if($res['flag']){
                    $or_path = $res['path'];
//                file_put_contents('./wx2.txt',var_export($or_path,true));

                    //还要生成缩略图
                    $image = new \Think\Image();
                    $start_str = substr($or_path,0,strlen($or_path)-4);
                    $last_str = substr($or_path,-4,4);
                    $thumb_path = $start_str.'_thumb'.$last_str;
                    $image->open($or_path);
                    $image->thumb(240, 250,\Think\Image::IMAGE_THUMB_CENTER)->save($thumb_path);

                    //这里是存入数据库的操作，去除 ./uploads 前的那个点。
                    $tzpath= substr($res['path'],1,strlen($res['path']));
                    $or_path_arr[] = $tzpath;


                }
                else{
                    ajaxMsg(0,$res['message']);
                }
            }
            $_SESSION['wxpic']['pic_path_arr'] = $or_path_arr;
            ajaxMsg(1002,'ok',$or_path_arr);
        }
        else{
            ajaxMsg(1000,'no data');
        }


    }
}