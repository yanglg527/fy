<?php
namespace Todo\Util;
class TodoUtils
{
	/**
	 * 添加待办事项已完成记录
	 * @param $sourceId
	 * @return int|mixed
	 */
	static function finish($sourceId, $type){
		D('TodoUserStatus')->where(array('resource_id'=>$sourceId, 'type'=>$type))->save(array('status'=>1));
	}

	/**
	 * 设置待办事项状态
	 * @param $sourceId
	 * @return int|mixed
	 */
	static function set_url($sourceId, $type, $url){
		if($sourceId && $type){
			D('TodoUserStatus')->where(array('resource_id'=>$sourceId, 'type'=>$type))->save(array('url'=>$url));
		}

	}

	/**
	 * 设置待办事项状态
	 * @param $sourceId
	 * @return int|mixed
	 */
	static function set_status($sourceId, $type,$status){
		if($sourceId && $type) {
			D('TodoUserStatus')->where(array('resource_id' => $sourceId, 'type' => $type))->save(array('status' => $status));
		}
	}

	static function add_todo_status($sourceId, $type,$todo_id,$uid,$url){
		$todo = D('Todo')->find($todo_id);
		if($todo){
			$time = time();
			$todoUserStatus['todo_id'] = $todo['id'];
			$todoUserStatus['uid'] = $uid;
			$todoUserStatus['status'] = 0;
			$todoUserStatus['url'] = $url;
			$todoUserStatus['type'] = $type;
			$todoUserStatus['resource_id']= $sourceId;
			$todoUserStatus['create_time'] = $time;
			$todoUserStatus['submit_time'] = $time;

			return D('TodoUserStatus')->add($todoUserStatus);
		}
		return 0;
	}

	/**
	 * 添加一条待办事项
	 * @param $todo
	 * @return todo_id
	 */
	static function add_todo($todo){

		$todo['create_time'] = time();
		$todo['status'] = 1;
		$todo['request_finish_date'] = strtotime("+1 month");
		$todoId = D('Todo')->add($todo);

//    ajaxMsg(1,"todoId = ".$todoId);

		$roles = explode(',', $todo['receiver_roles']);
		$branchs = explode(',', $todo['receiver_branchs']);
		foreach ($roles as $item) {
			foreach ($branchs as $branch) {
				$todo_receiver_role['role_id'] = $item;
				$todo_receiver_role['branch_id'] = $branch;
				$todo_receiver_role['todo_id'] = $todoId;
				D('TodoReceiverRole')->add($todo_receiver_role);
			}
		}
		return $todoId;
	}



}
