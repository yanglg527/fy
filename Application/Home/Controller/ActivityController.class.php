<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Home\Controller;
use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;

class ActivityController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
        $this->setHeader('组织活动');
        $this->setTitle('组织活动');
    }

    public function index()
    {
        $header_left['url'] = __ROOT__."/Home/Index/index";
        $this->assign('header_left', $header_left);
        $this->setHeader('组织活动');
        $this->setTitle('组织活动');
        $this->display();
    }


}