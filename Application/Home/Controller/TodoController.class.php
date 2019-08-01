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

class TodoController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
        $this->setHeader('待办事项');
        $this->setTitle('待办事项');
    }

    public function index()
    {
        $this->setHeader('待办事项');
        $this->setTitle('待办事项');
        $this->display();
    }


}