<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace SignIn\Controller;
use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;
use SignIn\Util\CalendarUtil;

class IndexController extends BaseController {

    function _initialize(){
        parent::_initialize();
        $this->setHeader('签到');
        $this->setTitle('签到');

        $this->check_wx_redirect('signIn_index');//判断是否重定向登录
    }

    public function signPrize(){
        // 获取用户签到抽奖情况
        $signInPrize = $this->_getSignInPrize();

        // 处理签到业务
        if($signInPrize && isToday(date('Y-m-d',$signInPrize['last_sign_in_time']))){ // 已签到
//            ajaxMsg(1, '您签到过了，明天再来吧...');
        }else{ // 未签到
            // 更新最近一次签到时间,送抽奖机会
            $signInPrize = $this->_updateSignIn($signInPrize);
            // 更新用户积分并生成积分获得记录， 必须在更新连续签到次数之前
            $rewardScore = 4 + $signInPrize['continue_sign_in_count'];
            $score = update_score($rewardScore, "SIGN_IN");
            // 生成签到记录
            $this->_createSignInLog();
            $data['id'] = date('Y-m-d');
            $data['continue_sign_in_count'] = $signInPrize['continue_sign_in_count'];
            $data['score'] = $score;
            $data['prizeChanceCount'] = $signInPrize[prize_chance_count];
//            ajaxMsg(0, '签到成功，奖励'.$rewardScore.'积分', $data);
        }

        // 下传界面信息
        if($signInPrize){
            $lastSignDate = date('Y-m-d',$signInPrize['last_sign_in_time']); // 最近一次签到日期
            $continueSignInCount = $signInPrize['continue_sign_in_count'];   // 连续签到次数
            if(isToday($lastSignDate)){
                $this->assign('signState',true);     // 下传今日签到状态
                $this->assign('continueSignInCount',$continueSignInCount);  // 下传连续签到次数
            }else{
                $this->assign('signState',false);     // 下传今日签到状态
                $yesterday = date("Y-m-d",strtotime("-1 day")); // 获得昨天
                if(isSameDay($yesterday,$lastSignDate)){
                    $this->assign('continueSignInCount',$continueSignInCount);  // 下传连续签到次数
                }else{
                    $signInPrize['continue_sign_in_count'] = 0;
                    D('SignInPrize')->save($signInPrize);
                    $this->assign('continueSignInCount',0);     // 下传连续签到次数
                }
            }
        }else{
            $this->assign('signState',false);     // 下传今日签到状态
            $this->assign('continueSignInCount',0);     // 下传连续签到次数
        }

        // 下传用户拥有的抽奖次数
        $this->assign('prizeChangeCount',$signInPrize['prize_chance_count']);

        // 获取抽奖转盘配置
        $prizeConfigList = D('SignInPrizeConfig')->order('code asc ')->select();
        $this->assign('prizeConfigList',$prizeConfigList);

        // 设置导航栏左上角功能
        $header_left['url']=__ROOT__.'/Home';
        $this->assign('header_left',$header_left);

        $this->setHeader('签到抽奖');
        $this->setTitle('签到抽奖');
        $this->display();
    }

    public function index()
    {
        // 新建一个日历工具对象
        $calendarUtil = new CalendarUtil();
        $dayList = $calendarUtil->getCalendar();   // 获得本月日历表
        $this->assign('list',$dayList);           // 下传本月日历表
        $yearMonth = $calendarUtil->getCurMonth(); // 获得当前年月
        $this->assign('yearMonth',$yearMonth);   // 下传当前年月

        // 获取用户签到抽奖情况
        $signInPrize = $this->_getSignInPrize();
        if($signInPrize){
            $lastSignDate = date('Y-m-d',$signInPrize['last_sign_in_time']); // 最近一次签到日期
            $continueSignInCount = $signInPrize['continue_sign_in_count'];   // 连续签到次数
            if(isToday($lastSignDate)){
                $this->assign('signState',true);     // 下传今日签到状态
                $this->assign('continueSignInCount',$continueSignInCount);  // 下传连续签到次数
            }else{
                $this->assign('signState',false);     // 下传今日签到状态
                $yesterday = date("Y-m-d",strtotime("-1 day")); // 获得昨天
                if(isSameDay($yesterday,$lastSignDate)){
                    $this->assign('continueSignInCount',$continueSignInCount);  // 下传连续签到次数
                }else{
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
//        exit(to_json_string($signInLogList));
        foreach($signInLogList as $value ){  // 遍历签到记录，转成键值对格式
//            exit(date("Y-m-d", $value['create_time'])."class");
            $signStateList[date("Y-m-d", $value['create_time'])."class"] = "signed-cell";
            $signStateList[date("Y-m-d", $value['create_time'])] = "签到";
        }
        $this->assign('signStateList',$signStateList);  // 下传本月签到记录

//        echo to_json_string(time());

        // 设置导航栏左上角功能
        $header_left['url']=__ROOT__.'/Home';
        $this->assign('header_left',$header_left);

        $this->setHeader('签到');
        $this->setTitle('签到');
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
            // 更新用户积分并生成积分获得记录， 必须在更新连续签到次数之前
            $rewardScore = 4 + $signInPrize['continue_sign_in_count'];
            $score = update_score($rewardScore, "SIGN_IN");
            // 生成签到记录
            $this->_createSignInLog();
            $data['id'] = date('Y-m-d');
            $data['continue_sign_in_count'] = $signInPrize['continue_sign_in_count'];
            $data['score'] = $score;
            $data['prizeChanceCount'] = $signInPrize[prize_chance_count];
            ajaxMsg(0, '签到成功，奖励'.$rewardScore.'积分', $data);
        }
    }



    private function _getSignInPrize(){
        return D('SignInPrize')->where(array('uid'=>uid()))->find();
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

            $signInPrizeD->save($signInPrize);
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

}