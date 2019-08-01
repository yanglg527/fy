<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Mob\Controller;
use Common\Controller\BaseAuthController;
use Mob\Util\CalendarUtil;

class LotteryController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
    }

    function sign_in(){
        // 新建一个日历工具对象
        $calendarUtil = new CalendarUtil();
        $dayList = $calendarUtil->getCalendar();   // 获得本月日历表
        $this->assign('list',$dayList);           // 下传本月日历表
        $yearMonth = $calendarUtil->getCurMonth(); // 获得当前年月
        $this->assign('yearMonth',$yearMonth);   // 下传当前年月

        // 获取用户签到抽奖情况
        $signInPrize = $this->_getSignInPrize();
//        exit("signInPrize = " . to_json_string($signInPrize));
        if($signInPrize){
            $lastSignDate = date('Y-m-d',$signInPrize['last_sign_in_time']); // 最近一次签到日期
            $continueSignInCount = $signInPrize['continue_sign_in_count'];   // 连续签到次数
            if(isToday($lastSignDate)){
                $this->assign('signState',true);     // 下传今日签到状态
                $this->assign('continueSignInCount',$continueSignInCount);  // 下传连续签到次数
            }else{
                $this->assign('signState',false);     // 下传今日签到状态
                $yesterday = date("Y-m-d",strtotime("-1 day")); // 获得昨天
//                exit("yesterday = ".$yesterday);
                if(isSameDay($yesterday,$lastSignDate)){
                    $this->assign('continueSignInCount',$continueSignInCount);  // 下传连续签到次数
                }else{
//                    exit("b");
                    $signInPrize['continue_sign_in_count'] = 0;
                    D('SignInPrize')->save($signInPrize);
                    $this->assign('continueSignInCount',0);     // 下传连续签到次数
                }
            }
        }else{
            $this->assign('signState',false);     // 下传今日签到状态
            $this->assign('continueSignInCount',0);     // 下传连续签到次数
        }

        // 获取用户本月的签到记录
        $cur = date('Y-m',time());//当天年月
        $cur_y = date('Y',time());//当天年份
        $cur_m = date('m',time());//当天月份
        $cur_f = $cur . '-1';//本月首日
        $first = strtotime($cur_f);//时间戳最小值，本月第一天时间戳
        //下月首日
        if($cur_m>=12){
            $cur_n = ($cur_y+1) . '-1-1';
        }else{
            $cur_n = $cur_y . '-' .  ($cur_m+1) . '-1';
        }
        $last = strtotime($cur_n);//时间戳最大值，下个月第一天时间戳

        $signInLogList = D(SignInLog)->where("uid = " . uid() . " and create_time >= " . $first . " and create_time <= " . $last)->select();
        foreach($signInLogList as $value ){  // 遍历签到记录，转成键值对格式
            $signStateList[date("Y-m-d", $value['create_time'])."class"] = "star";
        }
        $this->assign('signStateList',$signStateList);  // 下传本月签到记录

        $this->display();
    }

    function ajaxSignIn(){
        // 获取用户签到抽奖情况
        $signInPrize = $this->_getSignInPrize();
        // 处理签到业务
        if($signInPrize && isToday(date('Y-m-d',$signInPrize['last_sign_in_time']))){ // 已签到
            ajaxMsg(1, '您签到过了，明天再来吧...');
        }else{ // 未签到
            // 更新最近一次签到时间,送抽奖机会
            $signInPrize = $this->_updateSignIn($signInPrize);
//            ajaxMsg(1,"date = ".date('Y-m-d',$signInPrize['last_sign_in_time']));
            // 更新用户积分并生成积分获得记录， 必须在更新连续签到次数之前
            $rewardScore = 4 + $signInPrize['continue_sign_in_count'];
//            $score = update_score($rewardScore, "SIGN_IN");
            $score = update_score($rewardScore, 7, "签到");
            // 生成签到记录
            $this->_createSignInLog();
            $data['id'] = date('Y-m-d');
            $data['continue_sign_in_count'] = $signInPrize['continue_sign_in_count'];
            $data['score'] = $score;
            $data['prizeChanceCount'] = $signInPrize[prize_chance_count];
            ajaxMsg(0, '签到成功，奖励'.$rewardScore.'积分', $data);
        }
    }

    function lottery(){
        $this->display();
    }

    public function ajaxGetPrize(){
        $index = I("index");
//        ajaxMsg(1,"index = $index");
        // 获得用户签到抽奖情况记录
        $signInPrize = $this->_getSignInPrize();
        // 处理抽奖业务
        if($signInPrize && $signInPrize['prize_chance_count'] > 0){
            // 更新剩余抽奖机会次数
            $signInPrize['prize_chance_count'] -= 1;
            D('SignInPrize')->where(array("uid"=>$signInPrize['uid']))->save($signInPrize);
            // 获得抽奖配置
            $prizeConfigList = D('SignInPrizeConfig')->order('code asc ')->select();
            // 随机获得奖品
            $winPrizeCell = $this->_getWinPrizeCell($prizeConfigList);
            // 更新最近一次抽奖时间
            $signInPrize = $this->_updateLastPrizeTime($signInPrize);
            // 生成抽奖记录
            $this->_createSignInPrizeLog($winPrizeCell['point']);
            // 更新用户积分并生成积分获得记录
//            update_score($winPrizeCell['point'], "PRIZE");
            update_score($winPrizeCell['point'], 7, "抽奖");
            // 计算用户积分排名百分比
            $user = user();
            $rankingPercent = $this->_getRankingPercent($user);
            // 生成抽奖结果并下传
            $data['prizeConfigList'] = $prizeConfigList;
            $data['prizeChanceCount'] = $signInPrize['prize_chance_count'];
            $data['rankingPercent'] = $rankingPercent;
            $data['userScore'] = $user['score'];
            $data['prize'] = $winPrizeCell;

            if($winPrizeCell['point'] > 0){
                $tip = '恭喜抽中'.$winPrizeCell['point'] .'积分';
            }else{
                $tip = '谢谢参与';
            }
            ajaxMsg(0, $tip, $data);
        }else{ // 没有抽奖机会
            ajaxMsg(1, '很遗憾，你的抽奖机会用完了，下次再来吧...');
        }
    }

    private function _isCanPrize($signInPrize){
        if(isToday(date('Y-m-d',$signInPrize['last_sign_in_time']))){ // 今日已签到
            if(isToday(date('Y-m-d',$signInPrize['last_prize_time']))){ // 今日已抽奖
                return false;
            }else{ // 今日未抽奖
                return true;
            }
        }
        return false;
    }

    private function _getSignInPrize(){
        return D('SignInPrize')->where(array("uid"=>uid()))->find();
    }


    private function _getWinPrizeCell($prizeConfigList){
        if($prizeConfigList){
            srand($this->_seed());
            $random = rand(0,100);
            $min = 0;
            $max = 0;
            foreach($prizeConfigList as $item){
                $max += $item['probability'];
                if($random >= $min && $random < $max){
                    $item['win_count'] = $item['win_count'] + 1;
                    D('SignInPrizeConfig')->save($item);
                    return $item;
                }
                $min = $max;
            }
        }else{
            echo "数据库抽奖配置出错";
        }
    }

    private function _seed()
    {
        list($msec, $sec) = explode(' ', microtime());
        return (float) $sec;

    }

    private function _getRankingPercent($user){
        $userSum = D('User')->count();  // 用户总人数
        if($userSum > 0){
            $ranking = D('User')->where("score < " . $user['score'])->count(); // 统计积分超过多少人
            return round(($ranking+1)/$userSum ,3) * 100; // $ranking+1加的是自己。这样如果自己是最高分的话算出来才会是100%
        }
        return 100;
    }

    private function _updateLastPrizeTime($signInPrize){
        $signInPrizeD = D('SignInPrize');
        $signInPrize['last_prize_time'] = time();
        $signInPrizeD->save($signInPrize);
        return $signInPrize;
    }

    private function _createSignInPrizeLog($point){
        $signInPrizeLog['uid'] = uid();
        $signInPrizeLog['point'] = $point;
        $signInPrizeLog['create_time'] = time();
        D('SignInPrizeLog')->add($signInPrizeLog);
    }

    private  function _updateSignIn($signInPrize){
//        echo "updateSignIn(" . to_json_string($signInPrize) . ")";
        $signInPrizeD = D('SignInPrize');
        if($signInPrize){ // 非首次签到
            $signInPrize['sign_in_sum'] = $signInPrize['sign_in_sum'] +  1;
            if($this->_isContinueSignIn(date('Y-m-d',$signInPrize['last_sign_in_time']))){
                // 累计连续签到次数
                $signInPrize['continue_sign_in_count'] = $signInPrize['continue_sign_in_count'] + 1;
            }else{
                // 复位连续签到次数
                $signInPrize['continue_sign_in_count'] = 1;
            }
            $signInPrize['last_sign_in_time'] = time();

            // 签到奖励抽奖机会；连续第30的倍数次签到增加奖励一次抽奖机会
            $signInPrize['prize_chance_count'] += 1;
            if($signInPrize['continue_sign_in_count'] % 30 == 0){
                $signInPrize['prize_chance_count'] += 1;
            }

            $signInPrizeD->where(array("id"=>$signInPrize['id']))->save($signInPrize);
        }else{ // 首次签到
            $signInPrize = array();
            $signInPrize['uid'] = uid();
            $signInPrize['sign_in_sum'] = 1;
            $signInPrize['continue_sign_in_count'] = 1;
            $signInPrize['last_sign_in_time'] = time();
            $signInPrize['create_time'] = time();
            $signInPrize['prize_chance_count'] = 1;
            $signInPrizeD->add($signInPrize);
        }

        return $signInPrize;
    }

    private function _isContinueSignIn($lastSignInDate){
        $yesterday = date("Y-m-d",strtotime("-1 day")); // 获得昨天
        if(isSameDay($yesterday,$lastSignInDate)){
            return true;
        }else{
            return false;
        }
    }

    private  function _createSignInLog(){
        $signInLogD = D('SignInLog');
        $signInLog['uid'] = uid();
        $signInLog['create_time'] = time();
        $signInLogD->add($signInLog);
    }




    public function convert(){
        $uid = uid();
        $now = time();
        $listHuaFei = D('Prize')->where(array("type"=>1, "status"=>1, "start_time"=>array("lt", $now), "end_time"=>array("gt", $now)))->order("create_time desc")->select();
        $listLiuLiang = D('Prize')->where(array("type"=>2, "status"=>1, "start_time"=>array("lt", $now), "end_time"=>array("gt", $now)))->order("create_time desc")->select();
        $listDianYing = D('Prize')->where(array("type"=>3, "status"=>1, "start_time"=>array("lt", $now), "end_time"=>array("gt", $now)))->order("create_time desc")->select();
//        exit("a = ".to_json_string($listHuaFei));
        $this->assign("listHuaFei", $listHuaFei);
        $this->assign("listLiuLiang", $listLiuLiang);
        $this->assign("listDianYing", $listDianYing);

        $listConvertLog = D('PrizeConvertLogView')->where(array("uid"=>$uid))->order("create_time desc")->select();
        if(count($listConvertLog)){
            $this->assign("listConvertLog", $listConvertLog);
            $this->assign("isShowLog", true);
        }else{
            $this->assign("isShowLog", false);
        }

        $this->display();
    }

    public function ajaxConvertPrize(){
        $now = time();
        $id = I('id');
        $user = user();
        $prize = D('Prize')->where(array("status"=>1, "start_time"=>array("lt", $now), "end_time"=>array("gt", $now), "id"=>$id))->find();
        if($prize){
            if($user['cost_score'] >= $prize['cost_score']){
                update_cost_score(0-$prize['cost_score'], $prize['type']);
                $convertLog = array("uid"=>uid(), "prize_id"=>$prize['id'], "create_time"=>time());
                D('PrizeConvertLog')->add($convertLog);
                ajaxMsg(0, "兑换成功");
            }else{
                ajaxMsg(1, "兑换失败，积分不足");
            }
        }else{
            ajaxMsg(1, "兑换失败，此奖品已过期");
        }
    }
}