<?php
$appConfig = array(
    'TMPL_CACHE_ON'=>false,
    'HTML_CACHE_ON'=>false,
    'TMPL_PARSE_STRING'  =>array(
        '__JS__' => __ROOT__.'/Public/Mob/js/', // js文件位置
        '__IMG__' => __ROOT__.'/Public/Mob/images/', // 增加新的JS类库路径替换规则
        '__CSS__' => __ROOT__.'/Public/Mob/css/', // 增加新的JS类库路径替换规则
        '__STATICS__' => __ROOT__.'/Statics', // 插件文件位置
        '__UPLOADS__' => __ROOT__.'/Uploads', // 增加新的上传路径替换规则
      //  1 个人台账 2 党组台账 3 领导台账 4 支部台账

    ),
    'taizhang_type'=>array(
        array('type_id'=>1,'type_name'=>'个人台账'),
        array('type_id'=>2,'type_name'=>'党委台账'),
        // array('type_id'=>3,'type_name'=>'领导台账'),
        array('type_id'=>4,'type_name'=>'支部台账'),
        array('type_id'=>5,'type_name'=>'党小组台账'),
    ),
    'tztype'=>array(
        '1' =>'person',
        '2' =>'organization',
        '3'=>'leader',
        '4'=>'branch',
    ),
    'tzpath' =>'.'.__ROOT__.'/Uploads/newtz/', //放到服务器要把点去掉
    //'SHOW_PAGE_TRACE' =>true,
);
$dbConfig = array(
//    'DB_SQL_BUILD_CACHE' => true,
//    'DB_SQL_BUILD_QUEUE' => 'xcache',
);

return array_merge($appConfig,$dbConfig);