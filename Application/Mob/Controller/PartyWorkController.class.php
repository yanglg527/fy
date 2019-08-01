<?php

/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Mob\Controller;
use Common\Controller\BaseController;
use Common\Util\ImageUploadUtil;
use Think\Controller;
use Weixin\Util\QYConfig;
use Admin\Util\AdminUtil;
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

    public function partyPay()
    {
        $this->check_wx_redirect('party_pay');//判断是否重定向登录
        $user = user();
        //根据user表 拿支部名 党小组名
        $party = D('User')->where(array('uid'=>$user['uid']))->field('party_group_id,branch_id')->find();
        if($party['branch_id']){
            $branch_name= D('PartyBranch')->where(array('id'=>$party['branch_id']))->field('name')->find();
        }
        if($party['party_group_id']){
           $group_name= D('PartyGroup')->where(array('id'=>$party['party_group_id']))->field('name')->find();
        }
        $party['branch_name'] = $branch_name['name'];
        $party['group_name'] = $group_name['name'];
        //查询该用户 正常有效 缴纳总金额
        $party['total'] = D('PartyMembershipDues')->where(array('uid'=>$user['uid'],'status'=>1))->sum('money');
        $party['total'] = number_format($party['total'],2);//保留两位小数

        //获取最近十年年份
        $currentYear = date('Y');
        for ($i=0; $i<12; $i++)
        {
            $years[$i] = $currentYear - $i;
        }

        //初始化数据
        //获取12个月
        $currentMonth = 12;
        for ($i=0; $i<12; $i++)
        {
            $month[$i] = $currentMonth - $i;
        }
        sort($month);//倒序
        //循环查询每月缴费金额
        foreach($month as $key=>$item){
            $money = D('PartyMembershipDues')->where(array('year'=>$currentYear,'month'=>$item,'uid'=>$user['uid']))->sum('money');
            if(!$money){
                $SumMonth[$key] = number_format(0,2);//保留两位小数
            }else {
                $month_money = D('PartyMembershipDues')->where(array('year' => $currentYear, 'month' => $item, 'uid' => $user['uid']))->sum('money');
                $SumMonth[$key] = number_format($month_money,2);//保留两位小数
            }
        }
        //今年缴费总金额
        $currentyear_money = number_format (D('PartyMembershipDues')->where(array('year'=>$currentYear,'uid'=>$user['uid']))->sum('money'),2);
        $current_month = intval(date('m'));
        $this->assign('current_month',$current_month);
        $this->assign('currentyear_money',$currentyear_money);
        $this->assign('currentYear',$currentYear);
        $this->assign('year',$years);
        $this->assign('party',$party);
        $this->assign('user',$user);
        $this->assign('SumMonth',$SumMonth);
        $this->display();
    }

    //根据年份获取每月缴纳总金额
    public function  ajaxGetMoney()
    {
        $user = user();
        $year = I('year');
        $year_money = D('PartyMembershipDues')->where(array('year'=>$year,'uid'=>$user['uid']))->sum('money');
        //获取12个月份
        $currentMonth = 12;

        for ($i=0; $i<12; $i++)
        {
            $month[$i] = $currentMonth - $i;
        }
        sort($month);//倒序
        foreach($month as $key=>$item){
            $money = D('PartyMembershipDues')->where(array('year'=>$year,'month'=>$item,'uid'=>$user['uid']))->sum('money');
            if(!$money){
                $SumMonth[$key+1] = number_format(0,2);//保留两位小数
            }else {
                $month_money = D('PartyMembershipDues')->where(array('year' => $year, 'month' => $item, 'uid' => $user['uid']))->sum('money');
                $SumMonth[$key + 1] = number_format($month_money,2);//保留两位小数
            }
        }
        if(!$year_money){
            $SumMonth['year_money'] = number_format(0,2);//保留两位小数
        }else{
            $SumMonth['year_money'] = number_format($year_money,2);//保留两位小数
        }

        ajaxMsg(0, 'success', $SumMonth);
    }

    /*
   *
   * $mouth 月份 数字
   *返回月初时间戳 月末时间戳
   */
    function time_mouth($year,$month)
    {
        $year = (int)$year;
        $month = (int)$month;
        $_POST['yue'] = $year."-".$month;
        $arr=explode('-', $_POST['yue']);
        $begin_time=mktime(0,0,0,$arr['1'],1,$arr['0']);
        $day=date('t',$begin_time);
        $end_time=mktime(23,59,59,$arr['1'],$day,$arr[0]);

        $data['month_start'] = $begin_time;//指定月份月初时间戳  
        $data['month_end'] = $end_time;
        return $data;
    }

    /*
    *
    * $year 年份 数字
    * 返回月年初时间戳 年末时间戳
    */
    function time_year($year)
    {
        $year = (int)$year;
        $smonth = 1;
        $emonth = 12;
        $data['startTime'] = strtotime($year.'-'.$smonth.'-1 00:00:00');
        $em = $year.'-'.$emonth.'-1 23:59:59';
        $data['endTime'] = strtotime(date('Y-m-t H:i:s',strtotime($em)));
        return $data;
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