<?php
namespace Admin\Controller;

use Common\Controller\BaseController;
use Think\Controller;

/**
 * 后台用户管理
 * Class UserController
 * @package Home\Controller
 */
class UserController extends BaseAdminController
{
    public function index()
    {
        $this->display();
    }


}