<?php

namespace Weixin\Util;

use Weixin\Sdk\JSSDK;
use Weixin\Sdk\WechatAuth;

/**
 * Class GZHUtils
 * @package Weixin\Util
 *
 *
 *
 */
class GZHUtils
{

    public static $menuType = array(
        'click' => '点击',
        'sub_button' => '父菜单',
        'view' => '网站'
    );

    function _initialize()
    {

    }

    /**
     * @param $corpID
     * @param $secret
     * {
     * "access_token": "accesstoken000001",
     * "expires_in": 7200
     * "updateTime":time()
     * }
     */
    static function get_access_token($appid, $secret)
    {
//        $token = D('WeixinConfig')->where("name='gzh_access_token'")->find();
        $token = S('gzh_access_token_'.$appid);
        if (!$token) {
            $token = GZHUtils::_reget_access_token($appid, $secret);
        }
        return $token;
//
//        if ($token) {
//            $token['value'] = $token['value'] ? $token['value'] : "{}";
//            $jsonToken = to_json_obj($token['value']);
//            //access_token未失效
//            if ($jsonToken['updateTime'] && (time() - $jsonToken['updateTime'] < ($jsonToken['expires_in'] - 10))) {
//                return $jsonToken['access_token'];
//            } else {//获取 accessToken
//                return GZHUtils::_reget_access_token($appid, $secret);
//            }
//        } else {
//            return false;
//        }
    }

    static function _reget_access_token($appid, $secret)
    {

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
        $result = GZHUtils::wx_http_get($url);
        $jsonToken = to_json_obj($result);
        if ($jsonToken['access_token']) {
            S('gzh_access_token_'.$appid, $jsonToken['access_token'], $jsonToken['expires_in'] - 10);

            //统计调用次数,统计有没有错，验证后删除
//            $count = D('WeixinConfig')->where("name='gzh_access_token'")->find();
//            $useSize = $count['value'];
//            $useSize = $useSize ? $useSize + 1 : 1;
//            D('WeixinConfig')->where("name='gzh_access_token'")->save(array('value'=>$useSize));
            //            $jsonToken['updateTime'] = time();
//            $token['value'] = to_json_string($jsonToken);
            return $jsonToken['access_token'];
        } else {
            return false;
        }
    }

