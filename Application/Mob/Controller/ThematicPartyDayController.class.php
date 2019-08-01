<?php
namespace Mob\Controller;

use Common\Controller\BaseController;

/**
 * 主题党日
 * @author calvin
 */
class ThematicPartyDayController extends BaseController
{
    const branchcover = '/Public/Mob/images/branch.png';
    const basestatus = array('草稿', '已结束', '待开始', '进行中', '已终止');
    const defaultclass = array(1 => 'finished', 2 => 'ready', 3 => 'doing' );
    const images = array('image/gif', 'image/jpeg', 'image/png', 'image/bmp');

    function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 计划任务更新主题活动状态入口
     */
    public function statusManageAction()
    {
        // 待开始 to 进行中
        // 查询所有状态为3（进行中）、 如果当前时间大于结束时间 更改状态
        // 进行中 to 已结束
        // 查询所有状态为3（进行中）、 如果当前时间大于结束时间 更改状态
        //
    }

    public function index()
    {
        $p = I('get.p', 1);
        $keyword = I('get.keyword');
        $branchId = I('get.branchId');
        $branchName = I('get.branchName');
        $map['ThemePartyDay.status'] = array('in', '1,2,3');

        $map['ThemePartyDay.branch_id'] = array('neq', 318);
        if (is_numeric($branchId)) {
            $map['ThemePartyDay.branch_id'] = array('eq', $branchId);
        }
        if ($keyword) {
            $map['ThemePartyDay.theme'] = array('like', '%' . $keyword . '%');
        }
        $item = D('Admin/ThemeDayView')
            ->where($map)
            ->page("$p,15")
            ->order('create_at desc')
            ->select();
        $this->assign('item', $item);
        $this->assign('branchs', getBranchInfo());
        $this->assign('branchcover', self::branchcover);
        $this->assign('basestatus', self::basestatus);
        $this->assign('defaultclass', self::defaultclass);
        $this->assign('default', array(
            'branchName'=>$branchName,
            'branchId'=>$branchId,
            'keyword'=>$keyword,
        ));
        $this->display();
    }

    /**
     * 详情
     */
    public function detail()
    {
        $id = I('get.id');
        $detail = array();
        if ($id) {
            $detail = D('Admin/ThemePartyDay')->detail($id);
            // echo "<pre>";
            // var_dump($detail);exit;
        }
        $this->assign('images', self::images);
        $this->assign('detail', $detail);
        $this->display();
    }


}
