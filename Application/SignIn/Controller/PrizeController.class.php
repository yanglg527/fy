<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace SignIn\Controller;
use Common\Controller\BaseController;

class PrizeController extends BaseController {

    function _initialize(){
        parent::_initialize();
        $this->setHeader('抽奖');
        $this->setTitle('抽奖');

        $this->check_wx_redirect('signIn_prize_index');//判断是否重定向登录
    }

    public function index()
    {
        // 获得用户签到抽奖情况记录
        $signInPrize = $this->_getSignInPrize();
        $this->assign('signInPrize',$signInPrize);
        // 下传抽奖按钮状态
        $this->assign('btnPrizeState',$this->_isCanPrize($signInPrize));

        // 获取用户积分排名百分比
        $user = user();
        $this->assign('rankingPercent',$this->_getRankingPercent($user));

        // 获取抽奖转盘配置
        $prizeConfigList = D('SignInPrizeConfig')->order('code asc ')->select();
        $this->assign('prizeConfigList',$prizeConfigList);

        // 设置导航栏左上角功能,跳转回签到界面，触发签到界面刷新数据
        $header_left['url']=__ROOT__.'/SignIn/index';
        $this->assign('header_left',$header_left);

        $header_left['url'] = __ROOT__.'/Home/index';
        $this->assign('header_left',$header_left);

        $this->setHeader('抽奖');
        $this->setTitle('抽奖');
        $this->display();
    }

    // 备份 翻牌版抽奖
//    public function ajaxGetPrize(){
//        $index = I("index");
//        // 获得用户签到抽奖情况记录
//        $signInPrize = $this->_getSignInPrize();
//        // 处理抽奖业务
//        if($signInPrize && $signInPrize['prize_chance_count'] > 0){
//            // 更新剩余抽奖机会次数
//            $signInPrize['prize_chance_count'] -= 1;
//            D('SignInPrize')->save($signInPrize);
//            // 获得卡牌积分值数组
//            $winPrizeCell = $this->_getWinPrizeCell($index);
////            ajaxMsg(1, to_json_string($winPrizeCell));
//            // 更新最近一次抽奖时间
//            $signInPrize = $this->_updateLastPrizeTime($signInPrize);
//            // 生成抽奖记录
//            $this->_createSignInPrizeLog($winPrizeCell[$index]['point']);
//            // 更新用户积分并生成积分获得记录
//            update_score($winPrizeCell[$index]['point'], "PRIZE");
//            // 计算用户积分排名百分比
//            $user = user();
//            $rankingPercent = $this->_getRankingPercent($user);
//            // 生成抽奖结果并下传
//            $data['prizeChanceCount'] = $signInPrize['prize_chance_count'];
//            $data['rankingPercent'] = $rankingPercent;
//            $data['userScore'] = $user['score'];
//            $data['prize'] = $winPrizeCell;
//
//            if($winPrizeCell[$index]['point'] > 0){
//                $tip = '恭喜抽中'.$winPrizeCell[$index]['point'] .'积分';
//            }else{
//                $tip = '谢谢参与';
//            }
//            ajaxMsg(0, $tip, $data);
//        }else{ // 没有抽奖机会
//            ajaxMsg(1, '抽奖失败');
//        }
//    }

    /**
     * 备份旧版转盘逻辑，换成翻牌抽奖
     */
    public function ajaxGetPrize(){
//        ajaxMsg(1,"aaaaaaaaaaaaaaaaaaaaaaa");
        // 获得用户签到抽奖情况记录
        $signInPrize = $this->_getSignInPrize();
        // 处理抽奖业务
//        if($this->_isCanPrize($signInPrize)){ // 拥有抽奖机会
        if($signInPrize && $signInPrize['prize_chance_count'] > 0){
            // 更新剩余抽奖机会次数
            $signInPrize['prize_chance_count'] -= 1;
            D('SignInPrize')->save($signInPrize);
            // 随机获得奖品
            $winPrizeCell = $this->_getWinPrizeCell();
            // 更新最近一次抽奖时间
            $signInPrize = $this->_updateLastPrizeTime($signInPrize);
            // 生成抽奖记录
            $this->_createSignInPrizeLog($winPrizeCell['point']);
            // 更新用户积分并生成积分获得记录
            update_score($winPrizeCell['point'], "PRIZE");
            // 计算用户积分排名百分比
            $user = user();
            $rankingPercent = $this->_getRankingPercent($user);
            // 生成抽奖结果并下传
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
            ajaxMsg(1, '抽奖失败');
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
        return D('SignInPrize')->find(uid());
    }

    // 备份  新版翻牌调用
//    private function _getWinPrizeCell($targetIndex){
//        $prizeConfigList = D('SignInPrizeConfig')->order('code asc ')->select();
//        $prizeConfigListTemp = $prizeConfigList;
//        if($prizeConfigList){
//            srand($this->_seed());
//            $random = rand(0,100);
//            $min = 0;
//            $max = 0;
//            foreach($prizeConfigList as $index => $item){
//                $max += $item['probability'];
//                if($random >= $min && $random < $max){
//                    $item['win_count'] = $item['win_count'] + 1;
//                    D('SignInPrizeConfig')->save($item);
//
//                    shuffle($prizeConfigListTemp);
//                    $temp = $prizeConfigListTemp[$targetIndex];
//                    $prizeConfigListTemp[$targetIndex] = $item;
//                    $prizeConfigListTemp[$index] = $temp;
//                    return $prizeConfigListTemp;
//                }
//                $min = $max;
//            }
//        }else{
//            echo "数据库抽奖配置出错";
//        }
//    }

// 备份 旧版 转盘抽奖调用
    private function _getWinPrizeCell(){
        $prizeConfigList = D('SignInPrizeConfig')->order('code asc ')->select();
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
}