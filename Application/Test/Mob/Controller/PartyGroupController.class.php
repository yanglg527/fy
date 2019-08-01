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

class PartyGroupController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
    }


    //信箱列表
    public function index()
    {
        $this->check_wx_redirect('mob_party_group_index');//判断是否重定向登录
        $user = user();
        if($user['organization_id'] > 0){
            $group = D('PartyOrganizationView')->where(array('PartyOrganization.id'=>$user['organization_id']))->find();
        }else{
            $group = D('PartyOrganizationView')->where(array('PartyOrganization.is_main'=>1))->find();
        }
        $total = D('User')->where(array('organization_id'=>$group['id']))->sum('score');
        $total = $total?$total:0;
        $group['score'] = $total;
        $groups = D('PartyOrganizationView')->order('sort desc')->select();

        $total_standing_book = D('TaizhangView')->where(array('Taizhang.organization_id'=>$group['id'],'type'=>2,'status'=>0))->count();
        $group['tz'] = $total_standing_book;


        //排名
        $count = D()->query("select count(*) as count from (select sum(score) as total_score from user u  group by u.organization_id HAVING total_score>$total) c");
        $group['pm'] = $count[0]['count']?$count[0]['count']:1;

        $this->assign('taizhang1',
            D('TaizhangView')->where(array('type'=>2,'Taizhang.status'=>array('gt',-1)))->order(array('create_time'=>'desc','dz_count'=>'desc'))->limit(10)->select());
        //点赞数多的台账
        $this->assign('taizhang2',
            D('TaizhangView')->where(array('type'=>2,'Taizhang.status'=>array('gt',-1)))->order(array('dz_count'=>'desc','create_time'=>'desc'))->limit(10)->select());

        $this->assign('group',$group);
        $this->assign('tags', D('TaizhangTags')->order('title asc')->select());
        $this->assign('norms',D('TaizhangNorms')->where(array('type'=>2))->order('id asc')->select());
        $this->assign('groups',$groups);
        $this->setWebTitle("党组核心");
        $this->display();
    }

    public function zone()
    {
        $this->check_wx_redirect('mob_party_group_zone');//判断是否重定向登录
        $id = I('id');
        $group = D('PartyOrganizationView')->where(array('PartyOrganization.id'=>$id))->find();
        $total = D('User')->where(array('organization_id'=>$group['id']))->sum('score');
        $total = $total?$total:0;
        $group['score'] = $total;
        $groups = D('PartyOrganizationView')->select();

        $total_standing_book = D('TaizhangView')->where(array('Taizhang.organization_id'=>$group['id'],'type'=>2,'status'=>0))->count();
        $group['tz'] = $total_standing_book;

        //排名
        $count = D()->query("select count(*) as count from (select sum(score) as total_score from user u  group by u.organization_id HAVING total_score>$total) c");

        $group['pm'] = $count[0]['count']?$count[0]['count']:1;

        $this->assign('taizhang',
            D('TaizhangView')->where(array('Taizhang.organization_id'=>$group['id'],'type'=>2,'Taizhang.status'=>0))->order('Taizhang.create_time desc')->limit(20)->select());

        $this->assign('norms',
            D('TaizhangNorms')->where(array('type'=>2))->order('id asc')->select());


        $this->setWebTitle($group['name'].'空间');
        $this->assign('group',$group);
        
        $this->assign('users',D('UserView')->where(array('organization_id'=>$group['id']))->order('User.sort desc')->select());
        $this->display();
    }

    



    public function users()
    {
        $this->check_wx_redirect('mob_party_member_index');//判断是否重定向登录
        $id = I('id');
        $sql = "select 
              (select count(*) from user_friends uf where uf.uid=u.uid and uf.status=1) as uf_count,
              (select count(*) from taizhang tz where tz.uid=u.uid and tz.status=0) as tz_count,u.*
                        from user u where u.status>-1 and u.organization_id=$id order by u.sort DESC";
        $this->setWebTitle("党组成员");
        $result = D()->query($sql);
        $this->assign('list', $result);
        $this->display();
    }

    public function broadcast(){
        $user = user();
        if($user['organization_id'] > 0){
            $this->assign('isGroup', true);
        }else{
            $this->assign('isGroup', false);
        }
        $this->display();
    }

    public function ajaxSendBroadcast(){
        $user = user();
        if($user['organization_id'] > 0){
            $msg = I('msg');
            if($msg != "" && $msg != "undefined" && $msg != null){
                $broadcast = array(
                    "uid"=>uid(),
                    "create_time"=>time(),
                    "content"=>$msg,
                );
                D('Broadcast')->add($broadcast);
                ajaxMsg(0, "发布成功");
            }else{
                ajaxMsg(1, "请不要发布空内容");
            }
        }else{
            ajaxMsg(1, "你不是党组成员，不能发布广播");
        }

    }

    public function ajaxGetBroadcastList(){
        $published_at=I('lastItem');
        if ($published_at == 0 || $published_at == null) {
            $published_at=time();
        }

        $where = array('Broadcast.create_time'=>array('lt', $published_at));
        $page = D('BroadcastView')->findPage($where, 20, 'Broadcast.create_time desc');

        ajaxMsg(0,to_json_string($page['list']),$page['list']);
    }
}