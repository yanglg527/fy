<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
    namespace Mob\Controller;
use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;

class TrackController extends BaseAuthController {

    function _initialize(){
        parent::_initialize();
    }


    //信箱列表
    public function integral()
    {
        $this->display();
    }

    public function integral_leader()
    {
        $this->display();
    }

    public function ajaxIntegral(){
        $uid = uid();
        $user = user();
        // 获得我的积分
        $data['me'] = $user;
        // 统计个积分段的人数
        $scoreBarChart = D()->query("select
          sum(case when score >= 0 and score < 500 then 1 else 0 end) as 'score0',
          sum(case when score >= 500 and score < 1000 then 1 else 0 end) as 'score500',
          sum(case when score >= 1000 and score < 1500 then 1 else 0 end) as 'score1000',
          sum(case when score >= 1500 then 1 else 0 end) as 'score1500' from user ");
        $yData[0] = (int)$scoreBarChart[0]['score0'];
        $yData[1] = (int)$scoreBarChart[0]['score500'];
        $yData[2] = (int)$scoreBarChart[0]['score1000'];
        $yData[3] = (int)$scoreBarChart[0]['score1500'];
        $data['yDataBar'] = $yData;
        // 获得曲线图X轴日期
        $times = [
            strtotime('-28 days'),
            strtotime('-21 days'),
            strtotime('-14 days'),
            strtotime('-7 days'),
            strtotime('-7 days'),
            time()
        ];
        $xDataGraph = [
            date('m-d', $times[0]),
            date('m-d', $times[1]),
            date('m-d', $times[2]),
            date('m-d', $times[3]),
            date('m-d',$times[4]),
            date('m-d',$times[5]),
        ];
        $data['xDataGraph'] = $xDataGraph;
        $myGraph = D()->query("select
          sum(case when create_time < $times[0] then score else 0 end) as 'score0',
          sum(case when create_time < $times[1] then score else 0 end) as 'score1',
          sum(case when create_time < $times[2] then score else 0 end) as 'score2',
          sum(case when create_time < $times[3] then score else 0 end) as 'score3',
          sum(case when create_time < $times[4] then score else 0 end) as 'score4',
          sum(case when create_time < $times[5] then score else 0 end) as 'score5' from user_score_log where uid = $uid ");
        $data['myGraph'] = [
            (int)$myGraph[0]['score0'],
            (int)$myGraph[0]['score1'],
            (int)$myGraph[0]['score2'],
            (int)$myGraph[0]['score3'],
            (int)$myGraph[0]['score4'],
            (int)$myGraph[0]['score5']
        ];
        // 计算各时间节点所有用户的总分
        $sumGraph = D()->query("select
          sum(case when create_time < $times[0] then score else 0 end) as 'score0',
          sum(case when create_time < $times[1] then score else 0 end) as 'score1',
          sum(case when create_time < $times[2] then score else 0 end) as 'score2',
          sum(case when create_time < $times[3] then score else 0 end) as 'score3',
          sum(case when create_time < $times[4] then score else 0 end) as 'score4',
          sum(case when create_time < $times[5] then score else 0 end) as 'score5' from user_score_log");
        $data['sumGraph'] = [
            $sumGraph[0]['score0'],
            $sumGraph[0]['score1'],
            $sumGraph[0]['score2'],
            $sumGraph[0]['score3'],
            $sumGraph[0]['score4'],
            $sumGraph[0]['score5']
        ];
        // 用户总数
        $userCount = D('User')->count();
        $data['userCount'] = $userCount;

        // 积分排名前三
        $topThreeUsers = D()->query("select headimgurl, realname, score from user order by score desc limit 10");
        $data['topThreeUsers'] = $topThreeUsers;
        ajaxMsg(0,"data = ".to_json_string($data),$data);


    }

    public function ajaxIntegralLeader(){
        // 统计个积分段的人数
        $scoreBarChart = D()->query("select
          sum(case when score >= 0 and score < 500 then 1 else 0 end) as 'score0',
          sum(case when score >= 500 and score < 1000 then 1 else 0 end) as 'score500',
          sum(case when score >= 1000 and score < 1500 then 1 else 0 end) as 'score1000',
          sum(case when score >= 1500 then 1 else 0 end) as 'score1500' from user where is_leader = 1 ");
        $yData[0] = (int)$scoreBarChart[0]['score0'];
        $yData[1] = (int)$scoreBarChart[0]['score500'];
        $yData[2] = (int)$scoreBarChart[0]['score1000'];
        $yData[3] = (int)$scoreBarChart[0]['score1500'];
        $data['yDataBar'] = $yData;
        // 获得曲线图X轴日期
        $times = [
            strtotime('-28 days'),
            strtotime('-21 days'),
            strtotime('-14 days'),
            strtotime('-7 days'),
            strtotime('-7 days'),
            time()
        ];
        $xDataGraph = [
            date('m-d', $times[0]),
            date('m-d', $times[1]),
            date('m-d', $times[2]),
            date('m-d', $times[3]),
            date('m-d',$times[4]),
            date('m-d',$times[5]),
        ];
        $data['xDataGraph'] = $xDataGraph;
        // 获得sort最靠前的领导
        $leader = D('User')->where('is_leader = 1')->order("sort desc")->limit(1)->select();
        $leaderUid = $leader[0]['uid'];
        $leaderName = $leader[0]['uid'] == uid() ? "您本人" : $leader[0]['realname'];
        $data['leaderName'] = $leaderName;
        // 统计最靠前领导近期积分走势
        $leaderGraph = D()->query("select
          sum(case when create_time < $times[0] then score else 0 end) as 'score0',
          sum(case when create_time < $times[1] then score else 0 end) as 'score1',
          sum(case when create_time < $times[2] then score else 0 end) as 'score2',
          sum(case when create_time < $times[3] then score else 0 end) as 'score3',
          sum(case when create_time < $times[4] then score else 0 end) as 'score4',
          sum(case when create_time < $times[5] then score else 0 end) as 'score5' from user_score_log where uid = $leaderUid ");
        $data['leaderGraph'] = [
            (int)$leaderGraph[0]['score0'],
            (int)$leaderGraph[0]['score1'],
            (int)$leaderGraph[0]['score2'],
            (int)$leaderGraph[0]['score3'],
            (int)$leaderGraph[0]['score4'],
            (int)$leaderGraph[0]['score5']
        ];
        // 计算各时间节点所有用户的总分
        $sumGraph = D()->query("select
          sum(case when user_score_log.create_time < $times[0] then user_score_log.score else 0 end) as 'score0',
          sum(case when user_score_log.create_time < $times[1] then user_score_log.score else 0 end) as 'score1',
          sum(case when user_score_log.create_time < $times[2] then user_score_log.score else 0 end) as 'score2',
          sum(case when user_score_log.create_time < $times[3] then user_score_log.score else 0 end) as 'score3',
          sum(case when user_score_log.create_time < $times[4] then user_score_log.score else 0 end) as 'score4',
          sum(case when user_score_log.create_time < $times[5] then user_score_log.score else 0 end) as 'score5'
          from user_score_log left join user on user_score_log.uid = user.uid where user.is_leader = 1");
        $data['sumGraph'] = [
            $sumGraph[0]['score0'],
            $sumGraph[0]['score1'],
            $sumGraph[0]['score2'],
            $sumGraph[0]['score3'],
            $sumGraph[0]['score4'],
            $sumGraph[0]['score5']
        ];
        // 领导总数
        $leaderCount = D('User')->where('is_leader = 1')->count();
        $data['leaderCount'] = $leaderCount;
        //

        // 积分排名前三
        $topThreeUsers = D()->query("select headimgurl, realname, score from user where is_leader = 1 order by score desc limit 10");
        $data['topThreeUsers'] = $topThreeUsers;
        ajaxMsg(0,"data = ".to_json_string($data),$data);

    }
}