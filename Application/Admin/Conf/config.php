<?php
$appConfig = array(
    'TMPL_PARSE_STRING'  =>array(
        '__JS__' => __ROOT__.'/Public/Admin/js/', // js文件位置
        '__IMG__' => __ROOT__.'/Public/Admin/img/', // 增加新的JS类库路径替换规则
        '__CSS__' => __ROOT__.'/Public/Admin/css/', // 增加新的JS类库路径替换规则
        '__STATICS__' => __ROOT__.'/Statics', // 插件文件位置
        '__UPLOAD__' => __ROOT__.'/Uploads', // 增加新的上传路径替换规则
    ),
    'TMPL_CACHE_ON'=>false,
    'HTML_CACHE_ON'=>false,
    //认证开关
    'AUTH_ON'=>true,
    'AUTH_TYPE'=>1,
    'AUTH_GROUP'=>'admin_auth_group',
    'AUTH_GROUP_ACCESS'=>'admin_auth_group_access',
    'AUTH_RULE'=>'admin_auth_rule',
    'AUTH_USER'=>'user',
    'ADMINISTRATOR'=>array('1'),
    'taizhang_type'=>array(
        array('type_id'=>1,'type_name'=>'个人台账'),
        array('type_id'=>2,'type_name'=>'党委台账'),
        array('type_id'=>3,'type_name'=>'领导台账'),
        array('type_id'=>4,'type_name'=>'支部台账'),

    ),
    'tztype'=>array(
        '1' =>'person',
        '2' =>'organization',
        '3'=>'leader',
        '4'=>'branch',
    ),
    'tzpath' =>'.'.__ROOT__.'/Uploads/newtz/', //放到服务器要把点去掉
);
$dbConfig = array(
);
return array_merge($appConfig,$dbConfig);