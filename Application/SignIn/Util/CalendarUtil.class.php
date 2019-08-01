<?php
namespace SignIn\Util;
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/12/5
 * Time: 21:04
 */
class CalendarUtil{
    //上一个月信息
    private $lastYear=null;
    private $lastMonth=null;
    //当前月信息
    private $curYear=null;
    private $curMonth=null;
    private $curWek=null;
    private $curDay=null;
    private $curDaySum=0;
    //下一个月月信息
    private $nextYear=null; //下一个月是哪一年
    private $nextMonth=null;//下一个月

    private $calendar=null;

    public function __construct($dateTime=null){
        if(isset($_get['yeal']) && is_numeric($_get['yeal'])){
            $this->curYear=$_get['yeal'];
        }else{
            $this->curYear=date('Y');
        }
        if(isset($_get['month']) && is_numeric($_get['month'])){
            $this->curMonth=$_get['month'];
        }else{
            $this->curMonth=date('n');
        }
        if(isset($_get['day']) && is_numeric($_get['day'])){
            $this->curDay=$_get['day'];
        }else{
            $this->curDay=date('j');
        }

        $this->init($dateTime);
        $this->createCalendar();
    }
    /**
     *初始化
     */
    public function init($dateTime=null){
        if(!empty($dateTime)){ //当月
            $this->curYear=date('Y',strtotime($dateTime));
            $this->curMonth=date('n',strtotime($dateTime));
            $this->curDay=date('j',strtotime($dateTime));
        }
        $this->curWek=date('w',strtotime($this->curYear.'-'.$this->curMonth.'-1'));
        //上一个月
        $this->lastMonth=$this->curMonth-1; //上一个月
        $this->lastYear=$this->curYear; //上一个月属于哪一年
        if($this->lastMonth<0){
            $this->lastMonth=12;
            $this->lastYear-=1;
        }
        //下一个月
        $this->nextMonth=$this->curMonth+1;//下一个月
        $this->nextYear=$this->curYear; //下一个月属于哪一年
        if($this->nextMonth > 12){
            $this->nextMonth=1;
            $this->nextYear+=1;
        }
    }
    public function getCalendar(){
        return $this->calendar;
    }
    /**
     *创建日历从周日 周一 周二 周三 周四 周五 周六，7*5方格,前面补上月后几天，后面补下月开始几天
     **/
    public function createCalendar(){
        //判断当月共计多少天
        $nextStr=$this->nextYear.'-'.$this->nextMonth.'-1 -1 days';
        $curDaySum=date('j',strtotime($nextStr));
        //判断上一个月最后一天是多少号
        $lastStr=$this->curYear.'-'.$this->curMonth.'-1 -1 days';
        $lastDaySum=date('j',strtotime($lastStr));
        $prefixLId=$this->lastYear.'-'.fill_zero_before($this->lastMonth);
        $prefixCId=$this->curYear.'-'.fill_zero_before($this->curMonth);
        $prefixNId=$this->nextYear.'-'.fill_zero_before($this->nextMonth);

        if($this->curWek == 0){
            $lastMonthSum=7; //需要添加上个月的$lastMonthSum天
        }else{
            $lastMonthSum=$this->curWek;
        }
        $lastMonthStart=$lastDaySum - $lastMonthSum+1;

        for($i=0,$j=1,$k=1;$i<42;$i++){
            $dateInfo=array();
            $temp = [];
            if($i<$lastMonthSum){ //上一个月
                $dateInfo['day']=$lastMonthStart + $i;
                $dateInfo['type']=1;
                $id=$prefixLId.'-'.fill_zero_before($dateInfo['day']);
                $this->calendar[]=array('id'=>$id, 'info'=>$dateInfo);
            }else if($j > $curDaySum){//下一个月
                $id=$prefixNId.'-'.fill_zero_before($k);
                $dateInfo['day']=$k;
                $dateInfo['type']=3;
                $this->calendar[]=array('id'=>$id, 'info'=>$dateInfo);
                $k++;
            }else{//本月
                $dateInfo['day']=$j;
                $dateInfo['type']=2;
                $dayTemp = $prefixCId.'-'.fill_zero_before($j);
                /******************** add by linjiahuan begin ********************/
                $dayClass='will-sign-cell';  // 日历表中 未来天 的方格样式
                if($this->dayPass($dayTemp)){
                    $dayClass='no-sign-cell'; // 日历表中 过去天没签到 的方格的样式
                }
                /******************** add by linjiahuan end ********************/
                $this->calendar[]=array('id'=>$dayTemp, 'info'=>$dateInfo, 'dayClass'=>$dayClass);
                $j++;
                $this->curDaySum+=1;
            }
        }
    }

    public function dayPass($dayTemp){
        return strtotime(date('Y-m-d')) > strtotime($dayTemp);
    }

    public function getDayTime(){
        return $this->curYear.'-'.$this->curMonth.'-'.$this->curDay;
    }
    /**
     *获取当前月属于哪个月
     **/
    public function getCurMonth(){
        return $this->curYear.'-'.$this->curMonth;
    }
    /**
     *获取上一个月属于哪个月
     **/
    public function getLastMonth(){
        return $this->lastYear.'-'.$this->lastMonth;
    }
    /**
     *获取下一个月属于哪个月
     **/
    public function getNextMonth(){
        return $this->nextYear.'-'.$this->nextMonth;
    }
    /**
     *判断当前月有多少天
     **/
    public function getCurDaySum(){
        return $this->curDaySum;
    }
}