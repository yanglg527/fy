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

class IndexRightController extends BaseAuthController
{

    function _initialize()
    {
        parent::_initialize();

    }

    public function test(){
        // var_dump(M('mission_user')->alias('MS')->join('user as U on U.uid = MS.uid')->where(array('U.status'=>1,'MS.status'=>array('gt',-1),'U.organization_id'=>array('gt',0)))->fetchSql(true)->count());
        // var_dump(M('party_organization')->alias('PO')->
        //     field('PO.id, PO.name,PO.sort')->join('user as U on U.organization_id = PO.id')->join('mission_user as MU on MU.uid = U.uid')->
        //     order('PO.sort desc')->where(array('U.status'=>1,'MU.status'=>array('gt',-1)))->group('PO.id')->select());
        // $party = M('party_organization')->order('id asc')->select();

        // var_dump($party);
        // $i = 0;
        // foreach($party as $k=>$v){
        //     $party[$k]['user'] = M('user')->where(array('organization_id'=>$v['id'],'status'=>1))->select();
        //     foreach($party[$k]['user'] as $k1=>$v1){
        //         $arr[$k][$k1]= M('mission_user')->where(array('uid'=>$v1['uid']))->select();
        //         foreach($arr[$k][$k1] as $k2=>$v2){
        //             $i++;
        //         }
        //     }
        // }   
        // var_dump($i);
          $totallist = M('user')->alias('U')->join('mission_user as MU on MU.uid = u.uid')
            ->where(array('U.status'=>1,'MU.status'=>array('gt',-1),'U.branch_id'=>70))->fetchSql(true)->count();
           // ->fetchSql(true)
        var_dump($totallist);
        $this->display('/index/test');
    }
    /**
     * $page
     *
     */
    public function index()
    {

        $page =I('page',1);
        $select_type = I('selectType',1);
        $rightrankby = I('rightRankBy',1);
        $limit = I('limit',10);
        $order = I('order',0);
        switch($select_type){
            case 1 : $data = $this->dzMission($page,$rightrankby,$limit,$order);
                ajaxMsg('ok','message_ok',$data,true);
            case 2 : $data = $this->ldMission($page,$rightrankby,$limit,$order);
                ajaxMsg('ok','message_ok',$data,true);
                break;
            case 3 : $data = $this->zbMission($page,$rightrankby,$limit,$order);
                ajaxMsg('ok','message_ok',$data,true);
                break;
            case 4 : $data = $this->dyMission($page,$rightrankby,$limit,$order);
                ajaxMsg('ok','message_ok',$data,true);
                break;
        }

    }

    //任务总数公共部分
    public function allMission(){
        //右边党委总信息
        $righttop =array();
         //任务总数
        $righttop[0]['name'] = '任务总数';
        // $righttop[0]['count'] = M('user')->alias('U')->join('mission_user as MU on MU.uid = U.uid')
        //        ->where(array('U.status'=>1,'MU.status'=>array('gt',-1),'U.organization_id'=>array('gt',0)))->count();
        $righttop[0]['count'] = M('mission_user')->where(array('status'=>array('gt',-1)))->count();
        //任务未完成
        $righttop[1]['name'] = '未完成';
        $righttop[1]['count'] =  M('mission_user')->where(array('status'=>0))->count();
        //任务已完成
        $righttop[2]['name'] = '已完成';
        $righttop[2]['count'] = M('mission_user')->where(array('status'=>1))->count();
        //任务逾期完成
        $righttop[3]['name'] = '逾期完成';
        $righttop[3]['count'] = M('mission_user')->where(array('status'=>2))->count();
        return $righttop;
    }

