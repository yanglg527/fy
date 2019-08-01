<?php
namespace Admin\Controller;

use Common\Controller\BaseController;
use Common\Util\ImageUploadUtil;
use Think\Controller;
use Weixin\Util\QYConfig;
use Admin\Util\AdminUtil;

/**
 * 数据迁移
 * Class SystemController
 * @package Admin\Controller
 */
class DataMigrationController extends BaseAdminController
{
    function _initialize()
    {
        parent::_initialize();
        $this->set_sidebar_module('PartyBranch');
    }

    function index(){
        $map['uid']=array('gt',10406);
       $old = D('SignInLog')->where($map)->select();
       foreach ($old as $key=>$item){
           $userOld = D('User')->where(array('uid'=>$item['uid']))->find();//根据uid获取旧表用户数据
           //根据旧表的电话与姓名确定其新表的uid
           $userNew = D('UserNew')->where(array('mobile'=>$userOld['mobile'],'realname'=>$userOld['realname']))->field('uid')->find();
//           $old[$key]['uid'] = $userNew['uid'];//修改为新的uid
           $item['uid'] = $userNew['uid'];
           if( $item['uid']) {
               D('SignInLogNew')->add($item);
           }
       }
    }

    function indexs(){
        $map['uid']=array('gt',10406);
        $old = D('User')->where($map)->field('uid,realname,mobile')->select();
        $_UserNew = M('UserNew');
        $_SignInLogNewCopy = M('SignInLogNewCopy');
        foreach ($old as $item){
            $map['realname']=$item['realname'];
            $map['mobile']=$item['mobile'];
            $new_uid_realname = $_UserNew->where(array('realname'=>$item['realname'],'mobile'=>$item['mobile']))->field('uid,realname')->find();
            if($new_uid_realname){
                $newArray[$item['uid']] = $new_uid_realname;
            }
        }
        $map['uid']=array('gt',10406);
        $SignInLog = D('SignInLog')->where($map)->select();
        foreach($SignInLog as $key =>$value){
            $value['uid'] = $newArray[$value['uid']]['uid'];
            if($value['uid']){
                $data['uid'] = $value['uid'];
                $data['create_time'] = $value['create_time'];
                $result = $_SignInLogNewCopy->add($data);
            }
        }
    }


    //统计加积分
    function  addscore(){
        $map['uid']=array('gt',46);
        $log = D('SignInLogNew')->where($map)->select();
        $newArray = array();
        //新建一个数组，键值为uid  value为签到次数
        foreach($log as $item){
            $newArray[$item['uid']] = $newArray[$item['uid']] +1;

            //往UserScoreLog表插入一条数据
            $riqi = date("Y-m-d",$item['create_time']);
            $data['content'] = $riqi."签到获得4积分";
            $data['score'] = 4;
            $data['type'] = 7;
            $data['uid']=  $item['uid'];
            $data['create_time']=  $item['create_time'];
            $id = D('UserScoreLogCopy')->add($data);
        }
        //修改user中积分
        foreach($newArray as $key=>$v){
            $score = D('User')->where(array('uid'=>$key))->field('score')->find();
            $array['score'] = $score['score']+$v*4;
            D('User')->where(array('uid'=>$key))->save($array);
        }
    }
}
