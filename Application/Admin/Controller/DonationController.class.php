<?php
namespace Admin\Controller;

use Admin\Model\AdminAuthRuleViewModel;
use Common\Controller\BaseController;
use Common\Util\ImageUploadUtil;
use Think\Controller;

/**
 * 文章管理
 * Class ContentController
 * @package Home\Controller
 */
class DonationController extends BaseAdminController
{
    function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->set_sidebar_module('App');
        $this->set_sidebar_sub('Donation');
    }

    public function index()
    {

        $admin = admin();
        $auth = session_auth();
        $map = array();
        // if($auth == 2){//总支
        //     $map['Donation.branch_hq_id'] = $admin['admin_branch_hq_id'];
        // }
        // if($auth == 3){//支部
        //     $map['Donation.branch_id'] = $admin['admin_branch_id'];
        // }

        $map['Donation.status']=array('gt',-1);

        $keyword = I('keyword');
        if ($keyword) {
            $map['Donation.title'] = array('like', '%' . $keyword . '%');
        }
        $this->assign('search',array('keyword'=>$keyword));

        $page =D('DonationView')->findPage($map, 15, 'id desc');
        foreach($page['list'] as $index=>$item){
            $total = D('DonationLogView')->where(array('DonationLog.donation_id'=>$item['id']))->sum('DonationLog.fee');
          
            $item['real_money'] = number_format($total, 2 , '.' , ',');
            $page['list'][$index] = $item;
        }




        $this->assign('page', $page);
        $this->display();
    }

    public function show()
    {
        $id = I('id');
        $detail = D('Donation')->find();
        $this->assign('detail', $detail);
        $keyword = I('keyword');
        if ($keyword) {
            $page = D('DonationLog')->findPage("realname like '%$keyword%'", 15, 'id desc');
            $this->assign('search', array('keyword'=>$keyword));
        } else {
            $map['DonationLog.donation_id'] = $id;
            $map['PayOrder.status'] = 1;
            $page =D('DonationLogView')->findPage($map, 15, 'id desc');
        }
        $_SESSION['page'] =$page['list'];
        $this->assign('page', $page);
        $this->display();
    }

    public function edit($id = 0)
    {
        if ($id > 0) {
            $condition['id'] = $id;
            $detail = D('DonationView')->where($condition)->find();
          
            $this->assign('detail', $detail);
        }
        $this->assign('p', I('get.p', 1));
        $this->display();
    }

    function ajaxUploadCover()
    {
        ajaxMsg(0, "success", ImageUploadUtil::upload("donation/"));
    }

    public function ajaxDecVote($id = 0){

        $detail = D('Donation')->where(array('id'=>$id))->find();
        if(checkAuth($detail['branch_id']) > 0){
            D('Donation')->where(array('id'=>$id))->save(array('status'=>-1));
            ajaxMsg(0, '保存成功');
        }
    }

    #新增文章 ajax提交
    public function ajaxSave()
    {
        $id = I('id');
        $title = I('title');
//        $name = I('name');
        $cover = I('cover');
        $target = I('target',0);
        $content = $_POST['intro'];
        $status = I('status', 1);
        $start_time = I('start_time');
        $end_time = I('end_time');
        $proposer_name = I('publish_name');
        $uid = I('uid');
        if (!$uid) {
            ajaxMsg(1, '请先填写发布人');
        }
        if (!$title) {
            ajaxMsg(1, '请先填写标题');
        }
        if (!$start_time) {
            ajaxMsg(1, "请选择捐款开始日期");
        }
        if (!$end_time) {
            ajaxMsg(1, "请选择捐款结束日期");
        }
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time);
//        if (!$name) {
//            ajaxMsg(1, '请先填写募捐人姓名');
//        }
//        if (!$target) {
//            ajaxMsg(1, '请先填写募捐目标金额');
//        }
//        if ($target < 0) {
//            ajaxMsg(1, '目标金额必须大于0');
//        }



        if ($id) {
            $array = array(
                'title' => $title,
                'target' => $target,
                'status' => $status,
                'cover'=>$cover,
                'intro' => $content,
                'start_time'=>$start_time,
                'end_time' =>$end_time,
                'uid' => $uid,
//                'proposer_name' => $name
            );
            $detail = D('Donation')->find($id);
            if (!$detail) {
                ajaxMsg(1, "找不到该项目");
            }
           
                D('Donation')->where(array('id' => $id))->save($array);

        } else {
            $array = array(
                'title' => $title,
                'target' => $target,
                'status' => $status,
                'intro' => $content,
                'proposer_name' => $proposer_name,
                'start_time'=>$start_time,
                'end_time' =>$end_time,
                'uid' => $uid,
                'create_time'=>time(),
            );

            // $admin = admin();
            //     $array['create_time'] = time();
            //     $array['branch_id'] = $admin['branch_id'];
                D('Donation')->add($array);



        }


        ajaxMsg(0, '保存成功');
    }
    /**
     * 导出捐款列表
     * @param  string $fileName 文件名
     * @param  string $strTable 内如
     * @return [type]           [description]
     */
    public function DonationListToExcel()
    {
        $fileName = '捐款列表';
        $cellTitle = '捐款列表';
        $data = $_SESSION['page'];
        $cellName=[
            0=>['id','id',0,12,'LEFT'],
            1=>['user_realname','姓名',0,12,'LEFT'],
            2=>['fee','金额',0,12,'LEFT'],
            3=>['remark','留言',0,50,'LEFT'],
        ];
        $this->exportExcel($fileName, $cellTitle, $cellName, $data);
    }

    /**
     * 加载PHPExcel类
     * @return [type] [description]
     */
    protected function loadPhoExcel5()
    {
        vendor("PHPExcel.PHPExcel");//如果这里提示类不存在，肯定是你文件夹名字不对。
        $this->PHPReader = new \PHPExcel_Reader_Excel5();
    }

    /**
     * execl数据导出
     * @param string $title 文件名的前缀
     * @param string $cellTitle 表头标题
     * @param array $cellName 表头及字段名
     * @param array $data 导出的表数据
     *
     * 特殊处理：合并单元格需要先对数据进行处理
     */
    public function exportExcel($title, $cellTitle, $cellName, $data)
    {
        //引入核心文件
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();

        //定义配置
        $topNumber = 2;//表头有几行占用
        $xlsTitle = iconv('utf-8', 'gb2312', $title);//文件名称
        $fileName = $title.date('_YmdHis');//文件名称
        $fileName = $xlsTitle.date('_YmdHis');//文件名称
        $cellKey = array(
            'A','B','C','D','E','F','G','H','I','J','K','L','M',
            'N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
            'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM',
            'AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'
        );

        //处理表头标题
        $objPHPExcel->getActiveSheet()->mergeCells('A1:'.$cellKey[count($cellName)-1].'1');//合并单元格（如果要拆分单元格是需要先合并再拆分的，否则程序会报错）
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $cellTitle);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        //处理表头
        foreach ($cellName as $k=>$v)
        {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellKey[$k].$topNumber, $v[1]);//设置表头数据
            $objPHPExcel->getActiveSheet()->freezePane($cellKey[$k].($topNumber+1));//冻结窗口
            $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k].$topNumber)->getFont()->setBold(true);//设置是否加粗
            $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k].$topNumber)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
            if($v[3] > 0)//大于0表示需要设置宽度
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($cellKey[$k])->setWidth($v[3]);//设置列宽度
            }
        }

        //处理数据
        foreach ($data as $k=>$v)
        {
            foreach ($cellName as $k1=>$v1)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($cellKey[$k1].($k+1+$topNumber), $v[$v1[0]]);
                if($v['end'] > 0)
                {
                    if($v1[2] == 1)//这里表示合并单元格
                    {
                        $objPHPExcel->getActiveSheet()->mergeCells($cellKey[$k1].$v['start'].':'.$cellKey[$k1].$v['end']);
                        $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k1].$v['start'])->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    }
                }
                if($v1[4] != "" && in_array($v1[4], array("LEFT","CENTER","RIGHT")))
                {
                    $v1[4] = eval('return PHPExcel_Style_Alignment::HORIZONTAL_'.$v1[4].';');
                    //这里也可以直接传常量定义的值，即left,center,right；小写的strtolower
                    $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k1].($k+1+$topNumber))->getAlignment()->setHorizontal($v1[4]);
                }
            }
        }
        //接下来当然是下载这个表格了，在浏览器输出就好了
        ob_end_clean() ;
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename='.$fileName.Date('Y-m-d').'.xls');
        header("Content-Transfer-Encoding:binary");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        return ;
        // exit;
    }
}