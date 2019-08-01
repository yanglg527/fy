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

class UserFileWanderController extends BaseAuthController
{

    function _initialize()
    {
        parent::_initialize();
    }
    /**
     * 文件流转功能详细页
     */
    public function fileWanderPage()
    {
        $id = I('id');
        $role_data = C('ROLE_GROUP');
        $detail = D('UserFileWander')->where(array('id' => $id, 'status' => array('gt', 0)))->find();
        $user = user();
        $isown = false;
        if ($detail['uid'] == $user['uid']) {
            $isown = true;
        }
        $this->assign('isown', $isown);
        if ($detail['branch_id']) {
            $detail['branch_id_arr'] = explode(',', $detail['branch_id']); //变换时的数组     
            foreach ($detail['branch_id_arr'] as $v) {
                //字符串
                $branch_name = M('party_branch')->where(array('id' => $v))->getField('name');
                $branch_text = $branch_text . $branch_name . ' ' . '/' . ' '; //显示在input中的名字
            }
            $detail['branch_text'] = trim($branch_text, ' / ');
        }

        if ($detail['role_id']) {
            $detail['role_id_arr'] = explode(',', $detail['role_id']); //变换时的数组
            foreach ($detail['role_id_arr'] as  $v) {
                $key = $v - 1;
                $role_text = $role_text . $role_data[$key]['name'] . '/';
            }
            $detail['role_text'] = trim($role_text, ' /');
        }

        if ($detail['post_id']) {
            $detail['post_id_arr'] = explode(',', $detail['post_id']); //变换时的数组
            foreach ($detail['post_id_arr'] as  $v) {
                $post_name = M('party_post')->where(array('status_id' => $v))->getField('name');
                $post_text = $post_text . $post_name . ' ' . '/' . ' '; //显示在input中的名字
            }
            $detail['post_text'] = trim($post_text," /");
        }

        if ($detail['dw_id']) {
            $detail['dw_id_arr'] = explode(',', $detail['dw_id']); //变换时的数组
            foreach ($detail['dw_id_arr'] as  $v) {
                $dw_name = M('party_post')->where(array('status_id' => $v))->getField('name');
                $dw_text = $dw_text . $dw_name . ' ' . '/' . ' '; //显示在input中的名字
            }
            $detail['dw_text'] = trim($dw_text, ' /');
        }

        if ($detail['post_id']) {
            $detail['post_id_arr'] = explode(',', $detail['post_id']); //变换时的数组
            foreach ($detail['post_id_arr'] as  $v) {
                $post_name = M('party_post')->where(array('status_id' => $v))->getField('name');
                $post_text = $post_text . $post_name . ' ' . '/' . ' '; //显示在input中的名字
            }
            $detail['post_text'] = trim($post_text, '/');
        }

        if ($detail['dw_id']) {
            $detail['dw_id_arr'] = explode(',', $detail['dw_id']); //变换时的数组
            foreach ($detail['dw_id_arr'] as  $v) {
                $dw_name = M('party_post')->where(array('status_id' => $v))->getField('name');
                $dw_text = $dw_text . $dw_name . ' ' . '/' . ' '; //显示在input中的名字
            }
            $detail['dw_text'] = trim($dw_text, '/');
        }
        // var_dump($detail);
        //file文件显示

        if ($detail['file_id']) {
            $file_arr =  M('files')->where(array('files_id' => array('in', $detail['file_id'])))->select();
            // var_dump($file_arr);
            $exts = array('doc', 'pdf', 'xlsx', 'xls', 'docx');
            foreach ($file_arr as $k => $v) {

                $file_ext = getFileExtension($v['files_path']);
                if ($file_ext == 'jpeg') {
                    $file_arr[$k]['type'] = 'png';
                } else {
                    $file_arr[$k]['type'] = $file_ext;
                }
                //$exts = array('doc', 'pdf', 'xlsx', 'xls', 'docx');
                //好东西，解决安卓不能在线预览文件的工具
                //https://view.officeapps.live.com/op/view.aspx?src=dj.zhzy.net.cn/Uploads/file_wander/2019-05-22/5ce4df3487e8b.doc

                if (in_array($file_ext, $exts)) {
                    $file_arr[$k]['files_path'] = 'https://view.officeapps.live.com/op/view.aspx?src=' . $_SERVER["HTTP_HOST"] . $v['files_path'];
                }
            }
        }

        //详细页下面的阅读量获取
        $read_list = D('UserFileWanderClick')->where(array('fw_id' => $id))->select();
        foreach($read_list as $key=>$value){
            if($value['headimgurl'] == null){
                $read_list[$key]['headimgurl'] = '/Public/Common/img/common-head.png';
            }
        }
        $this->assign('read_count', count($read_list));
        $this->assign('read_list', $read_list);
        $this->assign('file_arr', $file_arr);
        $this->assign('detail', $detail);
        //添加点击量
        $this->addClick($id, $detail['uid']);
        $this->display();
    }