    /**
     * 党委核心模块数据
     */
    public function dzMission($page,$rightrankby = 1,$limit = 10,$order = 0){
        //右边排序数组
        $rightlist = array();
        //右边排序数组分页情况
        $pagemessage = array();
        $pagemessage['page'] = intval($page);
        $pagemessage['limit'] = $limit;
       
        $righttop = $this->allMission();
        //->fetchSql(true)
        //右边数组:按任务总数排名
        if($rightrankby == 1){
            //当前页数的数组
            $title = ['序号','党委','总数'];
            //总条数
            $totallist = D()->query('select po.id,po.name,po.sort,count(mu.uid) AS m_count from user u right join party_organization po on u.organization_id = po.id and po.id !=21 left join mission_user mu on mu.uid=u.uid and mu.status > -1 where u.status=1 GROUP BY po.id ORDER BY m_count desc,po.sort desc');
           
            $total = count($totallist);
            $pagetotal = ceil($total/$limit);
            for($i=0;$i<$total;$i++){
                //加序号
                $totallist[$i]['rowNum']=$i+1;
            }

            if($order ==1 ){
                $totallist=quick_sort($totallist,'desc');
            }
            //分页
            $rightlist = makePage($page,$limit,$total,$totallist);
            //变排序方式？
        }
        //右边数组:按未完成排名
        else if($rightrankby == 2){
            $title = ['序号','党委','未完成'];
           //总条数
            $totallist = D()->query('select po.id,po.name,po.sort,count(mu.uid) AS m_count from user u right join party_organization po on u.organization_id = po.id and po.id !=21  left join mission_user mu on mu.uid=u.uid and mu.status = 0 where u.status=1 GROUP BY po.id ORDER BY m_count desc,po.sort desc');
           
            $total = count($totallist);
            $pagetotal = ceil($total/$limit);
            for($i=0;$i<$total;$i++){
                //加序号
                $totallist[$i]['rowNum']=$i+1;
            }

            if($order ==1 ){
                $totallist=quick_sort($totallist,'desc');
            }
            //分页
            $rightlist = makePage($page,$limit,$total,$totallist);
            //变排序方式？
        }
        //右边数组:按已完成排名
        else if($rightrankby == 3){
            $title = ['序号','党委','未完成'];
           //总条数
            $totallist = D()->query('select po.id,po.name,po.sort,count(mu.uid) AS m_count from user u right join party_organization po on u.organization_id = po.id and po.id !=21  left join mission_user mu on mu.uid=u.uid and mu.status = 1 where u.status=1 GROUP BY po.id ORDER BY m_count desc,po.sort desc');
           
            $total = count($totallist);
            $pagetotal = ceil($total/$limit);
            for($i=0;$i<$total;$i++){
                //加序号
                $totallist[$i]['rowNum']=$i+1;
            }

            if($order ==1 ){
                $totallist=quick_sort($totallist,'desc');
            }
            //分页
            $rightlist = makePage($page,$limit,$total,$totallist);
            //变排序方式？
        }
        //右边数组:按逾期完成排名
        else if($rightrankby == 4){
            $title = ['序号','党委','逾期完成'];
           //总条数
            $totallist = D()->query('select po.id,po.name,po.sort,count(mu.uid) AS m_count from user u right join party_organization po on u.organization_id = po.id and po.id !=21  left join mission_user mu on mu.uid=u.uid and mu.status = 2 where u.status=1 GROUP BY po.id ORDER BY m_count desc,po.sort desc');
           
            $total = count($totallist);
            $pagetotal = ceil($total/$limit);
            for($i=0;$i<$total;$i++){
                //加序号
                $totallist[$i]['rowNum']=$i+1;
            }

            if($order ==1 ){
                $totallist=quick_sort($totallist,'desc');
            }
            //分页
            $rightlist = makePage($page,$limit,$total,$totallist);
            //变排序方式？
        }
        $pagemessage['total'] = $total;
        $pagemessage['pagetotal'] = $pagetotal;
        //汇总
        $data = array();
        $data['rightTop'] = $righttop;
        $data['rightList'] = $rightlist;
        $data['pageMessage'] = $pagemessage;
        $data['listTitle'] = $title;
        return $data;
    }
    /**
     * 领导核心模块数据
     */
    public function ldMission($page,$rightrankby = 1,$limit = 10,$order = 0){
        //右边排序数组
        $rightlist = array();
        //右边排序数组分页情况
        $pagemessage = array();
        $pagemessage['page'] = intval($page);
        $pagemessage['limit'] = $limit;
       
        $righttop = $this->allMission();
        //->fetchSql(true)
        //右边数组:按任务总数排名
        if($rightrankby == 1){
            //当前页数的数组
            $title = ['序号','领导成员','总数'];
            //总条数 
            $totallist = M('user')->alias('u')->field('u.uid as id,u.realname as name,u.sort,count(m.uid) as m_count')->join('left join mission_user m on m.uid = u.uid and m.status > -1')->where(array('is_leader'=>1,'u.status'=>1))->group('u.uid')->order('m_count desc,u.sort desc')->select();
           // ->fetchSql(true)
            $total = count($totallist);
            $pagetotal = ceil($total/$limit);
            for($i=0;$i<$total;$i++){
                //加序号
                $totallist[$i]['rowNum']=$i+1;
            }

            if($order ==1 ){
                $totallist=quick_sort($totallist,'desc');
            }
            //分页
            $rightlist = makePage($page,$limit,$total,$totallist);
            //变排序方式？
        }
        //右边数组:按未完成排名
        else if($rightrankby == 2){
            $title = ['序号','领导成员','未完成'];
           //总条数
            $totallist = M('user')->alias('u')->field('u.uid as id,u.realname as name,u.sort,count(m.uid) as m_count')->join('left join mission_user m on m.uid = u.uid and m.status = 0')->where(array('is_leader'=>1,'u.status'=>1))->group('u.uid')->order('m_count desc,u.sort desc')->select();
           
            $total = count($totallist);
            $pagetotal = ceil($total/$limit);
            for($i=0;$i<$total;$i++){
                //加序号
                $totallist[$i]['rowNum']=$i+1;
            }

            if($order ==1 ){
                $totallist=quick_sort($totallist,'desc');
            }
            //分页
            $rightlist = makePage($page,$limit,$total,$totallist);
            //变排序方式？
        }
        //右边数组:按已完成排名
        else if($rightrankby == 3){
            $title = ['序号','领导成员','已完成'];
           //总条数
            $totallist = M('user')->alias('u')->field('u.uid as id,u.realname as name,u.sort,count(m.uid) as m_count')->join('left join mission_user m on m.uid = u.uid and m.status = 1')->where(array('is_leader'=>1,'u.status'=>1))->group('u.uid')->order('m_count desc,u.sort desc')->select();
           
            $total = count($totallist);
            $pagetotal = ceil($total/$limit);
            for($i=0;$i<$total;$i++){
                //加序号
                $totallist[$i]['rowNum']=$i+1;
            }

            if($order ==1 ){
                $totallist=quick_sort($totallist,'desc');
            }
            //分页
            $rightlist = makePage($page,$limit,$total,$totallist);
            //变排序方式？
        }
        //右边数组:按逾期完成排名
        else if($rightrankby == 4){
            $title = ['序号','领导成员','逾期完成'];
           //总条数
            $totallist = M('user')->alias('u')->field('u.uid as id,u.realname as name,u.sort,count(m.uid) as m_count')->join('left join mission_user m on m.uid = u.uid and m.status = 2')->where(array('is_leader'=>1,'u.status'=>1))->group('u.uid')->order('m_count desc,u.sort desc')->select();
           
            $total = count($totallist);
            $pagetotal = ceil($total/$limit);
            for($i=0;$i<$total;$i++){
                //加序号
                $totallist[$i]['rowNum']=$i+1;
            }

            if($order ==1 ){
                $totallist=quick_sort($totallist,'desc');
            }
            //分页
            $rightlist = makePage($page,$limit,$total,$totallist);
            //变排序方式？
        }
        $pagemessage['total'] = $total;
        $pagemessage['pagetotal'] = $pagetotal;
        //汇总
        $data = array();
        $data['rightTop'] = $righttop;
        $data['rightList'] = $rightlist;
        $data['pageMessage'] = $pagemessage;
        $data['listTitle'] = $title;
        return $data;
    }
    /**
     * 支部核心模块数据
     */
    public function zbMission($page,$rightrankby = 1,$limit = 10,$order = 0){
        //右边排序数组
        $rightlist = array();
        //右边排序数组分页情况
        $pagemessage = array();
        $pagemessage['page'] = intval($page);
        $pagemessage['limit'] = $limit;
       
        $righttop = $this->allMission();
        //->fetchSql(true)
        //右边数组:按任务总数排名
        if($rightrankby == 1){
            //当前页数的数组
            $title = ['序号','支部','总数'];
            //总条数 
           $totallist = D()->query('select pb.id,pb.name,pb.sort,count(mu.id) AS m_count from user u right join party_branch pb on u.branch_id = pb.id and pb.id!=318 left join mission_user mu on mu.uid=u.uid and mu.status > -1 where u.status=1 GROUP BY pb.id ORDER BY m_count desc,pb.sort desc');
           // ->fetchSql(true)
            $total = count($totallist);
            $pagetotal = ceil($total/$limit);
            for($i=0;$i<$total;$i++){
                //加序号
                $totallist[$i]['rowNum']=$i+1;
            }

            if($order ==1 ){
                $totallist=quick_sort($totallist,'desc');
            }
            //分页
            $rightlist = makePage($page,$limit,$total,$totallist);
            //变排序方式？
        }
        //右边数组:按未完成排名
        else if($rightrankby == 2){
            $title = ['序号','支部','未完成'];
           //总条数
            $totallist = D()->query('select pb.id,pb.name,pb.sort,count(mu.id) AS m_count from user u right join party_branch pb on u.branch_id = pb.id and pb.id!=318 left join mission_user mu on mu.uid=u.uid and mu.status = 0 where u.status=1 GROUP BY pb.id ORDER BY m_count desc,pb.sort desc');
           
            $total = count($totallist);
            $pagetotal = ceil($total/$limit);
            for($i=0;$i<$total;$i++){
                //加序号
                $totallist[$i]['rowNum']=$i+1;
            }

            if($order ==1 ){
                $totallist=quick_sort($totallist,'desc');
            }
            //分页
            $rightlist = makePage($page,$limit,$total,$totallist);
            //变排序方式？
        }
        //右边数组:按已完成排名
        else if($rightrankby == 3){
            $title = ['序号','支部','已完成'];
           //总条数
            $totallist = D()->query('select pb.id,pb.name,pb.sort,count(mu.id) AS m_count from user u right join party_branch pb on u.branch_id = pb.id and pb.id!=318 left join mission_user mu on mu.uid=u.uid and mu.status = 1 where u.status=1 GROUP BY pb.id ORDER BY m_count desc,pb.sort desc');
           
            $total = count($totallist);
            $pagetotal = ceil($total/$limit);
            for($i=0;$i<$total;$i++){
                //加序号
                $totallist[$i]['rowNum']=$i+1;
            }

            if($order ==1 ){
                $totallist=quick_sort($totallist,'desc');
            }
            //分页
            $rightlist = makePage($page,$limit,$total,$totallist);
            //变排序方式？
        }
        //右边数组:按逾期完成排名
        else if($rightrankby == 4){
            $title = ['序号','支部','逾期完成'];
           //总条数
            $totallist = D()->query('select pb.id,pb.name,pb.sort,count(mu.id) AS m_count from user u right join party_branch pb on u.branch_id = pb.id and pb.id!=318 left join mission_user mu on mu.uid=u.uid and mu.status = 2 where u.status=1 GROUP BY pb.id ORDER BY m_count desc,pb.sort desc');
           
            $total = count($totallist);
            $pagetotal = ceil($total/$limit);
            for($i=0;$i<$total;$i++){
                //加序号
                $totallist[$i]['rowNum']=$i+1;
            }

            if($order ==1 ){
                $totallist=quick_sort($totallist,'desc');
            }
            //分页
            $rightlist = makePage($page,$limit,$total,$totallist);
            //变排序方式？
        }
        $pagemessage['total'] = $total;
        $pagemessage['pagetotal'] = $pagetotal;
        //汇总
        $data = array();
        $data['rightTop'] = $righttop;
        $data['rightList'] = $rightlist;
        $data['pageMessage'] = $pagemessage;
        $data['listTitle'] = $title;
        return $data;
    }
    /**
     * 党员先锋模块数据
     */
    public function dyMission($page,$rightrankby = 1,$limit = 10,$order = 0){
        //右边排序数组
        $rightlist = array();
        //右边排序数组分页情况
        $pagemessage = array();
        $pagemessage['page'] = intval($page);
        $pagemessage['limit'] = $limit;
       
        $righttop = $this->allMission();

        $roleId = C('ROLE_PARTY_ID');//党员
        //->fetchSql(true)
        //右边数组:按任务总数排名
        if($rightrankby == 1){
            //当前页数的数组
            $title = ['序号','成员','总数'];
            //总条数 
           $totallist = M('user')->alias('u')->field('u.uid as id,u.realname as name,u.sort,count(m.uid) as m_count')->join('left join mission_user m on m.uid = u.uid and m.status > -1')->where(array('u.status'=>1,'role_id'=>$roleId))->group('u.uid')->order('m_count desc,u.sort desc')->select();
           // ->fetchSql(true)
            $total = count($totallist);
            $pagetotal = ceil($total/$limit);
            for($i=0;$i<$total;$i++){
                //加序号
                $totallist[$i]['rowNum']=$i+1;
            }

            if($order ==1 ){
                $totallist=quick_sort($totallist,'desc');
            }
            //分页
            $rightlist = makePage($page,$limit,$total,$totallist);
            //变排序方式？
        }
        //右边数组:按未完成排名
        else if($rightrankby == 2){
            $title = ['序号','成员','未完成'];
           //总条数
           $totallist = M('user')->alias('u')->field('u.uid as id,u.realname as name,u.sort,count(m.uid) as m_count')->join('left join mission_user m on m.uid = u.uid and m.status = 0')->where(array('u.status'=>1,'role_id'=>$roleId))->group('u.uid')->order('m_count desc,u.sort desc')->select();
           
            $total = count($totallist);
            $pagetotal = ceil($total/$limit);
            for($i=0;$i<$total;$i++){
                //加序号
                $totallist[$i]['rowNum']=$i+1;
            }

            if($order ==1 ){
                $totallist=quick_sort($totallist,'desc');
            }
            //分页
            $rightlist = makePage($page,$limit,$total,$totallist);
            //变排序方式？
        }
        //右边数组:按已完成排名
        else if($rightrankby == 3){
            $title = ['序号','成员','已完成'];
           //总条数
            $totallist = M('user')->alias('u')->field('u.uid as id,u.realname as name,u.sort,count(m.uid) as m_count')->join('left join mission_user m on m.uid = u.uid and m.status = 1')->where(array('u.status'=>1,'role_id'=>$roleId))->group('u.uid')->order('m_count desc,u.sort desc')->select();
           
            $total = count($totallist);
            $pagetotal = ceil($total/$limit);
            for($i=0;$i<$total;$i++){
                //加序号
                $totallist[$i]['rowNum']=$i+1;
            }

            if($order ==1 ){
                $totallist=quick_sort($totallist,'desc');
            }
            //分页
            $rightlist = makePage($page,$limit,$total,$totallist);
            //变排序方式？
        }
        //右边数组:按逾期完成排名
        else if($rightrankby == 4){
            $title = ['序号','成员','逾期完成'];
           //总条数
            $totallist = M('user')->alias('u')->field('u.uid as id,u.realname as name,u.sort,count(m.uid) as m_count')->join('left join mission_user m on m.uid = u.uid and m.status = 2')->where(array('u.status'=>1,'role_id'=>$roleId))->group('u.uid')->order('m_count desc,u.sort desc')->select();
           
            $total = count($totallist);
            $pagetotal = ceil($total/$limit);
            for($i=0;$i<$total;$i++){
                //加序号
                $totallist[$i]['rowNum']=$i+1;
            }

            if($order ==1 ){
                $totallist=quick_sort($totallist,'desc');
            }
            //分页
            $rightlist = makePage($page,$limit,$total,$totallist);
            //变排序方式？
        }
        $pagemessage['total'] = $total;
        $pagemessage['pagetotal'] = $pagetotal;
        //汇总
        $data = array();
        $data['rightTop'] = $righttop;
        $data['rightList'] = $rightlist;
        $data['pageMessage'] = $pagemessage;
        $data['listTitle'] = $title;
        return $data;
    }
}


