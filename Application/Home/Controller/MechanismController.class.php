<?php
/**
 * Created by PhpStorm.
 * User: 四个机制
 * Date: 17/2/12
 * Time: 下午8:20
 */
namespace Home\Controller;
use Common\Controller\BaseController;
use Org\Util\Date;

class MechanismController extends BaseController {

    function _initialize(){
        parent::_initialize();//判断 登录后用户的action权限
        $this->setHeader('四个机制');
        $this->setTitle('四个机制');
    }

    function index(){
    	$header_left['url'] = __ROOT__."/Home/Index/index";
        $this->assign('header_left', $header_left);
        $this->display();
    }

}