    /**
     * 文件流转功能添加编辑页面
     */
    public function addFileWander()
    {

        $id = I('id');
        $detail = array();
        $branch_data = getBranchInfo();
        $role_data = C('ROLE_GROUP');
        $party_post = M('party_post')->where(array('id' => array('in', '2,3,5,6,7,4')))->select();
        $party_post = filejueseorder($party_post, 1);
        $dw_post_arr = M('party_post')->where(array('id' => array('in', '2,3,15,14,4')))->select();
        $dw_post_arr = filejueseorder($dw_post_arr, 2);
        $this->assign('dw_post_arr', $dw_post_arr);
        $this->assign('party_post', $party_post);
        $this->assign('branch_data', $branch_data);
        $this->assign('role_data', $role_data);

        if ($id) {
            $title = '编辑文件';
            //编辑
            //需要组装这几个数组branch_text role_text '{$detail.branch_arr}''{$detail.branch_text_arr}''{$detail.role_arr}''{$detail.role_text_arr}'
            //branch_id = 1,2,3
            $detail = D('UserFileWander')->where(array('id' => $id))->find();

            $detail['branch_arr'] = $detail['branch_id']; //input的value
            $detail['role_arr']  = $detail['role_id']; //input的value
            $detail['post_arr']  = $detail['post_id']; //input的value
            $detail['dw_arr']  = $detail['dw_id'];
            $detail['branch_id_arr'] = explode(',', $detail['branch_id']); //变换时的数组
            $detail['role_id_arr'] = explode(',', $detail['role_id']); //变换时的数组
            $detail['post_id_arr'] = explode(',', $detail['post_id']); //变换时的数组
            $detail['dw_id_arr'] = explode(',', $detail['dw_id']); //变换时的数组
            //支部选择
            foreach ($detail['branch_id_arr'] as $v) {
                //字符串
                $branch_name = M('party_branch')->where(array('id' => $v))->getField('name');
                $branch_text_arr[] = $branch_name; //支部名字数组
                $branch_text = $branch_text . $branch_name . ','; //显示在input中的名字
            }
            $detail['branch_text'] = trim($branch_text, ',');
            $detail['branch_text_arr'] = $branch_text_arr;
            //角色选择
            foreach ($detail['role_id_arr'] as  $v) {
                //$v = 2  ==  $role_data = 1
                $key = $v - 1;
                $role_text_arr[] = $role_data[$key]['name']; //角色名字数组
                $role_text = $role_text . $role_data[$key]['name'] . ',';
                //字符串
                // $branch_name = M('party_branch')->where(array('id'=>$v))->getField('name');
                // $branch_text =$branch_text.',' .$branch_name.',';
                // $branch_text_arr[] = $branch_name;
            }
            $detail['role_text'] = trim($role_text, ',');
            $detail['role_text_arr'] = $role_text_arr;

            //party_post 支部角色
            foreach ($detail['post_id_arr'] as  $v) {
                $post_name = M('party_post')->where(array('status_id' => $v))->getField('name');
                $post_text_arr[] = $post_name; //支部名字数组
                $post_text = $post_text . $post_name . ','; //显示在input中的名字

            }
            $detail['post_text'] = trim($post_text, ',');
            $detail['post_text_arr'] = $post_text_arr;

            //party_post 党委角色
            foreach ($detail['dw_id_arr'] as  $v) {
                $dw_name = M('party_post')->where(array('status_id' => $v))->getField('name');
                $dw_text_arr[] = $dw_name; //支部名字数组
                $dw_text = $dw_text . $dw_name . ','; //显示在input中的名字

            }
            $detail['dw_text'] = trim($dw_text, ',');
            $detail['dw_text_arr'] = $dw_text_arr;
            // var_dump($detail);
            //file文件显示
            $file_arr =  M('files')->where(array('files_id' => array('in', $detail['file_id'])))->select();
            // var_dump($file_arr);
            foreach ($file_arr as $k => $v) {
                $file_arr[$k]['type'] = getFileExtension($v['files_path']);
            }
            $detail['add_name'] = M('user')->where(array('uid' => $detail['uid']))->getField('realname');
        } else {
            //新增
            $user = user();
            $detail['uid'] = $user['uid'];
            $detail['add_name'] = $user['realname'];
            $detail['create_time'] = time();
            $title = '新增文件';
        }
        $this->assign('title', $title);
        //   var_dump($detail);
        $this->assign('file_arr', $file_arr);
        $this->assign('detail', $detail);
        // var_dump($detail);
        $this->display();
    }
    /**
     * 新增和保存
     */
    public function ajaxSave()
    {
        # code...
        $id = I('id', 1);
        // surplus_file_id 需要删除的id
        $data['uid'] = I('uid');
        $data['file_id'] = I('file_id');
        $surplus_file_id = I('surplus_file_id');
        $data['title'] = I('title');
        $data['add_name'] = I('add_name');
        $data['create_time'] = strtotime(I('create_time'));
        $data['content'] = I('content');
        $data['branch_id'] = I('branch_id');
        $data['role_id'] = I('role_id');
        $data['post_id'] = I('status_id');
        $data['dw_id'] = I('dw_id');
        if ($id) {
            //编辑
            // id=1&file_id=163%2C164&uid=10437&surplus_file_id=&title=ceshi&add_name=%E9%9F%A9%E8%8D%A3%E7%A4%BC&create_time=2019-04-14&content=eqweqweqweqweqweqweqw&branch_input=325%2C323&role_input=1%2C2%2C3
            //  $data['id'] = I('id');

            //file_id的新增与删减
            if ($surplus_file_id) {
                $sur =  explode(',', $surplus_file_id);
                foreach ($sur as $v) {
                    $str = $data['file_id'];
                    $str = str_replace($v, '', $str);
                    $data['file_id'] = trim($str, ',');
                }
            }
            //  var_dump($data);
            D('UserFileWander')->where(array('id' => $id))->save($data);
            $res = $id;
        } else {
            //新增
            $res = D('UserFileWander')->add($data);
            $this->sendToQy($res);
        }
        ajaxMsg(0, 'success', $res);
    }
    /**
     * 文件添加成功后推送消息到企业微信
     */
    public function sendToQy($fw_id = '')
    {
        # code...
        if (!$fw_id) {
            return false;
        }
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/Mob/UserFileWander/fileWanderPage?id=' . $fw_id;
        $result_user = array();
        $fw = D('UserFileWander')->where(array('id' => $fw_id))->find();
        //组装用户信息
        //等级由告到低是党委dw_id>支部角色post_id>支部branch_id>角色role_id
        //有dw_id或者post_id就不用考虑role_id
        if ($fw['dw_id'] || $fw['post_id']) {
            $user_arr = array();
            if ($fw['dw_id']) {
                $pom = D('PartyOrganizationMembers')->where(array('status' => array('in', $fw['dw_id'])))->select();
                foreach ($pom as $v) {
                    $user_arr[] = $v['uid'];
                }
            }
            if ($fw['post_id']) {
                $where['status'] = array('in', $fw['post_id']);
                if ($fw['branch_id']) {
                    $where['branch_id'] = array('in', $fw['branch_id']);
                }

                $pbm = D('PartyBranchMembers')->where($where)->select();
                foreach ($pbm as $v) {
                    $user_arr[] = $v['uid'];
                }
            }

            $user_arr = array_unique($user_arr);
            if ($user_arr) {
                $user_str = implode(',', $user_arr);
                $user = M('user')->where(array('uid' => array('in', $user_str)))->select();
                if ($user) {
                    foreach ($user as $v) {
                        $result_user[] = $v['qyuserid'];
                    }
                }
            }
        } else {
            $user_arr = array();
            if ($fw['branch_id']) {
                $user_arr = M('user')->where(array('branch_id' => array('in', $fw['branch_id']), 'status' => 1))->select();
            } else {
                $user_arr = M('user')->where(array('status' => 1, 'uid' => array('neq', 1)))->select();
            }

            if ($fw['role_id']) {
                foreach ($user_arr as $v) {
                    if (strpos($fw['role_id'], $v['role_id']) !== false) {
                        if ($v['qyuserid'] != '') {
                            $result_user[] = $v['qyuserid'];
                        }
                    }
                }
            } else {
                foreach ($user_arr as $v) {
                    if ($v['qyuserid'] != '') {
                        $result_user[] = $v['qyuserid'];
                    }
                }
            }
        }
        //一个uid的一维数组
        $user_list = implode('|', $result_user);
        pushQyWechatMessage($user_list, '您有一条新的文件,请及时查看', '智慧党建平台的推送', $url);
    }
    /**
     * 文件流附件上传
     */
    public function ajaxUploadAppendix()
    {
        # code...
        $res = uploadAnnex('file_wander', 'file_wander', uid());
        if (!empty($res['url'])) {
            $res['type'] = getFileExtension($res['name']);
            if ($res['type'] == 'jpeg') {
                $res['type'] = 'png';
            }
            $res['url'] = $res['url']; //C('DOMAIN').
            ajaxMsg(0, 'success', $res);
        } else {
            ajaxMsg(1, '更新失败' . $res);
        }
    }

    public function addClick($fw_id = '', $uid = '')
    {
        # code...
        if (!$fw_id) {
            return false;
        }
        $user = user();
        if ($uid == $user['uid']) {
            return false;
        }
        $check = D('UserFileWanderClick')->where(array('fw_id' => $fw_id, 'uid' => $user['uid']))->find();
        if ($check) {
            return false;
        }
        $data['uid'] = $user['uid'];
        $data['fw_id'] = $fw_id;
        $data['create_time'] = time();
        M('user_file_wander_click')->add($data);
    }
}
