<?php
namespace Twolearnandonedo\Model;

use Common\Model\BaseViewModel;
/**
 *  两学一做 出勤人员视图模型
 */
class StudyDoAttendViewModel extends BaseViewModel
{
    protected $viewFields = array(
        'StudyDoAttend' => array('uid', 'attend_type', 'status', '_type'=>'LEFT'),
        'User' => array(
            'realname'=>'name',
            'headimgurl' => 'image',
            '_on'=>'StudyDoAttend.uid=User.uid',
            '_type'=>'LEFT'),
    );
}