    static function wx_http_get($url)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 40);   //只需要设置一个秒的数量就可以
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }


    //获取用户信息。注册用户，并返回用户
    static function get_web_auth_user_info($code, $getType = 'wx')
    {
        if ($getType == 'wx') {//公众授权
            $config = C('WEIXINGZH_CONFIG');
        } else {//网页授权
            $config = C('WEIXINGZHWEB_CONFIG');
        }

        $appid = $config['APP_ID'];
        $secret = $config['SECRET'];

        //获取 access_token 和基础 access_token不同
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";
        $result = wx_http_get($url);
        $jsonToken = to_json_obj($result);


        $isunionid = C('WEIXINGZH_IS_UNIONID');
        $openid = $jsonToken['openid'];
        if ($isunionid) {//如果是开放平台共用的用户
            if ($getType == 'wx') {
                $user = D('User')->where(array('unionid' => $openid))->find();
            } else {
                $wxUser = D('WeixinUser')->where(array('gzh_web_openid' => $openid))->find();
            }
        } else {
            $wxUser = D('WeixinUser')->where(array('gzh_openid' => $openid))->find();
        }
//        exit(to_json_string(array('gzh_web_openid'=>$openid)));

        if ($wxUser) {//已经有微信用户信息了  返回用户
            $user = D('Common/UserView')->where(array('User.uid' => $wxUser['uid']))->find();

        } else {//没有微信用户信息  注册新用户
            //获取用户信息
            $url2 = "https://api.weixin.qq.com/sns/userinfo?access_token={$jsonToken['access_token']}&openid={$jsonToken['openid']}&lang=zh_CN";
            $result2 = wx_http_get($url2);
            $info = to_json_obj($result2);


            $info['nickname'] = GZHUtils::filter($info['nickname']);

            if ($isunionid) {//如果是开放平台共用的用户
                $unionid = $info['unionid'];
                if (!$info['unionid'] && $getType = 'wx') {
                    redirect(__ROOT__ . $config['ERROR_URL'] . "?title=该公众号尚未绑定开放平台&content=该公众号尚未绑定开放平台");
                }
                $wxUser = D('WeixinUser')->where(array('gzh_unionid' => $info['unionid']))->find();

                if ($wxUser && $getType == 'wx') {//如果没有公众号 openid 则更新
                    GZHUtils::update_openid_by_unionid($info['openid'], $unionid, 'wx');
                }
                if ($wxUser && $getType == 'web') {//如果没有公众号 openid 则更新
                    GZHUtils::update_openid_by_unionid($info['openid'], $unionid, 'web');
                }
            }
            if (!$info['openid']) {
                redirect(__ROOT__ . $config['ERROR_URL'] . "?title=登录错误，请返回重试&content=登录错误，请返回重试");
            }
            if (!$wxUser && $info['openid']) {
                //注册新用户
                $uid = GZHUtils::register_user($info, $getType);
                $user = D('Common/UserView')->where(array('User.uid' => $uid))->find();
            } else {
                $user = D('Common/UserView')->where(array('User.uid' => $wxUser['uid']))->find();
            }

        }
//        exit(to_json_string($user));
        return $user;
    }

    static function update_openid_by_unionid($openid, $unionid, $type = 'wx')
    {
        if ($type == 'wx') {
            $arr = array('gzh_openid' => $openid);
        } else {
            $arr = array('gzh_web_openid' => $openid);
        }

        D('WeixinUser')->where(array('gzh_unionid' => $unionid))->save($arr);

    }

    static public function filter($str)
    {
        if ($str) {
            $name = $str;
            $name = preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '', $name);
            $name = preg_replace('/xE0[x80-x9F][x80-xBF]‘.‘|xED[xA0-xBF][x80-xBF]/S', '?', $name);
            $return = json_decode(preg_replace("#(\\\ud[0-9a-f]{3})#ie", "", json_encode($name)));
            if (!$return) {
                return '';
            }
        } else {
            $return = '';
        }
        return $return;

    }


    static function register_user($userInfo, $getType = 'wx')
    {
        $uid = UserUtil(1, $userInfo['openid'], time(), $userInfo['nickname'], $userInfo['headimgurl'], $userInfo['sex']);
        if ($uid) {
            $arr = array(
                'headimgurl' => $userInfo['headimgurl'],
                'gzh_unionid' => $userInfo['unionid'],
                'sex' => $userInfo['sex'],
                'city' => $userInfo['city'],
                'uid' => $uid,
                'nickname' => $userInfo['nickname']);
            if ($getType == 'wx') {
                $arr['gzh_openid'] = $userInfo['openid'];
            } else {
                $arr['gzh_web_openid'] = $userInfo['openid'];
            }
            D('WeixinUser')->add($arr);
            return $uid;
        } else {
            return false;
        }


    }


    //创建公众号菜单
    static public function make_menu()
    {
        $menuType = array(
            'click' => 'key',
            'view' => 'url',
            'media_id' => 'media_id',
            'view_limited' => 'media_id',
            'sub_button' => 'sub_button'
        );


        $menuDao = D('WeixinMenu');
        $menus = $menuDao->where('pid is null and status=1')->order('sort asc')->select();
        $returnMenus = array();

        foreach ($menus as $menu) {
            $type = $menu['type'];
            $valueName = $menuType[$type];

            $returnMenu = array();
            if ($valueName == 'sub_button') {
                $subMenus = $menuDao->where(array('pid' => $menu['id'], 'status' => 1))->order('sort asc')->select();

                $returnSubMenus = array();
                foreach ($subMenus as $subMenu) {

                    $type = $subMenu['type'];
                    $valueName = $menuType[$type];
                    $returnSubMenu['type'] = $type;
                    $returnSubMenu[$valueName] = $subMenu['value'];
                    $returnSubMenu['name'] = $subMenu['name'];
                    array_push($returnSubMenus, $returnSubMenu);
                }

                $returnMenu['sub_button'] = $returnSubMenus;
                $returnMenu['name'] = $menu['name'];
                array_push($returnMenus, $returnMenu);
            } else {
                $returnMenu['type'] = $type;
                $returnMenu[$valueName] = $menu['value'];
                $returnMenu['name'] = $menu['name'];
                array_push($returnMenus, $returnMenu);
            }


        }
        $config = C('WEIXINGZH_CONFIG');

        $wechatAuth = new WechatAuth($config['APP_ID'], $config['SECRET'], GZHUtils::get_access_token($config['APP_ID'], $config['SECRET']));

        $status = $wechatAuth->menuCreate($returnMenus);


        return $status;
    }


    static function is_weixin()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }

    static function get_jssdk()
    {

        $config = C('WEIXINGZH_CONFIG');
        $appid = $config['APP_ID'];
        $secret = $config['SECRET'];
        $jssdk = new JSSDK($appid, $secret, GZHUtils::get_access_token($appid, $secret));
        $signPackage = $jssdk->GetSignPackage();
        return $signPackage;
    }

    /**
     * @param $uids id1,id2,id3,...
     * @param $type text|news
     */
    static function sendMessage($uids, $msgId)
    {
        $sMsg = D('WeixinSendMessage')->find($msgId);

        $type = $sMsg['type'];

        $weixinUsers = D('WeixinUser')->where(array('uid' => array('in', $uids)))->select();
        $openids = array();
        foreach ($weixinUsers as $wuser) {
            if ($wuser['gzh_openid']) {
                $openids[] = $wuser['gzh_openid'];
            }
        }
        $info['openids'] = $openids;
        $info['type'] = $type;

        if ($type == 'text') {
            $info['content'] = $sMsg['content'];
        } elseif ($type == 'news') {
            $msgs = D('WeixinNews')->where(array('id' => array('in', $sMsg['news_ids'])))->order("field(id,{$sMsg['news_ids']})")->select();
            if (count($msgs) > 0) {//如果新闻是多条的话
                $replyNews = array();
                foreach ($msgs as $newss) {
                    $replyNews[] = array($newss['title'], $newss['description'], GZHUtils::url($newss['url']), $newss['picurl']);
                }
                $info['content'] = $replyNews;
            } else {
                $info['type'] = 'text';
                $info['content'] = '没有查询到相关信息';
            }

        } else {
            $info['type'] = 'text';
            $info['content'] = '没有查询到相关信息';
        }

        return GZHUtils::sendMessageToCustom($info);


    }

    static function url($url, $str)
    {
        $arr = explode("?", $url);
        if (sizeof($arr) > 1) {
            return $url . '&' . $str;
        }
        return $url . '?' . $str;
    }

    /**
     * @param $info
     * @return array
     * $info格式如下 需要转json：
     * 文本：
     * array(
     *      "openids"=> array(id,id2,id3...),
     *      "type"=>"text",
     *      "content"=>"内容"
     * )
     * 图文：
     * array(
     *      "openids"=> array(id,id2,id3...),
     *      "type"=>"text",
     *      "content"=>array(
     *           标题,描述,连接,封面(建议第一个360x200其他200x200)
     *      )
     *  )
     */
    static function sendMessageToCustom($info)
    {
        $config = C('WEIXINGZH_CONFIG');
        $appid = $config['APP_ID'];
        $secret = $config['SECRET'];

        $return = array();
        $openids = $info['openids'];
        foreach ($openids as $openid) {
            $type = $info['type'];
            $content = $info['content'];
//            $content = str_replace("\\/", "/", $content);
            $token = GZHUtils::get_access_token($appid, $secret);
            $wechatAuth = new WechatAuth($appid, $secret, $token);
//        $wechatAuth->sendNewsOnce($openid,'标题','描述','http://m.baidu.com','http://m.baidu.com');
//ajaxMsg(1,to_json_string($content[0]));

            if ($type == WechatAuth::MSG_TYPE_NEWS) {
                if (count($content) == 1) {
                    $status = $wechatAuth->sendNews($openid, $content[0]);
                } elseif (count($content) == 2) {
                    $status = $wechatAuth->sendNews($openid, $content[0], $content[1]);
                } elseif (count($content) == 3) {
                    $status = $wechatAuth->sendNews($openid, $content[0], $content[1], $content[2]);
                } elseif (count($content) == 4) {
                    $status = $wechatAuth->sendNews($openid, $content[0], $content[1], $content[2], $content[3]);
                } elseif (count($content) > 4) {
                    $status = $wechatAuth->sendNews($openid, $content[0], $content[1], $content[2], $content[3], $content[4]);
                } else {

                }
            } else {
                $status = $wechatAuth->sendText($openid, $content);
            }
            if ($status['errcode'] == 0) {
                array_push($return['success'], $openids);
            } else {
                array_push($return['fail'], $openids);
            }
        }
        return $return;
    }

    static function publish_to_GZH($gid, $previewOpenid)
    {
        $config = C('WEIXINGZH_CONFIG');
        $appid = $config['APP_ID'];
        $secret = $config['SECRET'];
        $access_token = GZHUtils::get_access_token($appid, $secret);
        $group = D('WeixinMediaArticles')->find($gid);
        $media_id = $group['media_id'];

        //测试预览
        if ($previewOpenid) {
            $data = array('touser' => $previewOpenid, 'mpnews' => array('media_id' => $media_id), 'msgtype' => 'mpnews');
            $url = "https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=$access_token";
        } else {
            $data = array('filter' => array('is_to_all' => true), 'mpnews' => array('media_id' => $media_id), 'msgtype' => 'mpnews');
            $url = "https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=$access_token";
        }


        $result = GZHUtils::wx_http_post($url, to_json_string($data));
        $result = to_json_obj($result);
//        ajaxMsg(1,$result);
        if ($result['errcode'] == 0) {
            return true;
        } else {
            ajaxMsg(1, "发送失败");
        }
    }

    static function add_or_save_media_article($gid, $aid)
    {
        $config = C('WEIXINGZH_CONFIG');
        $appid = $config['APP_ID'];
        $secret = $config['SECRET'];
        $access_token = GZHUtils::get_access_token($appid, $secret);
        $group = D('WeixinMediaArticles')->find($gid);
        if (!$aid) {//添加到微信素材
            $url = "https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=$access_token";
            if (!$group['media_ids']) {
                ajaxMsg(1, "没有设置文章");
            }

            $list = D('WeixinMediaArticle')->where(array('id' => array('in', $group['media_ids'])))->order('create_time desc')->select();
            $data = array('articles' => $list);
            $result = GZHUtils::wx_http_post($url, to_json_string($data));

            if ($result) {
                $result = to_json_obj($result);
                $media_id = $result['media_id'];
                D('WeixinMediaArticles')->where(array('id' => $gid))->save(array('media_id' => $media_id));
            } else {
                ajaxMsg(1, '添加失败');
            }
        } else {
            $media_id = $group['media_id'];
            $url = "https://api.weixin.qq.com/cgi-bin/material/update_news?access_token=$access_token";
            $ids = $group['media_ids'];
            $ids = explode(',', $ids);
            $index = 0;
            foreach ($ids as $i => $dd) {
                if ($aid == $dd) {
                    $index = $i;
                }
            }
            $detail = D('WeixinMediaArticle')->find($aid);
            $data = array('media_id' => $media_id, 'index' => $index, 'articles' => $detail);
            $result = GZHUtils::wx_http_post($url, to_json_string($data));
//            ajaxMsg(1,$result);
        }
    }


    static function upload_media_file($real_path, $file_name, $file_length, $type)
    {
        $config = C('WEIXINGZH_CONFIG');
        $appid = $config['APP_ID'];
        $secret = $config['SECRET'];
        $access_token = GZHUtils::get_access_token($appid, $secret);
        $url = "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token={$access_token}&type={$type}";
        $curl = curl_init();
        $timeout = 200;
        $real_path = "{$real_path}";
        $file_info = array('filename' => $file_name, 'content-type' => 'application/octet-stream', 'filelength' => $file_length);


        if (class_exists('\CURLFile')) {
            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
//            $data = array('file' => new \CURLFile(realpath($path)));//>=5.5
            $data = array("media" => new \CURLFile($real_path), "form-data" => $file_info);
        } else {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
            }
            $data = array("media" => "@{$real_path}", "form-data" => $file_info);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);

        curl_close($curl);
        if (curl_errno() == 0) {
            $result = to_json_obj($result);
            if ($result['errcode'] > 0) {
                return false;
            }
            return $result;
        } else {
            return false;
        }
    }

    /**
     * @param $path
     * @param $file_info
     * @return bool
     */
    static function upload_thumb($name, $real_path, $file_name, $file_length)
    {
        $info = GZHUtils::upload_media_file($real_path, $file_name, $file_length, 'thumb');
        if ($info) {
            $media_id = $info['media_id'];
            $file = array('weixin_url' => $info['url'], 'local_path' => $file_name, 'media_id' => $media_id, 'type' => 'thumb', 'name' => $name, 'create_time' => time());
            D('WeixinMediaFile')->add($file);
//                ajaxMsg(1, to_json_string($file));
            return $file;
        } else {
            ajaxMsg(1, "上传失败");
        }
    }

    static function upload_image($name, $real_path, $file_name, $file_length)
    {
        $info = GZHUtils::upload_media_file($real_path, $file_name, $file_length, 'thumb');
        if ($info) {
            $media_id = $info['media_id'];
            $file = array('weixin_url' => $info['url'], 'local_path' => $file_name, 'media_id' => $media_id, 'type' => 'image', 'name' => $name, 'create_time' => time());
            D('WeixinMediaFile')->add($file);
            return $file;
        } else {
            ajaxMsg(1, "上传失败");
        }
    }

    static function delete_media($media_id)
    {
        $config = C('WEIXINGZH_CONFIG');
        $appid = $config['APP_ID'];
        $secret = $config['SECRET'];
        $access_token = GZHUtils::get_access_token($appid, $secret);
        $result = GZHUtils::wx_http_post("https://api.weixin.qq.com/cgi-bin/material/del_material?access_token={$access_token}", to_json_string(array('media_id' => $media_id)));
        $obj = to_json_obj($result);
        return $obj['errcode'];
    }

    static function wx_http_post($url, $data)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 30);   //只需要设置一个秒的数量就可以
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, 1);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $data);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * @param $ids
     * @param $temple_num
     * @param $title
     * @param $arryKeywords['1','2','3',......]
     * @param $remark
     * @return array
     */
    public static function  sendTemplet($uid,$temple_num,$title,$url, $arryKeywords,$remark){

        //获取模板
        $temple = D('WeixinTemple')->where("num='$temple_num'")->find();
        $temple['url'] =$url;
        $templeData = $temple['data'];
        $templeDTJ = to_json_obj($templeData, true);
        $templeDTJ['first']['value'] = $title;
        foreach ($arryKeywords as $index=>$keyword){
            $templeDTJ['keyword'.($index + 1)]['value'] = $keyword;
        }
        $templeDTJ['remark']['value'] = $remark;
        $temple['data'] = $templeDTJ;

        $config = C('WEIXINGZH_CONFIG');
        $appid = $config['APP_ID'];
        $secret = $config['SECRET'];

        $content = $temple ;
        $return = array();

        $weixinuser = D('WeixinUser')->where(array('uid'=>$uid))->find();
        if($weixinuser){
            $openid = $weixinuser['gzh_openid'];
        }else{
            return false;
        }
            $token = GZHUtils::get_access_token($appid,$secret);

            $wechatAuth = new WechatAuth($appid, $secret, $token);

            $status =  $wechatAuth->sendTemple($openid, $content);
            if($uid == 1464)
                ajaxMsg(1,$status);
            if($status['errcode'] == 0){
            }else{
            }

        return $return;
    }


}
