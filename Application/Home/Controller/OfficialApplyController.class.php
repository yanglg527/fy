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

class OfficialApplyController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
        $this->setHeader('转正申请');
        $this->setTitle('转正申请');
    }

    public function index()
    {
        $this->setHeader('转正申请');
        $this->setTitle('转正申请');
        $this->display();
    }


}