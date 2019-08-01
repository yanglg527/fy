<?php

$appConfig = array(

    //测试开关
    'TEST_ON' => 1,
    //测试配置，如果是测试的话就用这些参数，否则用正式的参数
    //涉及测试和正式的值不一样的在这里定义一下调用
    //调用方法 如: 获取域名 $domain = C('DOMAIN'); 即可
    'TEST_CONFIG' => array(
        //数据库
        'DB_CONFIG' => array(
            'DB_TYPE' => 'mysql',
           'DB_HOST' => 'localhost',
           'DB_NAME' => 'djptnew',
           'DB_USER' => 'root',
           'DB_PWD' => 'root',
 //             'DB_HOST' => '192.168.1.5',
        //  'DB_HOST' => '120.25.64.8',
        //   'DB_NAME' => 'djptnew',
        //   'DB_USER' => 'yanggm',
        //   'DB_PWD' => '123@qwe',
        //     'DB_PORT' => '3306',
        //     'DB_PREFIX' => '',
        ),
        //SESSION名称
        'SESSION_CONFIG' => array(
            'ADMIN_AUTH_SESSION' => 'test_djpt_admin_auth2',
            'ADMIN_SESSION' => 'test_djpt_admin_user2',
            'USER_SESSION' => 'test_djpt_user2'
        ),
        'DOMAIN' => 'www.zh1bit.com/djptNew',
        //微信企业号
        'WEIXINQY_CONFIG' => array(
            'CORP_ID' => 'wx48af8e039f2a38da', // 企业号id
            'SECRET' => 'dpCZINzxcmfMfm3jnxIQv6I0uSvr7uvRnrhwG1Bz_Ts', // 管理组 secret，需要在企业号里面 查看管理组，授权查看通讯录;

            // 'CORP_ID' => 'wxd7890510b3a127e9', // 企业号id
           // 'SECRET' => 'I4_lkc5jUIqh68LHN14tZWsM_ociaOjPcioCr7kc2JI5DJysSzueBl9gVJhd64lZ', // 管理组 secret，需要在企业号里面 查看管理组，授权查看通讯录;
             'DOMAIN' => 'dj.fuyou.cn',
         //   'DOMAIN' => 'www.zh1bit.com/djpt2', // 重定向跳转域名
            'ERROR_URL' => '/Home/Error/index', // 错误重定向页面 请不要携带参数;//不存在该用户，跳转页面，会再参数中?url=urecode(原来的地址)
        ),
        //微信支付定义
        'WEIXINPAY_CONFIG' => array(
            'APPID' => 'wxd7890510b3a127e9', // 微信支付APPID/企业号id
            'MCHID' => '1422838202', // 微信支付MCHID 商户收款账号
            'KEY' => '622848poqwASDyhnfjkadunckjanf454', // 微信支付KEY
            'APPSECRET' => 'I4_lkc5jUIqh68LHN14tZWsM_ociaOjPcioCr7kc2JI5DJysSzueBl9gVJhd64lZ', // 公众帐号secert (公众号支付专用)
            'NOTIFY_URL' => 'http://www.zh1bit.com/djpt2/Weixin/Weixinpay/notify', // 接收支付状态的连接
        ),
        //微信公众号 网页版
        'WEIXINGZHWEB_CONFIG' => array(
            'APP_ID' => 'wx14f422cb31f8bf8fb', // 企业号id
            'SECRET' => 'ee8687b87caf0371aa7f45f843885f56d9', // 管理组 secret，需要在企业号里面 查看管理组，授权查看通讯录;
            'DOMAIN' => 'www.zh1bit.com/djpt2', // 重定向跳转域名
            'ERROR_URL' => '/Home/Error/weixin', // 错误重定向页面 请不要携带参数;//不存在该用户，跳转页面，会再参数中?url=urecode(原来的地址)
        ),
        //阿里大于
        'ALIDAYU_CONFIG' => array(
            'sms_product' => '智慧建设', //
            'sms_appkey' => '23587683', //
            'sms_secretKey' => '4923f33b9aa591e19a04836c6d81ce0e', //
            'sms_templateCode' => 'SMS_37580245', //
        ),
    ),
    //正式配置
    'FORMAL_CONFIG' => array(
        //数据库
        'DB_CONFIG' => array(
            'DB_TYPE' => 'mysql',
//    'DB_HOST' => '127.0.0.1',
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'djptnew',
            'DB_USER' => 'root',
            'DB_PWD' => 'root',
            'DB_PORT' => '3306',
            'DB_PREFIX' => '',
        ),
        //SESSION名称
        'SESSION_CONFIG' => array(
            'ADMIN_AUTH_SESSION' => 'djpt_admin_authr',
            'ADMIN_SESSION' => 'djpt_admin_userr',
            'USER_SESSION' => 'djpt_userr'
        ),
        'DOMAIN' => 'djpt.zhzy.net.cn',
        'WEIXINGZH_IS_UNIONID'=>1,
        //微信企业号
        'WEIXINQY_CONFIG' => array(
            'CORP_ID' => 'wx48af8e039f2a38da', // 企业号id
            'SECRET' => 'dpCZINzxcmfMfm3jnxIQv6I0uSvr7uvRnrhwG1Bz_Ts', // 管理组 secret，需要在企业号里面 查看管理组，授权查看通讯录;

            'DOMAIN' => 'dj.fuyou.cn', // 重定向跳转域名
            'ERROR_URL' => '/Home/Error/index', // 错误重定向页面 请不要携带参数;//不存在该用户，跳转页面，会再参数中?url=urecode(原来的地址)
        ),
//微信公众号 网页版
        'WEIXINGZHWEB_CONFIG' => array(
            'APP_ID' => 'wx14f422cb31f8bf8fb', // 企业号id
            'SECRET' => 'ee8687b87caf0371aa7f45f843885f56d9', // 管理组 secret，需要在企业号里面 查看管理组，授权查看通讯录;
            'DOMAIN' => 'dj.zhgs.gov.cn', // 重定向跳转域名
            'ERROR_URL' => '/Home/Error/weixin', // 错误重定向页面 请不要携带参数;//不存在该用户，跳转页面，会再参数中?url=urecode(原来的地址)
        ),
        //微信支付定义
        'WEIXINPAY_CONFIG' => array(
            'APPID' => 'wxd7890510b3a127e9', // 微信支付APPID/企业号id
            'MCHID' => '1422838202', // 微信支付MCHID 商户收款账号
            'KEY' => '622848poqwASDyhnfjkadunckjanf454', // 微信支付KEY
            'APPSECRET' => 'I4_lkc5jUIqh68LHN14tZWsM_ociaOjPcioCr7kc2JI5DJysSzueBl9gVJhd64lZ', // 公众帐号secert (公众号支付专用)
            'NOTIFY_URL' => 'http://dj.zhgs.gov.cn/Weixin/Weixinpay/notify', // 接收支付状态的连接
        ),
        //阿里大于
        'ALIDAYU_CONFIG' => array(
            'sms_product' => '智慧建设', //
            'sms_appkey' => '23587683', //
            'sms_secretKey' => '4923f33b9aa591e19a04836c6d81ce0e', //
            'sms_templateCode' => 'SMS_37580245', //
        ),
    ),






    'SHOW_PAGE_TRACE' => false,
    'LOG_RECORD' => true, // only if the debug model is true will take effect
    'MODULE_ALLOW_LIST' => array(
        'Api','Home', 'Admin', 'Event', 'Test', 'Questionnaire', 'Vote', 'SignIn', 'Weixin', 'Mail', 'JoinApply', 'Todo','Mob'
    ),
    'TMPL_PARSE_STRING' => array(
        '__JS__' => __ROOT__ . '/Public/Common/js/', // js文件位置
        '__IMG__' => __ROOT__ . '/Public/Common/img/', // 增加新的JS类库路径替换规则
        '__CSS__' => __ROOT__ . '/Public/Common/css/', // 增加新的JS类库路径替换规则
        '__STATICS__' => __ROOT__ . '/Statics', // 插件文件位置
        '__UPLOAD__' => __ROOT__ . '/Uploads', // 增加新的上传路径替换规则
    ),
    'DEFAULT_MODULE' => 'Home',
    'SESSION_OPTIONS' => array(
        'expire' => 3600 * 24 * 10
    ),
    'SESSION_AUTO_START' => true,
    'LANG_AUTO_DETECT' => true,
    'VAR_LANGUAGE' => 'l', // 默认语言切换变量
    //paging item
    'LIMITITEM' => 20,

    // prevent XSS attack
    'DEFAULT_FILTER' => 'htmlentities',

    // use rewrite
    'URL_MODEL' => 2,
    'DEFAULT_TIMEZONE' => 'PRC', //设置时区为中国+8区
    'TMPL_CACHE_ON' => false,
    'HTML_CACHE_ON' => false,


    'AUTH_CONFIG' => array(
        'AUTH_ON' => true, //认证开关
        'AUTH_TYPE' => 1, // 认证方式，1为时时认证；2为登录认证。
        'AUTH_GROUP' => 'admin_auth_group', //用户组数据表名
        'AUTH_GROUP_ACCESS' => 'admin_auth_group_access', //用户组明细表
        'AUTH_RULE' => 'admin_auth_rule', //权限规则表
        'AUTH_USER' => 'user',//用户信息表
    ),


    //权限 id 定义
    'ADMINISTRATOR' => array('1'),
    'ADMIN_GROUP_ID' => '1',//管理员 groupId 使用方法 C('xxxx')
    'BRANCH_GROUP_ID' => '4',//支部 groupId
    'ACTIVIST_GROUP_ID' => '3',//积极分子 groupId
    "PARTY_GROUP_ID" => "2",//党员 groupId
    "NORMAL_GROUP_ID" => "5",//普通成员 groupId

    //角色 id 定义
    'ROLE_NORMAL_ID' => 5,//群众
    'ROLE_ACTIVITE_ID' => 4,//积极分子
    'ROLE_TOBE_ID' => 3,//拟发展对象
    'ROLE_READY_ID' => 2,//预备党员
    'ROLE_PARTY_ID' => 1,//党员

    //默认支部
    'BRANCH_DEFAULT_ID' => 1,

    //默认支部
    'POST_SJ_ID' => 1,


);
if ($appConfig['TEST_ON']) {//测试环境
    $dbConfig = $appConfig['TEST_CONFIG']['DB_CONFIG'];

    $appConfig2 = $appConfig['TEST_CONFIG'];
} else {
    $dbConfig = $appConfig['FORMAL_CONFIG']['DB_CONFIG'];
    $appConfig2 = $appConfig['FORMAL_CONFIG'];
}

$second_tags = include "second.php";
return array_merge(
    $dbConfig,
    $appConfig,
    $appConfig2,
    $second_tags
);