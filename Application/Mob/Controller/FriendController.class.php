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

class FriendController extends BaseAuthController
{

    function _initialize()
    {
        parent::_initialize();
    }


    //信箱列表
    public function index()
    {
        $this->check_wx_redirect('mob_party_member_index');//判断是否重定向登录
        $type = I('type', 'all');
        $user = user();
        $sql = "select 
              (select count(*) from  user_friends ufs2 where ufs.friend_uid=ufs2.uid) as uf_count,
              (select count(*) from taizhang tz where tz.uid=ufs.friend_uid and tz.status=1) as tz_count, ufs.friend_uid
                        from user_friends ufs where ufs.uid={$user['uid']} order by follow_time desc";
        $this->setWebTitle("好友列表");

        $result = D()->query($sql);
        echo to_json_string($result);
        $this->assign('list', $result);
        // $this->display();
    }

    public function ajaxFollow()
    {
        $uid = I('uid');
        if ($uid) {
            $friend = D('UserFriends')->where(array('uid' => uid(), 'friend_uid' => $uid))->find();
            if ($friend) {
                D('UserFriends')->where(array('uid' => uid(), 'friend_uid' => $uid))->save(array('status' => 1,'follow_time'=>time()));
            } else {
                D('UserFriends')->add(array('uid' => uid(), 'friend_uid' => $uid, 'status' => 1,'follow_time'=>time()));
            }
        }
        ajaxMsg(0, '');
    }

    public function ajaxCancelFollow()
    {
        $uid = I('uid');
        if ($uid) {
            $friend = D('UserFriends')->where(array('uid' => uid(), 'friend_uid' => $uid))->find();
            if ($friend) {
                D('UserFriends')->where(array('uid' => uid(), 'friend_uid' => $uid))->save(array('status' => -1));
            }
        }
        ajaxMsg(0, '');
    }


}