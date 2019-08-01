<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-07-04
 * Time: 16:50
 */

function get_first_tag_name($tags){
	foreach ($tags as $tag) {
		return $tag['name'];
	}
    return '';
}


function show_cost_season($year, $season){
	if($season == 1){
		return "$year-1 至 $year-3";
	}elseif($season == 2){
		return "$year-4 至 $year-6";
	}elseif($season == 3){
		return "$year-7 至 $year-9";
	}elseif($season == 4){
		return "$year-10 至 $year-12";
	}

}


/**
 * 添加待办事项已完成记录
 * @param $sourceId
 * @return int|mixed
 */
function add_todo_user_status($sourceId){
	$todo = D('Todo')->where(array('source_id'=>$sourceId))->find();
	if($todo){
		$todoUserStatus['todo_id'] = $todo['id'];
		$todoUserStatus['uid'] = uid();
		$todoUserStatus['status'] = 1;
		$todoUserStatus['create_time'] = time();
		$todoUserStatus['submit_time'] = time();

		return D('TodoUserStatus')->add($todoUserStatus);
	}
	return 0;
}