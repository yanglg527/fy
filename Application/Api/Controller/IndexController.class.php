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
use Think\Image;
class IndexController extends BaseAuthController
{

    function _initialize()
    {
        parent::_initialize();

    }

    /**
     * 全景首页接口请求方法
     *
     */
    public function index()
    {

        $page = I('page', 1);
        //->fetchSql(true)用于查看sql;
        $select_type = I('selectType', 1);
        $leftrankby = I('leftRankBy', 1);
        $limit = I('limit', 10);
        $order = I('orderBy', 0);
        switch ($select_type) {
            case 1 :
                $data = $this->dzMessage($page, $leftrankby, $limit, $order);
                ajaxMsg('ok', 'message_ok', $data, true);
                break;
            case 2 :
                $data = $this->ldMessage($page, $leftrankby, $limit, $order);
                ajaxMsg('ok', 'message_ok', $data, true);
                break;
            case 3 :
                $data = $this->zbMessage($page, $leftrankby, $limit, $order);
                ajaxMsg('ok', 'message_ok', $data, true);
                break;
            case 4 :
                $data = $this->dyMessage($page, $leftrankby, $limit, $order);
                ajaxMsg('ok', 'message_ok', $data, true);
                break;
        }

    }

    /**
     * 党委核心模块数据
     */
    public function dzMessage($page, $leftrankby = 1, $limit = 10, $order)
    {
        //左边党委总信息

        $lefttop = array();
        //左边排序数组
        $leftlist = array();
        //左边排序数组分页情况
        $pagemessage = array();
        $pagemessage['page'] = (int)$page;
        $pagemessage['limit'] = $limit;
        //党委积分
        $lefttop[0]['name'] = '积分';
        $lefttop[0]['count'] = M('user')->where(array('status' => 1, 'organization_id' => array('gt', 0)))->sum('score');
        //台账总数
        $lefttop[1]['name'] = '台账';
        $lefttop[1]['count'] = M('Taizhang')->where(array('status' => 0, 'type' => 2, 'organization_id' => array('gt', 0)))->count();
        //成员总数
        $lefttop[2]['name'] = '成员';
        $lefttop[2]['count'] = M('user')->where(array('status' => 1, 'organization_id' => array('gt', 0)))->count();
        //->fetchSql(true)
        //左边数组:按积分排名
        if ($leftrankby == 1) {
            //当前页数的数组
            $title = ['排名', '党委', '积分'];
            //总条数
            $totallist = M('party_organization')->alias('PO')->
            field('PO.id, PO.name,PO.sort,(select sum(u1.score) from user u1 where u1.status=1 and u1.organization_id=PO.id) score')->
            where('PO.id != 21 ')->
            order('score desc,sort desc')->select();
            $total = count($totallist);
            $pagetotal = ceil($total / $limit);

            $totallist = $this->makeList($total, $order, $totallist);
            //分页
            $leftlist = makePage($page, $limit, $total, $totallist);
            //变排序方式？
        } //左边数组:按台账数量排名
        else if ($leftrankby == 2) {
            $title = ['排名', '党委', '台账'];
            $totallist = M('party_organization')->alias('PO')->
            field('PO.id, PO.name,PO.sort,(select count(*) from taizhang tz where tz.status=0 and tz.type = 2 and tz.organization_id=PO.id) score')->
            order('score desc,PO.sort desc')->select();
            $total = count($totallist);
            $pagetotal = ceil($total / $limit);

            $totallist = $this->makeList($total, $order, $totallist);
            //分页
            $leftlist = makePage($page, $limit, $total, $totallist);
        }
        $pagemessage['total'] = $total;
        $pagemessage['pagetotal'] = $pagetotal;
        //汇总
        $data = array();
        $data['leftTop'] = $lefttop;
        $data['leftList'] = $leftlist;
        $data['pageMessage'] = $pagemessage;
        $data['listTitle'] = $title;
        return $data;
    }

