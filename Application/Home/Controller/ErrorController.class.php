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
use Org\Util\Date;
use Think\Controller;

class ErrorController extends Controller {


    public function index(){
        $this->assign('title',"抱歉，你没有进入的权限");
        $this->assign('url',I('url',__ROOT__."/"));
        $this->display();
    }

    public function weixin(){
        $this->assign('title',"请在微信浏览器中打开");
        $this->assign('content',"请在微信浏览器中打开");
        $this->assign('url',I('url',__ROOT__."/"));
        $this->display();
    }


}












