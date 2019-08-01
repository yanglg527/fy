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
use Weixin\Util\PayUtils;

class PartySocieyController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
        $this->setHeader('群团组织');
        $this->setTitle('群团组织');
    }

    public function detail()
    {
        $id = I('id');
        $qt = D('PartySociey')->find($id);
        $qt['intro'] = str_replace(array("\r\n", "\r", "\n"),"<br>", $qt['intro']);//换行替换显示
        $qt['honors'] = str_replace(array("\r\n", "\r", "\n"),"<br>", $qt['honors']);//换行替换显示
        $qt['other_in_charge'] = str_replace(array("\r\n", "\r", "\n"),"<br>", $qt['other_in_charge']);//换行替换显示
        $this->assign('detail',$qt);
        $this->setHeader($qt['name']);
        $this->setTitle($qt['name']);
        $this->display();
    }

}