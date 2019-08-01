<?php

/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Mob\Controller;

use Common\Controller\BaseAuthController;
use Common\Util\CalendarUtil;

class PartyWorkController extends BaseAuthController
{

    function _initialize()
    {
        parent::_initialize();
    }


    public function partyWork()
    {
        $this->check_wx_redirect('party_work');//判断是否重定向登录

        $this->display();
    }

    public function partySpace()
    {
        $this->check_wx_redirect('party_space');//判断是否重定向登录

        $this->display();
    }
    public function test(Type $var = null)
    {
        # code...
        echo date('Y-m-d',1523257130);
    }
    public function affairsView()
    {
        $user = user();
        $group_id = $user['group_id'];
        //筛选是三会一课还是民主生活会，还是中心组学习dengdeng 1~4
        $type = I('type', 1);
        $head_title = '三会一课';
        //用于判断是支部堡垒还是党组核心
        $type_id = '';
        switch ($type) {
            case 1:
                $head_title = '三会一课';
                $type_id = 4;
                break;
            case 2:
                $head_title = '民主生活会';
                $type_id = 2;
                break;
            case 3:
                $head_title = '中心组学习';
                $type_id = 2;
                break;
            case 4:
                $head_title = '谈心谈话';
                $type_id = 4;
                break;
            case 5:
                $head_title = '组织生活会';
                $type_id = 4;
                break;
        }
        $list = [];
        //读取数据统计 月份
        $currentMonth = (int)date('n');
        $year = date("Y");
          //统计年累计时长
        $yearTime = "";
        for ($i = $currentMonth; $i > 0; $i--) {
  //            $startDate = $y."-".$i."-1";
  //            $endDate = date('Y-m-d', strtotime("$startDate +1 month 0 day"));
  //            $startDate = strtotime($startDate);
  //            $endDate = strtotime($endDate);
  //                        echo "startTime = ".$startDate."  endTime = ".$endDate;
  //            $sql = "select count(*) as note_count from notes where status > -1 and uid = $uid and create_time >= $startDate and create_time < $endDate";

            $monthTime = strtotime($year. "-" . $i . "-1");

            $sql = "select count(*) as tz_count from taizhang where affairs_id = " . $type . "  and month(FROM_UNIXTIME(create_time)) = month(FROM_UNIXTIME($monthTime)) and year(FROM_UNIXTIME(create_time)) = year(curdate());";    
           
               
              // file_put_contents('./year.txt',$sql);
            $numNote = D()->query($sql);
           
  //            echo  "aaaa = ".to_json_string($numNote);
            $temp = array(
                'month' => $i,
                'tz_count' => $numNote[0]['tz_count'] == null ? 0 : $numNote[0]['tz_count'],

            );
            //   $yearTime = $yearTime + $numNote[0]['sum_time'];

            array_push($list, $temp);
        }
        $this->assign('group_id',$group_id);
        $this->assign('type_id',$type_id);
        $this->assign('year', $year);
        $this->assign('list', $list);
        $this->assign('head_title', $head_title);
        $this->display();
    }
}