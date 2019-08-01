<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Mob\Controller;
use Common\Controller\BaseAuthController;
use Common\Util\ImageUploadUtil;
use Weixin\Util\PayUtils;

class PartyServeController extends BaseAuthController {

	function _initialize() {
		parent::_initialize();
	}

	public function index() {
		$this -> check_wx_redirect('party_serve_index');
		//判断是否重定向登录

		$this -> display();
	}

	/**
	 *
	 */
	function ajaxGetSpeakingList() {
		$lastItem = I("lastItem");
		if ($lastItem == 0) {
			$lastItem = time();
		}

		$list = D() -> query("select user.realname as user_realname,user.headimgurl,speaking.content,speaking.id,speaking.create_time,
                            (select count(*) from speaking_comment where speaking_comment.speaking_id = speaking.id) AS comment_count,
                            (select count(*) from speaking_agree where speaking_agree.speaking_id = speaking.id) AS agree_count 
                            from speaking LEFT JOIN user ON speaking.uid=user.uid 
                            where (speaking.create_time < $lastItem and speaking.status = 1) order by speaking.create_time desc limit 10");

		//        $speakingList = D("SpeakingView")->where(array("status"=>0, "create_time"=>array("lt", $lastItem)))->order("create_time desc")->limit(20)->select();
		ajaxMsg(0, to_json_string($list), $list);
	}

	function ajaxSaveTruth() {
		$truthTitle = I("title");
		$truthContent = I("content");
		if (empty($truthTitle) || empty($truthContent)) {
			ajaxMsg(1, "标题和内容都不能为空");
		} else {
			$truth = array('status' => 0, "title" => $truthTitle, "content" => $truthContent, "create_time" => time(), "uid" => uid());
			D("Speaking") -> add($truth);
			ajaxMsg(0, "");
		}
	}

	function truthinfo() {
		$id = I('id');
		//        exit("id = ".$id);
		$id = $id ? $id : I('state');
		$this -> check_wx_redirect('party_serve_truthinfo', $id);
		//判断是否重定向登录
		$user = user();

		if ($id > 0) {
			$truth = D('Speaking') -> find($id);
			if ($user && $truth) {
				$click_log = D('SpeakingClickLog') -> where(array('uid' => $user['uid'], 'speaking_id' => $truth['id'])) -> find();
				if (!$click_log) {
					update_user_score($user["uid"], 2, 4, '我向组织说句话阅读');
					D('SpeakingClickLog') -> add(array('uid' => $user['uid'], 'speaking_id' => $truth['id'], 'score' => 2, 'create_time' => time()));
				}

			}
			$this -> assign("detail", $truth);

			$agreeCount = D('SpeakingAgree') -> where(array("speaking_id" => $id)) -> count();
			$this -> assign('agreeCount', $agreeCount);

			$commentCount = D('SpeakingComment') -> where(array("speaking_id" => $id)) -> count();
			$this -> assign('commentCount', $commentCount);

			$helpAgree = D("SpeakingAgree") -> where(array("uid" => uid(), "speaking_id" => $id)) -> find();
			if ($helpAgree) {
				$this -> assign("agreeFlag", 1);
			} else {
				$this -> assign("agreeFlag", 0);
			}

			if(!$truth){
				$this->redirect(__ROOT__."/error3.html");
			}
			$this -> display();
		}
	}

	function ajaxGetSpeakingCommentList() {
		$id = I('id');
		$lastItem = I("lastItem");
		if ($lastItem == 0) {
			$lastItem = time();
		}
		$list = D('SpeakingCommentView') -> where(array("speaking_id" => $id, "status" => 0, "create_time" => array("lt", $lastItem))) -> order("create_time desc") -> limit(10) -> select();
		ajaxMsg(0, "success", $list);
	}

	function ajaxSaveTruthComment() {
		$id = I("id");
		$commentContent = I("content");
		if (empty($commentContent) && $id > 0) {
			ajaxMsg(1, "内容不能为空");
		} else {
			$comment = array("speaking_id" => $id, "content" => $commentContent, "create_time" => time(), "uid" => uid());
			D("SpeakingComment") -> add($comment);
			ajaxMsg(0, "");
		}
	}

	function ajaxTruthAgree() {
		//        ajaxMsg(1, "该心里话已删除");
		$id = I("id");
		if ($id > 0) {
			
			$count = D("SpeakingAgree") -> where(array('speaking_id' => $id, 'uid' => uid()))->count();
			if ($count > 0) {
				D("SpeakingAgree") -> where(array('speaking_id' => $id, 'uid' => uid()))->delete();
				ajaxMsg(0, "已取消点赞");
			} else {
				$comment = array("speaking_id" => $id, "create_time" => time(), "uid" => uid());
				D("SpeakingAgree") -> add($comment);
				ajaxMsg(0, "已点赞");
			}
		} else {
			ajaxMsg(1, "该心里话已删除");
		}
	}

	function ajaxGetHelpList() {
		//        ajaxMsg(1,"eeeeee");
		$lastItem = I("lastItem");
		if ($lastItem == 0) {
			$lastItem = time();
		}
		$list = D() -> query("select user.realname as user_realname,user.headimgurl,help.title,help.content,help.id,help.create_time,
                            (select count(*) from help_comment where help_comment.help_id = help.id) AS comment_count,
                            (select count(*) from help_agree where help_agree.help_id = help.id) AS agree_count 
                            from help LEFT JOIN user ON help.uid=user.uid 
                            where (help.create_time < $lastItem and help.status = 1) order by help.create_time desc limit 10");
		//        $speakingList = D("HelpView")->where(array("status"=>0, "create_time"=>array("lt", $lastItem)))->order("create_time desc")->limit(20)->select();
		ajaxMsg(0, to_json_string($list), $list);
	}

	function ajaxUploadImg() {
		ajaxMsg(0, "success", ImageUploadUtil::uploadFormImg("help"));
	}

	function ajaxSaveHelp() {
		$truthTitle = I("title");
		$truthContent = $_POST['content'];
		if (empty($truthTitle) || empty($truthContent)) {
			ajaxMsg(1, "标题和内容都不能为空");
		} else {
			$truth = array("title" => $truthTitle, "content" => $truthContent, 'statud' => 0, "create_time" => time(), "uid" => uid());
			D("Help") -> add($truth);
			ajaxMsg(0, "");
		}
	}

	function helpinfo() {
		$id = I('id');
		//        exit("id = ".$id);
		$id = $id ? $id : I('state');
		$this -> check_wx_redirect('party_serve_helfinfo', $id);
		//判断是否重定向登录
		$user = user();

		if ($id > 0) {
			$truth = D('Help') -> find($id);
			if ($user && $truth) {
				$click_log = D('HelpClickLog') -> where(array('uid' => $user['uid'], 'help_id' => $truth['id'])) -> find();
				if (!$click_log) {
					update_user_score($user["uid"], 2, 4, '困难帮扶阅读');
					D('HelpClickLog') -> add(array('uid' => $user['uid'], 'help_id' => $truth['id'], 'score' => 2, 'create_time' => time()));
				}

			}

			$this -> assign("detail", $truth);

			$agreeCount = D('HelpAgree') -> where(array("help_id" => $id)) -> count();
			$this -> assign('agreeCount', $agreeCount);

			$commentCount = D('HelpComment') -> where(array("help_id" => $id)) -> count();
			$this -> assign('commentCount', $commentCount);

			$helpAgree = D("HelpAgree") -> where(array("uid" => uid(), "help_id" => $id)) -> find();
			if ($helpAgree) {
				$this -> assign("agreeFlag", 1);
			} else {
				$this -> assign("agreeFlag", 0);
			}

			if(!$truth){
				$this->redirect(__ROOT__."/error3.html");
			}
			$this -> display();
		}
	}

	function ajaxGetHelpCommentList() {
		$id = I('id');
		$lastItem = I("lastItem");
		if ($lastItem == 0) {
			$lastItem = time();
		}
		$list = D('HelpCommentView') -> where(array("help_id" => $id, "status" => 0, "create_time" => array("lt", $lastItem))) -> order("create_time desc") -> limit(10) -> select();
		ajaxMsg(0, "success", $list);
	}

	function ajaxSaveHelpComment() {
		$id = I("id");
		$commentContent = I("content");
		if (empty($commentContent)) {
			ajaxMsg(1, "内容不能为空");
		} else {
			$comment = array("help_id" => $id, "content" => $commentContent, "create_time" => time(), "uid" => uid());
			D("HelpComment") -> add($comment);
			ajaxMsg(0, "");
		}
	}

	function ajaxHelpAgree() {
		$id = I("id");
		if ($id > 0) {
			$count = D("HelpAgree") -> where(array('help_id' => $id, 'uid' => uid())) -> count();
			if ($count > 0) {
				D("HelpAgree") -> where(array('help_id' => $id, 'uid' => uid())) -> delete();
				ajaxMsg(0, "已取消点赞");
			} else {
				$comment = array("help_id" => $id, "create_time" => time(), "uid" => uid());
				D("HelpAgree") -> add($comment);
				ajaxMsg(0, "已点赞");
			}

		} else {
			ajaxMsg(1, "该求助已删除");
		}
	}

	/******************捐款 start*****************/
	public function donation() {
		$this -> check_wx_redirect("service_donation");
		$this -> setHeader('扶贫捐款');
		$this -> setTitle('扶贫捐款');
		$this -> display();
	}

	public function donationPay() {
		$id = I('id');
		$id = $id ? $id : I('state');
		$this -> check_wx_redirect('service_donationPay', $id);
		//判断是否重定向登录
		$detail = D('Donation') -> find($id);
		$this -> assign('detail', $detail);
		$this -> setHeader('扶贫捐款');
		$this -> setTitle('扶贫捐款');
		$this -> display();
	}

	public function ajaxDonation() {
		$id = I('id');
		$this -> check_wx_redirect();
		//判断是否重定向登录
		$remark = I('remark');
		$fee = I('fee');
		$detail = D('Donation') -> find($id);

		$fee = intval($fee * 100);
		$fee = $fee / 100;
		if ($fee <= 0) {
			ajaxMsg(1, "请先填写你的捐款金额");
		}
		$total = D('DonationLogView') -> where(array('DonationLog.donation_id' => $id, 'PayOrder.status' => 1)) -> sum('DonationLog.fee');
		//        $total = $total?$total:0;
		//        if($total == $detail['target']){
		//            ajaxMsg(1,"已达到目标捐款金额了");
		//        }
		//        if($fee + $total > $detail['target']){
		//            ajaxMsg(1,"你的捐款金额已超出目标捐款金额了");
		//        }

		if ($detail['status'] == 1) {
			$order = PayUtils::make_order(uid(), $fee, $detail['title'], "扶贫捐款");
			if ($order) {
				$array = array('donation_id' => $detail['id'], 'order_id' => $order['id'], 'uid' => uid(), 'create_time' => time(), 'remark' => $remark, 'fee' => $fee);
				$id = D('DonationLog') -> add($array);
				PayUtils::order_save_resource($order['id'], 'DONATION', $id);
				ajaxMsg(0, "", array('out_trade_no' => $order['out_trade_no']));
			} else {
				ajaxMsg(1, "生成订单失败");
			}

		} else {
			ajaxMsg(1, "抱歉，改募捐项目已经关闭收款了");
		}
	}

	public function ajaxLoadingDonation() {
		// 获取分页参数(上一次的最后一条数据的排序字段)
		$published_at = I('lastItem');
		if ($published_at == 0 || $published_at == null) {
			$published_at = time();
		}

		$where = array('Donation.status' => array('gt', 0), 'Donation.create_time' => array('lt', $published_at));
		$page = D('DonationView') -> findPage($where, 15, 'Donation.create_time desc');

		foreach ($page['list'] as $index => $item) {
			$page['list'][$index]['help_count'] = D('DonationLogView') -> where(array('DonationLog.donation_id' => $item['id'], 'PayOrder.status' => 1)) -> count();
		}

		ajaxMsg(0, "", $page['list']);
	}

	public function donationDetail() {
		$id = I('get.id');
		$id = $id ? $id : I('state');
		$this -> check_wx_redirect('service_donationDetail', $id);
		//判断是否重定向登录
		$user = user();
		$donation = D('DonationView') -> where("Donation.id=$id") -> find();
		if ($user && $donation) {
			$click_log = D('DonationClickLog') -> where(array('uid' => $user['uid'], 'donation_id' => $donation['id'])) -> find();
			if (!$click_log) {
				update_user_score($user["uid"], 2, 4, '扶贫捐款阅读');
				D('DonationClickLog') -> add(array('uid' => $user['uid'], 'donation_id' => $donation['id'], 'score' => 2, 'create_time' => time()));
			}

		}

		$real_money = D('DonationLogView') -> where(array('DonationLog.donation_id' => $id, 'PayOrder.status' => 1)) -> sum('DonationLog.fee');
		$help_count = D('DonationLogView') -> where(array('DonationLog.donation_id' => $id, 'PayOrder.status' => 1)) -> count();
		$real_money = $real_money ? $real_money : 0;
		$donation['real_money'] = $real_money;
		$donation['help_count'] = $help_count;

		$this -> assign('detail', $donation);
		if ($donation['target'] > 0) {
			$progress = $donation['real_money'] / $donation['target'] * 100;
		} else {
			$progress = 100;
		}

		$this -> assign('width', $progress . "%");

		$this -> setHeader('扶贫捐款');
		$this -> setTitle('扶贫捐款');

		if(!$donation){
			$this->redirect(__ROOT__."/error3.html");
		}
		$this -> display();
	}

	public function ajaxLoadingDonationLog() {
		// 获取分页参数(上一次的最后一条数据的排序字段)
		$published_at = I('lastItem');
		if ($published_at == 0 || $published_at == null) {
			$published_at = time();
		}
		$donationId = I('id');

		$where = array('DonationLog.donation_id' => $donationId, 'PayOrder.status' => 1, 'DonationLog.create_time' => array('lt', $published_at));
		$page = D('DonationLogView') -> findPage($where, 20, 'DonationLog.create_time desc');

		ajaxMsg(0, to_json_string($page['list']), $page['list']);
	}

	/******************捐款 end*****************/

	public function ajaxIntegral() {
		$uid = I('uid') > 0 ? I('uid') : uid();
		$startTime = $_POST['startTime'];
		$endTime = $_POST['endTime'];

		if ($startTime > 0) {
			$this -> assign('startTime', $startTime);
			$startTime = strtotime($startTime . ' 00:00:00');
		} else {
			$startTime = 0;
		}
		if ($endTime > 0) {
			$this -> assign('endTime', $endTime);
			$endTime = strtotime($endTime . ' 23:59:59');
		} else {
			$endTime = time();
		}
		//        ajaxMsg(1,"startTime = ".$startTime."    endTime = ".$endTime);
		$userScoreTypes = D('UserScoreType') -> select();
		$sumScore = D('UserScoreLog') -> where(array('uid' => $uid, 'create_time' => array( array('gt', $startTime), array('lt', $endTime)))) -> sum('score');
		//        ajaxMsg(1,"sumScore = ".$sumScore);
		//        $userScoreList = array();
		if ($sumScore == null) {
			$sumScore = 0;
			foreach ($userScoreTypes as $item) {
				$temp['name'] = $item['name'] . "积分";
				$temp['score'] = 0;
				$temp['percent'] = '0%';
				$userScoreList[] = $temp;
			}
		} else {
			foreach ($userScoreTypes as $item) {
				$temp['name'] = $item['name'] . "积分";
				$temp['score'] = D('UserScoreLog') -> where(array('uid' => $uid, 'type' => $item['id'], 'create_time' => array( array('gt', $startTime), array('lt', $endTime)))) -> sum('score');
				$temp['score'] = $temp['score'] == null ? 0 : $temp['score'];
				$userScoreList[] = $temp;
			}
			$percentSumBase = 0;
			// 求绝对值的总和来算百分比
			foreach ($userScoreList as $userScore) {
				if ($userScore['score'] > 0) {
					$percentSumBase += $userScore['score'];
				} elseif ($userScore['score'] < 0) {
					$percentSumBase -= $userScore['score'];
				}
			}
			foreach ($userScoreList as $index => $userScore) {
				if ($userScore['score'] == 0 || $userScore['score'] == null || $userScore['score'] == "") {
					$userScoreList[$index]['score'] = 0;
					$userScoreList[$index]['percent'] = "0%";
				} else {
					$userScoreList[$index]['percent'] = round($userScore['score'] * 100 / $percentSumBase) . "%";
				}
			}
		}

		//六个打通数据
		$user = user();
		$info = M('user_system_score')->where(array('user_name'=>$user['realname']))->find();
		$branch = M('user_system_score_branch')->where(array('branch_id'=>$user['branch_id']))->find();
		$sixScoreList['performance_score'] = $branch['performance_score'];
		$sixScoreList['work_innovation_score'] = $branch['work_innovation_score'];
		$sixScoreList['discipline_score'] = $branch['discipline_score'];
		$sixScoreList['risk_score'] = $branch['risk_score'];
		$sixScoreList['strength_score'] = $branch['strength_score'];
		$sixScoreList['learn_innovation_score'] = $branch['learn_innovation_score'];
		$sixScoreList['personnel_score'] = $info['personnel_score'];
		$sixScoreList['dedication_score'] = $info['dedication_score'];
		$sixScoreList['intelligent_score'] = $branch['intelligent_score'];
		$sixScoreList['score'] = $user['score'];
		//personnel_score,dedication_score performance_score,work_innovation_score,discipline_score,risk_score,strength_score,learn_innovation_score
		$responseData = array('userScoreList' => $userScoreList, 'sumScore' => $sumScore,'sixScoreList'=>$sixScoreList);
		ajaxMsg(0, to_json_string($responseData), $responseData);
	}
	public function supportPoor()
	{
		# code...
		
		$this->display();
	}
	public function integral() {
		// $user = user();
		// var_dump($user);
		//        $uid = uid();
		//        $startTime = $_POST['startTime'];
		//        $endTime = $_POST['endTime'];
		//
		//        if($startTime > 0){
		//            $this->assign('startTime', $startTime);
		//            $startTime = strtotime($startTime.' 00:00:00');
		//        }else{
		//            $startTime = 0;
		//        }
		//        if($endTime > 0){
		//            $this->assign('endTime', $endTime);
		//            $endTime = strtotime($endTime.' 23:59:59');
		//        }else{
		//            $endTime = time();
		//        }
		////        exit("startTime = ".$startTime."    endTime = ".$endTime);
		//        $userScoreTypes = D('UserScoreType')->select();
		//        $sumScore = D('UserScoreLog')->where(array('uid'=>$uid, 'create_time'=>array('gt', $startTime), 'create_time'=>array('lt', $endTime)))->sum('score');
		////        $userScoreList = array();
		//        if($sumScore == null){
		//            $sumScore = 0;
		//            foreach ($userScoreTypes as  $item){
		//                $temp['name'] = $item['name'] . "积分";
		//                $temp['score'] = 0;
		//                $temp['percent'] = '0%';
		//            }
		//        }else{
		//            foreach ($userScoreTypes as  $item){
		//                $temp['name'] = $item['name'] . "积分";
		//                $temp['score'] = D('UserScoreLog')->where(array('uid'=>$uid, 'type'=>$item['id'], 'create_time'=>array('gt', $startTime), 'create_time'=>array('lt', $endTime)))->sum('score');
		//                $userScoreList[] = $temp;
		//            }
		//            $percentSumBase = 0;
		//            // 求绝对值的总和来算百分比
		//            foreach ($userScoreList as $userScore){
		//                if($userScore['score'] > 0){
		//                    $percentSumBase += $userScore['score'];
		//                }elseif ($userScore['score'] < 0){

		//                    $percentSumBase -= $userScore['score'];
		//                }
		//            }
		//            foreach ($userScoreList as $index=>$userScore){
		//                if($userScore['score'] == 0 || $userScore['score'] == null || $userScore['score'] == ""){
		//                    $userScoreList[$index]['score'] = 0;
		//                    $userScoreList[$index]['percent'] = "0%";
		//                }else{
		//                    $userScoreList[$index]['percent'] = round($userScore['score'] * 100 / $percentSumBase)."%";
		//                }
		//            }
		//        }
		//
		//        $this->assign('sumScore', $sumScore);
		//        $this->assign('userScoreList', $userScoreList);
	
		$uid = I('uid') > 0 ? I('uid') : uid();
		if($uid){
			$user = M('user')->where(array('uid'=>$uid))->find();
			$this->assign('user',$user);
		}
		$this->assign('uid',$uid);
		$this -> display();
	}

}