    /**
     * 领导核心模块数据
     */
    public function ldMessage($page = 1, $leftrankby = 1, $limit = 10, $order = 0)
    {
        //左边领导总信息
        $lefttop = array();
        //左边排序数组
        $leftlist = array();
        //左边排序数组分页情况
        $pagemessage = array();
        $pagemessage['page'] = (int)$page;
        $pagemessage['limit'] = $limit;
        //领导积分
        $lefttop[0]['name'] = '积分';
        $lefttop[0]['count'] = M('user')->where(array('status' => 1, 'is_leader' => 1))->sum('score');
        //台账总数
        $lefttop[1]['name'] = '台账';
        $lefttop[1]['count'] = M('Taizhang')->where(array('status' => 0, 'type' => 3))->count();
        //成员总数
        $lefttop[2]['name'] = '成员';
        $lefttop[2]['count'] = M('user')->where(array('status' => 1, 'is_leader' => 1))->count();

        //左边数组:按积分排名 rankby = 1 ->fetchSql(true)
        if ($leftrankby == 1) {
            $title = ['排名', '领导成员', '积分'];
            $totallist = M('user')->field('uid id,realname name,score,sort')->where(array('is_leader' => 1, 'status' => 1))->
            order('score desc,sort desc')->select();
            //总条数
            $total = count($totallist);
            $pagetotal = ceil($total / $limit);

            $totallist = $this->makeList($total, $order, $totallist);
            //分页
            $leftlist = makePage($page, $limit, $total, $totallist);

        } //左边数组:按台账数量排名
        else if ($leftrankby == 2) {
            $title = ['排名', '领导成员', '台账'];
            $totallist = M('user')->alias('u')->
            field('u.uid id,u.realname name,u.sort,(select count(*) from taizhang tz where tz.status=0 and tz.type = 3 and tz.uid=u.uid) score')->
            where(array('u.is_leader' => 1, 'u.status' => 1))->order('score desc,sort desc')->select();
            //总条数
            $total = count($totallist);
            $pagetotal = ceil($total / $limit);

            $totallist = $this->makeList($total, $order, $totallist);
            //分页
            $leftlist = makePage($page, $limit, $total, $totallist);

        }
        $pagemessage['total'] = $total;
        $pagemessage['pagetotal'] = $pagetotal;
        //汇总
        $data = array();
        $data['leftTop'] = $lefttop;
        $data['leftList'] = $leftlist;
        $data['pageMessage'] = $pagemessage;
        $data['listTitle'] = $title;

        return $data;

    }

    /**
     * 支部核心模块数据
     */
    public function zbMessage($page = 1, $leftrankby = 2, $limit = 10, $order = 1)
    {
        //总支部不用理会
        //左边支部总信息
        $lefttop = array();
        //左边排序数组
        $leftlist = array();
        //左边排序数组分页情况
        $pagemessage = array();
        $pagemessage['page'] = (int)$page;
        $pagemessage['limit'] = $limit;
        //支部积分
        $lefttop[0]['name'] = '积分';
        $lefttop[0]['count'] = M('user')->where(array('branch_id' => array('gt', 0), 'status' => 1))->sum('score');
        //台账总数
        $lefttop[1]['name'] = '台账';
        $lefttop[1]['count'] = M('Taizhang')->where(array('status' => 0, 'type' => 3))->count();
        //成员总数
        $lefttop[2]['name'] = '成员';
        $lefttop[2]['count'] = M('user')->where(array('status' => 1, 'branch_id' => array('gt', 0)))->count();

        //左边数组:按积分排名 rankby = 1
        if ($leftrankby == 1) {
            $title = ['排名', '支部', '积分'];
            $totallist = M('party_branch')->alias('pb')->
            field('pb.id,pb.name,pb.sort,(select sum(u1.score) from user u1 where u1.status=1 and u1.branch_id=pb.id) score')
                ->where('pb.id != 318 ')
                ->order('score desc,pb.sort desc')->select();
            //总条数
            $total = count($totallist);
            $pagetotal = ceil($total / $limit);

            $totallist = $this->makeList($total, $order, $totallist);
            //分页
            $leftlist = makePage($page, $limit, $total, $totallist);

        } //左边数组:按台账数量排名
        else if ($leftrankby == 2) {
            $title = ['排名', '支部', '台账'];
            $totallist = M('party_branch')->alias('pb')->
            field('pb.id, pb.name,pb.sort,(select count(*) from taizhang tz where tz.status=0 and tz.type = 4 and tz.branch_id=pb.id) score')->
            order('score desc,pb.sort desc')->select();
            //总条数
            $total = count($totallist);
            $pagetotal = ceil($total / $limit);

            $totallist = $this->makeList($total, $order, $totallist);
            //分页
            $leftlist = makePage($page, $limit, $total, $totallist);
        }

        //汇总
        $data = array();
        $pagemessage['total'] = $total;
        $pagemessage['pagetotal'] = $pagetotal;
        $data['leftTop'] = $lefttop;
        $data['leftList'] = $leftlist;
        $data['pageMessage'] = $pagemessage;
        $data['listTitle'] = $title;

        return $data;
    }

