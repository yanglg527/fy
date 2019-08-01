<?php
namespace Admin\Controller;

use Admin\Model\AdminAuthRuleViewModel;
use Common\Controller\BaseController;
use Think\Controller;

/**
 * 文章管理
 * Class ContentController
 * @package Home\Controller
 */
class VoteController extends BaseAdminController
{

    function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->set_sidebar_module('App');
        $this->set_sidebar_sub('Vote');
    }

    public function index()
    {

        $admin = admin();
        $auth = session_auth();
        $map = array();
        if ($auth == 2) { //总支
            $map['Vote.branch_hq_id'] = $admin['admin_branch_hq_id'];
        }
        if ($auth == 3) { //支部
            $map['Vote.branch_id'] = $admin['admin_branch_id'];
        }

        $map['Vote.status'] = array('gt', -1);

        $keyword = I('keyword');
        if ($keyword) {
            $map['Vote.title'] = array('like', '%' . $keyword . '%');
        }

        $this->assign('search', array('keyword' => $keyword));
        $this->assign('page', D('VoteView')->findPage($map, 15, 'id desc'));
        $this->display();
    }

    public function manage($id = 0)
    {
        if ($id > 0) {
            $condition['id'] = $id;
            $detail = D('Vote')->where($condition)->find();

            $totalCount = D('Vote/VoteLogView')->where(array('VoteItem.vote_id' => $id))->count();
            $people = D('Vote/VoteLogView')->where(array('VoteItem.vote_id' => $id))->group('VoteLog.uid')->count();
            $people = $people == 0 ? 1 : $people;
            $detail['people_count'] = $totalCount / $people;
            $detail['total_count'] = $totalCount;
            $items = D('VoteItem')->where(array('vote_id' => $id))->order('count desc')->select();
            $nowcount = null;
            $noworder = null;
            foreach ($items as $key=>$v){
                if($nowcount == null){
                    $nowcount =$v['count'];
                    $noworder = $key+1;
                }elseif($nowcount != $v['count']){
                    $nowcount =$v['count'];
                    $noworder = $noworder+1;
                }
                $items[$key]['order'] = $noworder;
                $items[$key]['vote_title'] = $detail['title'];
                $items[$key]['people_count'] = $detail['people_count'];
                $items[$key]['total_count'] = $detail['total_count'];
                if($detail['total_count'] == 0){
                    $items[$key]['present'] = '0'.'%';
                }else{
                    $items[$key]['present'] =  floor($v['count']/$detail['total_count']*100).'%';
                }
            }
            $_SESSION['pages'] = $items;//将该条投票的列表放到session 以便VoteListToExcel方法   excel下载使用
            $this->assign('vote_roles', D('VoteRole')->where(array('vote_id' => $id))->select());
            $this->assign('items', $items);
            $this->assign('detail', $detail);
        }
        $this->display();
    }

    public function edit($id = 0)
    {
        if ($id > 0) {
            $condition['id'] = $id;
            $detail = D('Vote')->where($condition)->find();
            $this->assign('vote_roles', D('VoteRole')->where(array('vote_id' => $id))->getField('role_id', true));
            $this->assign('items', D('VoteItem')->where(array('vote_id' => $id))->select());

            // 参与投票范围
            $voteBranchs = M('VoteLimitBranch')->where(array(
                'vote_id' => $id
            ))->getField('branch_id', true);
            $this->assign('vote_branchs', $voteBranchs);

            // 监督员
            $supervisors = D('VoteSupervisorView')->where(array(
                'VoteSupervisor.vote_id' => $id
            ))->getField('uid', true);
            $this->assign('vote_supervisors', $supervisors);

            $this->assign('detail', $detail);
        }
        $role = M('Role')->select();
        array_push($role, array('id'=>6,'name'=>'党务工作者'));
        // ajaxMsg(0, '', $detail);
        $this->assign('p', I('get.p', 1));
        $this->assign('visiblerange', array(
            1 => '正常完成时显示',
            2 => '投票结束后显示',
            3 => '管理员可见',
            4 => '党委办可见'
        ));
        $this->assign('allUser', getUsersBystatus(1));
        $this->assign('branchs',getBranchInfo());
        $this->assign('roles', $role);
        //真的不是我想写成这样的。被逼的
        $group_dw  = D('PartyBranchMembersView')->order('branch_sort desc')->group('branch_id')->select();
       // $dwmsg = D('PartyBranchMembersView')->order('branch_sort desc')->select();
    //  var_dump($group_dw);
       $dwmsg = array();
        foreach($group_dw as $v){
            if($v['branch_id'] == 318){
                //不要测试支部
                continue;
            }
            $same_branch = D('PartyBranchMembersView')->where(array('branch_id'=>$v['branch_id']))->order('post_sort')->select();
         
            //在这加入党小组组长
            $gl = D('PartyGroupView')->where(array('branch_id'=>$v['branch_id']))->select();
            $gl_arr = array();
            foreach($gl as $gv){
                if($gv['gl_uid']){
                    $gl_arr[] = $gv;
                }
            }
          
             $temp = voteOrder($same_branch,$gl_arr);
            foreach($temp as $tv){
                $dwmsg[] = $tv;
            }
          //  array_push($dwmsg,dutyorder($same_branch));
        }
        // var_dump($dwmsg);

        foreach($dwmsg as $k=>$v){
            if($v['post_name']){
                $dw_list[$k]['name'] = $v['realname'].'-'.$v['branch_name'].'-'.$v['post_name'];
            }else{
                $dw_list[$k]['name'] = $v['gl_realname'].'-'.$v['branch_name'].'-'.'党小组长';
            }

        }
        $this->assign('dw_list', $dw_list);

        $rolemsg = D('UserView')->where(array('User.status'=>1,'User.uid'=>array('neq',1)))->order('branch_sort desc')->select();

        foreach($rolemsg as $k=>$v){

            $role_list[$k]['name'] = $v['realname'].'-'.$v['branch_name'].'-'.$v['role_name'];
        }
        $this->assign('role_list', $role_list);
        $this->display();
    }


    public function detail($id = 0)
    {
        if ($id > 0) {
            $condition['id'] = $id;
            $detail = D('Vote')->where($condition)->find();
            $this->assign('vote_roles', D('VoteRole')->where(array('vote_id' => $id))->getField('role_id', true));
            $this->assign('items', D('VoteItem')->where(array('vote_id' => $id))->select());

            // 参与投票范围
            $voteBranchs = M('VoteLimitBranch')->where(array(
                'vote_id' => $id
            ))->getField('branch_id', true);
            $this->assign('vote_branchs', $voteBranchs);

            // 监督员
            $supervisors = D('VoteSupervisorView')->where(array(
                'VoteSupervisor.vote_id' => $id
            ))->getField('uid', true);
            $this->assign('vote_supervisors', $supervisors);

            $this->assign('detail', $detail);
        }
        $role = M('Role')->select();
        array_push($role, array('id'=>6,'name'=>'党务工作者'));
        // ajaxMsg(0, '', $detail);
        $this->assign('p', I('get.p', 1));
        $this->assign('visiblerange', array(
            1 => '正常完成时显示',
            2 => '投票结束后显示',
            3 => '管理员可见',
            4 => '党委办可见'
        ));
        $this->assign('allUser', getUsersBystatus(1));
        $this->assign('branchs',getBranchInfo());
        $this->assign('roles', $role);
        $group_dw  = D('PartyBranchMembersView')->order('branch_sort desc')->group('branch_id')->select();
        // $dwmsg = D('PartyBranchMembersView')->order('branch_sort desc')->select();
        $dwmsg = array();
        foreach($group_dw as $v){
            $same_branch = D('PartyBranchMembersView')->where(array('branch_id'=>$v['branch_id']))->select();

            //在这加入党小组组长
            $gl = D('PartyGroupView')->where(array('branch_id'=>$v['branch_id']))->select();
            foreach($gl as $gv){
                if($gv['gl_uid']){
                    $gl_arr[] = $gv;
                }
            }
            //  var_dump($temp);
            $temp = voteOrder($same_branch,$gl_arr);
            foreach($temp as $tv){
                $dwmsg[] = $tv;
            }
            //  array_push($dwmsg,dutyorder($same_branch));
        }
        // var_dump($dwmsg);

        foreach($dwmsg as $k=>$v){
            if($v['post_name']){
                $dw_list[$k]['name'] = $v['realname'].'-'.$v['branch_name'].'-'.$v['post_name'];
            }else{
                $dw_list[$k]['name'] = $v['gl_realname'].'-'.$v['branch_name'].'-'.$v['name'].'组长';
            }

        }
        $this->assign('dw_list', $dw_list);

        $rolemsg = D('UserView')->where(array('User.status'=>1,'User.uid'=>array('neq',1)))->order('branch_sort desc')->select();

        foreach($rolemsg as $k=>$v){

            $role_list[$k]['name'] = $v['realname'].'-'.$v['branch_name'].'-'.$v['role_name'];
        }
        $this->assign('role_list', $role_list);
        $this->display();
    }

    public function ajaxDecVote($id = 0)
    {

        $detail = D('Vote')->where(array('id' => $id))->find();
        if (checkAuth($detail['branch_id']) > 0) {
            D('Vote')->where(array('id' => $id))->save(array('status' => -1));
            ajaxMsg(0, '保存成功');
        }
    }

    public function ajaxisopen()
    {
        $data = I();
        $isopen = $data['isopen'];
        $id = $data['id'];
        if ($id){
            D('Vote')->where(array('id' => $id))->save(array('status' => $isopen));
            ajaxMsg(0, '修改成功');
        }else{
            ajaxMsg(1, '修改失败');
        }
    }

    #新增文章 ajax提交
    public function ajaxSave()
    {
        //监督员数组转为逗号分隔字符串
        $jd = I('jd');
        $id = I('id');
        $title = I('title');
        $cover = I('cover');
        $limited_time = I('limited_time');
        $multiply_count = I('multiply_count', 1);
        $content = $_POST['content'];
        $status = I('status', 0);
        // 支部
        $branchs = I('branchs');

        if (!$title && !$id) {
            ajaxMsg(1, '请先填写标题');
        }
        if (!$limited_time) {
            ajaxMsg(1, '请先选择截止时间');
        }
        // 当天 11:59:59
        $limited_time = strtotime($limited_time) + 86399;
        $items = I('items');
        $roles = I('roles');
        $size = sizeof($items);
        $sizeRole = sizeof($roles);
        if ($size < 2 && !$id) {
            ajaxMsg(1, '选项必须大于2个');
        }

        if (!$roles && !$id) {
            ajaxMsg(1, "查看限制至少选择1个");
        }
        if ($sizeRole < 1 && !$id) {
            ajaxMsg(1, '查看限制至少选择1个');
        }
        foreach ($items as $item) {
            if (!$item['title'] && !$id) {
                ajaxMsg(1, '选项内容还没填写');
            }
        }
        // 监督员
        $supervisor = array();
        // 可参与投票的支部
        $branchsArr = array();
        // 角色
        $roleArr = array();
        // 选项
        $itemsArr = array();
        if ($id) {
            $detail = D('Vote')->find($id);
            if (!$detail) {
                ajaxMsg(1, "找不到该项目");
            }
            $auth = checkAuth($detail['branch_id']);

            if ($auth > 0) {
                try {
                    $post = I('post.');
                    $post['content'] = htmlspecialchars_decode($post['content']);
                    $post['limited_time'] = $limited_time;
                    $_vote = D('Vote');
                    $_vote->startTrans();
                    if ($_vote->save($post) === false) {
                        throw new \Exception("请稍后再试", 1);
                    }
                    // 监督员
                    if (!empty($jd)) {
                        foreach ($jd as $key => $value) {
                            $supervisor[$key]['vote_id'] = $id;
                            $supervisor[$key]['uid'] = $value;
                        }
                        // 监督员
                        $_votesupervisor = M('VoteSupervisor');
                        $_votesupervisor->where(array('vote_id'=>$id))->delete();
                        $_votesupervisor->addAll($supervisor);
                    }

                    // 可参与投票的支部
                    if (!empty($branchs)) {
                        foreach ($branchs as $key => $value) {
                            $branchsArr[$key]['vote_id'] = $id;
                            $branchsArr[$key]['branch_id'] = $value;
                        }
                        $_votelimitbranch = M('VoteLimitBranch');
                        $_votelimitbranch->where(array('vote_id'=>$id))->delete();
                        $_votelimitbranch->addAll($branchsArr);
                    }

                    // 角色
                    if (!empty($roles)) {
                        foreach ($roles as $key => $value) {
                            $roleArr[$key]['vote_id'] = $id;
                            $roleArr[$key]['role_id'] = $value;
                        }
                        $_voterole = M('VoteRole');
                        $_voterole->where(array('vote_id'=>$id))->delete();
                        $_voterole->addAll($roleArr);
                    }

                    // 选项
                    if (!empty($items)) {
                        foreach ($items as $key => $value) {
                            $itemsArr[$key]['vote_id'] = $id;
                            $itemsArr[$key]['title']   = $value['title'];
                            $itemsArr[$key]['image']   = $value['image'] == '' ? null : $value['image'];
                            $itemsArr[$key]['count']   = 0;
                            $itemsArr[$key]['sort']    = $value['sort'];
                        }
                        $_voteitem = M('VoteItem');
                        $_voteitem->where(array('vote_id'=>$id))->delete();
                        $_voteitem->addAll($itemsArr);

                    }
                    $_vote->commit();
                } catch (\Exception $e) {
                    $_vote->rollback();
                    ajaxMsg(1, '服务器繁忙'.$e);
                }
            }
        } else {
            $array = array(
                'limited_time' => $limited_time,
                'cover' => $cover == '' ? null : $cover,
                'title' => $title,
                'status' => $status,
                'content' => $content,
                'visiblerange' => I('visiblerange'),
                'multiply_count' => $multiply_count > 1 ? $multiply_count : 1,
                'is_multiply' => $multiply_count > 1 ? 1 : 0,
            );

            $auth = session_auth();
            if ($auth > 0) {
                $array['published_time'] = time();
                $array = set_save_auth($array);
                //                ajaxMsg(1,to_json_string($roles));
                $id = D('Vote')->add($array);

                // 监督员
                foreach ($jd as $key => $value) {
                    $supervisor[$key]['vote_id'] = $id;
                    $supervisor[$key]['uid'] = $value;
                }
                M('VoteSupervisor')->addAll($supervisor);
                // 可参与投票的支部
                foreach ($branchs as $key => $value) {
                    $branchsArr[$key]['vote_id'] = $id;
                    $branchsArr[$key]['branch_id'] = $value;
                }
                M('VoteLimitBranch')->addAll($branchsArr);
                foreach ($items as $item) {
                    D('VoteItem')->add(array(
                        'vote_id' => $id, 'title' => $item['title'], 'image' => $item['image'] == '' ? null : $item['image'], 'count' => 0,
                        'sort' => $item['sort']
                    ));
                }

                foreach ($roles as $item) {
                    D('VoteRole')->add(array('vote_id' => $id, 'role_id' => $item));
                }
            }
        }
        ajaxMsg(0, '保存成功');
    }



    public function ajaxGetSelect()
    {
        # code...
        $type = I('type');
        switch ($type) {
            case 'branch':
                  $list = getBranchInfo();
                break;
            case 'dw':
                    $msg = D('PartyBranchMembersView')->order('branch_sort desc')->select();
                    foreach($msg as $k=>$v){

                        $list[$k]['name'] = $v['realname'].'-'.$v['branch_name'].'-'.$v['post_name'];
                    }
                break;
            case 'role':
            $msg = D('UserView')->where(array('User.status'=>1,'User.uid'=>array('neq',1)))->order('branch_sort desc')->select();

            foreach($msg as $k=>$v){

                $list[$k]['name'] = $v['realname'].'-'.$v['branch_name'].'-'.$v['role_name'];
            }
                break;
        }
        if($list){
            ajaxMsg(0, '获取数据成功',$list);
        }else{
            ajaxMsg(1, '获取数据出错');
        }
    }


    /**
     * 导出投票列表
     */
    public function VoteListToExcel()
    {
        $fileName = '投票统计列表';
        $cellTitle = $_SESSION['pages'][0]['vote_title'];
        $total = "参与人数：".($_SESSION['pages'][0]['people_count']).'       '.'总投票数：'.($_SESSION['pages'][0]['total_count']);
        $data = $_SESSION['pages'];
        $cellName=[
            0=>['order','排名',0,12,'CENTER'],
            1=>['title','投票项',0,70,'LEFT'],
            2=>['present','统计',0,12,'RIGHT'],
            3=>['count','票数',0,12,'RIGHT'],
//            3=>['people_count','参与人数',0,12,'LEFT'],
//            4=>['total_count','总投票数',0,12,'LEFT'],
        ];
        $this->exportExcel($fileName, $cellTitle, $cellName, $data,$total);
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
    public function exportExcel($title, $cellTitle, $cellName, $data,$total)
    {
        //引入核心文件
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();

        //定义配置
        $topNumber = 3;//表头有几行占用
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
//        $objPHPExcel->getActiveSheet()->mergeCells('A1:'.$cellKey[count($cellName)-1].'1');//合并单元格（如果要拆分单元格是需要先合并再拆分的，否则程序会报错）
        $objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $cellTitle);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        
        //总投票  总参与数
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(40);//设
        $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);//设
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);//设置是否加粗
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', $total);
        $objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        //处理表头
        foreach ($cellName as $k=>$v)
        {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellKey[$k].$topNumber, $v[1]);//设置表头数据
//            $objPHPExcel->getActiveSheet()->freezePane($cellKey[$k].($topNumber+1));//冻结窗口
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
            $C = 'C'.$k;//右对齐
            $objPHPExcel->getActiveSheet()->getStyle($C)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $D = 'D'.$k;
            $objPHPExcel->getActiveSheet()->getStyle($D)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('C3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('D3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
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