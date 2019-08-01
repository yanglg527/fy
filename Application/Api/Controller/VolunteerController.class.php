<?php
/**
 * 志愿服务 控制器
 */
namespace Api\Controller;

use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;
use Think\Image;
class VolunteerController extends BaseAuthController
{
    protected $domain;
    protected $userInfo;

    function _initialize()
    {
        parent::_initialize();
        $this->domain = C('DOMAIN');
        $userInfo = user();
        $this->userInfo = $userInfo;
    }

    public function volunteerBaseInfo()
    {
        $res = array(
            'all' => 0, // 进行中的
            'ingTotal' => 0, // 进行中的
            'inTotal' => 0, // 我参与的
            'endTotal' => 0, // 结束的
            'lists' => array()
         );
         $list = getUserVolunteerSignUp();
         $_service = M('VolunteerService');
         if ($_service) {
             // 得到总数
             $statuss = $_service->field('status')->where(array(
                 'status'=> array('in','1,2,3')
             ))->select();
             foreach ($statuss as $k => $v) {
                 $val[$v['status']][] = $v['status'];
             }
             $res['all'] = count($statuss);
             $res['ingTotal'] = count($val[2]);
             $res['endTotal'] = count($val[1]);
         }
         $res['inTotal'] = count($list);
         $res['lists'] = $list;
         responseSuccess($res);
    }

    public function index()
    {
        checkRequestType('get');
        $uid = $this->userInfo['uid'];
        $p = I('get.page', 1);
        $type= I('get.type', 'all');
        if ('end' == $type) {
            $status = 1;
        }elseif ('ing' == $type) {
            $status = 2;
        }elseif ('in' == $type) {
            $status = array('in', '1,2,3');
            $partner = M('VolunteerServicePartner')
                ->where(array('uid'=>$uid, 'status'=>1))
                ->getField('volunteer_id',true);
            if (empty($partner)) {
                responseError('无数据', 404);
            }
            $ids = implode(',', $partner);
            $map['VolunteerService.id'] = array('in', $ids);
        }else {
            $status = array('in', '1,2,3');
        }
        $map['VolunteerService.status'] = $status;
        $item = D('Admin/VolunteerServiceView')
            ->where($map)
            ->page("$p,15")
            ->order('create_at desc')
            ->select();
        if (!empty($item)) {
            responseSuccess($item);
        }
        responseError('无数据', 404);
    }

    public function registratioEntrance(){
        checkRequestType('post');
        $id = intval(I('post.id'));
        $_Volunteer = D('Admin/VolunteerService');
        $status = $_Volunteer->getStatusById($id);
        if ( 3 === intval($status)) {
          $_VolunteerPartner = D('Admin/VolunteerServicePartner');
          $bool = $_VolunteerPartner->signUp($id);
          if (true === $bool) {
              responseSuccess('报名成功');
          }
          responseError($bool);
        }
        responseError('已结过了报名时间了哦！！');
    }

    public function detail(){
        checkRequestType('get');
        $id = I('get.id',5);
        $_Volunteer = D('VolunteerListView');
        if ($_Volunteer) {
            $info = $_Volunteer->find($id);
            if ($info) {
                $_Partner = D('VolunteerPartnerView');
                $users = $_Partner->where(array(
                    'VolunteerServicePartner.status'=> 1,
                    'VolunteerServicePartner.volunteer_id'=>$id
                ))->select();
                $info['users'] = $users;
                responseSuccess($info);
            }
            responseError('没找到记录', 404);
        }
        responseError('服务器发生错误！请稍后重试..', 500);
    }

}
