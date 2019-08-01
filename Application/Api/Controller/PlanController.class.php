<?php
/**
 * 是五是都放里度啦
 */
namespace Api\Controller;

use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;

class PlanController extends BaseAuthController
{

    function _initialize()
    {
        parent::_initialize();
    }

    public function index(){
        // 待办任务
        $this->tasksAction();
        // 主题党日
        $this->themeDayAction();
        // 志愿服务
        $this->volunteerServiceAction();
        // ajaxMsg(0, 'success', date('Y-m-d H:i', time()));
    }

    /**
     * 待办任务状态管理
     */
    protected function tasksAction(){

        // $this->logins('待办任务', '待办任务内容','tasksSction');
        // 获得所有待开始的任务 3
        $_TasksItem = M('TasksItem');
        $list = $_TasksItem->where(array(
            'status'=> '3'
        ))->getField('id,start_date,end_date');
        // echo "<pre>";
        // var_dump($list);exit;
        if (!empty($list)) {
            foreach ($list as $key => $value) {
                $int = $this->getStatusBydate($value['start_date'], $value['end_date']);
                if (3 !== $int) {
                    $_TasksItem->where(array('id'=> $key))->setField('status', $int);
                }
            }
        }
    }

    /**
     * 主题党日 状态管理
     */
    protected function themeDayAction(){
        // 获得所有待开始的任务 2,3
        $_ThemePartyDay = M('ThemePartyDay');
        $list = $_ThemePartyDay->where(array(
            'status'=> array('in', '2,3')
        ))->getField('id,status,start_time,end_time');
        if (!empty($list)) {
            foreach ($list as $key => $value) {
                $int = $this->getStatusBydate($value['start_time'], $value['end_time']);
                if (intval($value['status']) !== $int) {
                    $_ThemePartyDay->where(array('id'=> $key))->setField('status', $int);
                }
            }
        }
    }

    /**
     * 志愿服务
     */
    protected function volunteerServiceAction(){
        // 获得所有待开始的任务 2,3
        $_VolunteerService = M('VolunteerService');
        $list = $_VolunteerService->where(array(
            'status'=> array('in', '2,3')
        ))->getField('id,status,start_date,end_date');
        if (!empty($list)) {
            foreach ($list as $key => $value) {
                $int = $this->getStatusBydate($value['start_date'], $value['end_date']);
                if (intval($value['status']) !== $int) {
                    $_VolunteerService->where(array('id'=> $key))->setField('status', $int);
                }
            }
        }
    }

    protected function getStatusBydate($startDate, $endDate){
        $time = time(); // 当前时间戳
        // 日期转时间戳
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        // 如果在同一天 则结束时间到当前晚上23点59分59秒
        if ($startDate == $endDate) {
            $endDate += 86399;
        }
        if ($time > $startDate && $time < $endDate) {
            // 当前时间大于开始时间 并且小于结束时间 进行中 2
            return 2;
        }elseif ($time < $startDate && $time < $endDate) {
            // 当前时间小于开始时间 并且小于结束时间 待开始 3
            return 3;
        }elseif ($time > $startDate && $time > $endDate) {
            //当前时间大于 开始时间 并且 大于结束时间 已结束
            return 1;
        }
        return false;
    }

    /**
     * 日志
     */
    protected function logins($title, $content, $file_name){
        Vendor('utils.Log');
        \Log::outLog($title, $content, $file_name);
    }

    /**
     * 测试消息推送
     */
    public function demoSendMessage(){
        $touser = '13750003233|15626908783';
        $title = '这是一份测试推送';
        $description = '描述的点点滴滴';
        $url = 'https://www.baidu.com';
        $bool = pushQyWechatMessage($touser, $title, $description, $url);
	    var_dump($boll,112);
    }

    /**
     * 测试导出word
     */
     public function demo(){
        require 'vendor/autoload.php';
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        // var_dump($phpWord);
        // Adding an empty Section to the document...
        $section = $phpWord->addSection();
        // Adding Text element to the Section having font styled by default...
        $section->addText(
            '"Learn from yesterday, live for today, hope for tomorrow. '
                . 'The important thing is not to stop questioning." '
                . '(Albert Einstein)'
        );

        $section->addListItem('dd','bb', '11', '20', '22');


        /*
         * Note: it's possible to customize font style of the Text element you add in three ways:
         * - inline;
         * - using named font style (new font style object will be implicitly created);
         * - using explicitly created font style object.
         */

        // Adding Text element with font customized inline...
        $section->addText(
            '"Great achievement is usually born of great sacrifice, '
                . 'and is never the result of selfishness." '
                . '(Napoleon Hill)',
            array('name' => 'Tahoma', 'size' => 10)
        );

        // Adding Text element with font customized using named font style...
        $fontStyleName = 'oneUserDefinedStyle';
        $phpWord->addFontStyle(
            $fontStyleName,
            array('name' => 'Tahoma', 'size' => 10, 'color' => '1B2232', 'bold' => true)
        );
        $section->addText(
            '"The greatest accomplishment is not in never falling, '
                . 'but in rising again after you fall." '
                . '(Vince Lombardi)',
            $fontStyleName
        );

        // Adding Text element with font customized using explicitly created font style object...
        $fontStyle = new \PhpOffice\PhpWord\Style\Font();
        $fontStyle->setBold(true);
        $fontStyle->setName('Tahoma');
        $fontStyle->setSize(13);
        $myTextElement = $section->addText('"Believe you can and you\'re halfway there." (Theodor Roosevelt)');
        $myTextElement->setFontStyle($fontStyle);

        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="helloWorld111.docx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        // $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        // Saving the document as OOXML file...
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        // $objWriter->save('helloWorld.docx');
        $objWriter->save('php://output');

     }
}