    /**
     * 党员先锋模块数据
     */
    public function dyMessage($page = 1, $leftrankby = 2, $limit = 10, $order = 1)
    {
        //总支部不用理会
        //左边支部总信息
        $lefttop = array();
        //左边排序数组
        $leftlist = array();
        //左边排序数组分页情况
        $pagemessage = array();
        $pagemessage['page'] = (int)$page;
        $pagemessage['limit'] = $limit;
        $roleId = C('ROLE_PARTY_ID');//党员
        //党员积分
        $lefttop[0]['name'] = '积分';
        $lefttop[0]['count'] = M('user')->where(array('role_id' => 1, 'status' => $roleId))->sum('score');
        //台账总数
        $lefttop[1]['name'] = '台账';
        $lefttop[1]['count'] = M('Taizhang')->where(array('status' => 0, 'type' => 1))->count();
        //成员总数
        $lefttop[2]['name'] = '成员';
        $lefttop[2]['count'] = M('user')->where(array('status' => 1, 'role_id' => $roleId))->count();

        //左边数组:按积分排名 rankby = 1
        if ($leftrankby == 1) {
            $title = ['排名', '成员', '积分'];
            $totallist = M('user')->field('uid id,realname name,score,sort')->
            where(array('status' => 1, 'role_id' => $roleId))->order('score desc,sort desc')->select();
            //总条数
            $total = count($totallist);
            $pagetotal = ceil($total / $limit);
            $totallist = $this->makeList($total, $order, $totallist);
            //分页
            $leftlist = makePage($page, $limit, $total, $totallist);
        } //左边数组:按台账数量排名
        else if ($leftrankby == 2) {
            $title = ['排名', '成员', '台账'];
            $totallist = M('user')->alias('pb')->
            field('pb.uid id, pb.realname name,pb.sort,(select count(*) from taizhang tz where tz.status=0 and tz.type = 1 and tz.uid=pb.uid) score')->
            where(array('status' => 1, 'role_id' => $roleId))->
            order('score desc,pb.sort desc')->select();
            //总条数
            $total = count($totallist);
            $pagetotal = ceil($total / $limit);

            $totallist = $this->makeList($total, $order, $totallist);
            //分页
            $leftlist = makePage($page, $limit, $total, $totallist);
        }

        //汇总
        $data = array();
        $pagemessage['total'] = $total;
        $pagemessage['pagetotal'] = $pagetotal;
        $data['leftTop'] = $lefttop;
        $data['leftList'] = $leftlist;
        $data['pageMessage'] = $pagemessage;
        $data['listTitle'] = $title;

        return $data;
    }

    public function makeList($total, $order, $totallist)
    {
        for ($i = 0; $i < $total; $i++) {
            //加序号
            $totallist[$i]['rowNum'] = $i + 1;
        }
        if ($order == 1) {
            $totallist = quick_sort($totallist, 'desc');
        }
        return $totallist;
    }

    /**
     * 首页底部行政数据
     */
    public function dangzuGraph()
    {
        $data = M('party_organization')->alias('PO')->
        field('PO.id, PO.name,PO.sort,
        (select sum(u1.score) from user u1 where u1.status=1 and u1.organization_id=PO.id) score,
        (select count(*) from taizhang tz where tz.status=0 and tz.type = 2 and tz.organization_id=PO.id) tzcount')->
        where('PO.id != 21')->
        order('score desc,sort desc')->select();
        $total = count($data);
        $data = $this->makeList($total, 0, $data);

        $data = quick_sort($data, 'desc');

        ajaxMsg('ok', 'message_ok', $data, true);
    }

    /**
     * 首页支部统计图
     */
    public function branchGraph()
    {

        $data = M('party_branch')->alias('pb')->
        field('pb.id, pb.name,pb.sort,
        (select sum(u1.score) from user u1 where u1.status=1 and u1.branch_id=pb.id) score,
        (select count(*) from taizhang tz where tz.status=0 and tz.type = 4 and tz.branch_id=pb.id) tzcount')->
        where('pb.id != 318')->
        order('score desc,sort desc')->select();
        $total = count($data);
        $data = $this->makeList($total, 0, $data);

        $data = quick_sort($data, 'desc');

        ajaxMsg('ok', 'message_ok', $data, true);
    }



    public function ajaxSaveFile()
    {
        $upload = new \Think\Upload();// 实例化上传类
        //$upload->maxSize = 3145728;// 设置附件上传大小
       // $upload->exts = array('jpg', 'gif', 'png', 'jpeg','bmp','tiff','SVG','svg','TIFF','BMP','JPG', 'GIF', 'PNG', 'JPEG');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = 'File/'; // 设置附件上传（子）目录

        $avatarData = json_decode($_POST['avatar_data'], true);
        $file = $_FILES['file'];
    //    $ext = $ext = strtolower($file['ext']);
        $size = $file['size'];
        $info = $upload->uploadOne($file);
        if (!$info) {// 上传错误提示错误信息
            ajaxMsg(1, "请上传正常文件");
        } else {
            $path = $info['savepath'] . $info['savename'];
            // $image = new Image();
            // $image->open($upload->rootPath . $path);
            // $image->crop($avatarData['width'], $avatarData['height'], $avatarData['x'], $avatarData['y'], 600, 480)->save($upload->rootPath . $path);
            // $image->crop(600, 480, 0, 0, 360, 240)->save($upload->rootPath . $path . "-m.png");
            // $image->crop(300, 240, 0, 0, 150, 120)->save($upload->rootPath . $path . "-s.png");


            // //文件上传记录
            // $filel = array(
            //     'savename' => $info['savename'],
            //     'ext' => $ext,
            //     'type' => 'image/jpeg',
            //     'savepath' => '/' . $info['savepath'],
            //     'uid' => uid(),
            //     'size' => $size,
            //     'create_time' => time()
            // );
            // D('Filelist')->add($filel);

            ajaxMsg(0, '', array('save_path' => '/' . $path,
                'show_path' => __ROOT__ . '/Uploads/' . $path));
        }
    }
    public function test()
    {
        # code...
        $json = $_POST;
        file_put_contents('./file.txt',var_export($json,true),FILE_APPEND);
        if(!$json){
            ajaxMsg(0, "error");
        }
        ajaxMsg(0, "success",$json);
    }
}


