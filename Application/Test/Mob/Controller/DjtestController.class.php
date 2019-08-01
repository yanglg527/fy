<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Mob\Controller;
use Think\Controller;


class DjtestController extends Controller {




	//测试
	public function index() {
		

		$this -> display('Notes/notes_edit');
	}

}