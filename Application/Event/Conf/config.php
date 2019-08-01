<?php
$appConfig = array(
    'TMPL_CACHE_ON'=>false,
    'HTML_CACHE_ON'=>false,
    'TMPL_PARSE_STRING'  =>array(
        '__JS__' => __ROOT__.'/Public/Event/js/', // js文件位置
        '__IMG__' => __ROOT__.'/Public/Event/img/', // 增加新的JS类库路径替换规则
        '__CSS__' => __ROOT__.'/Public/Event/css/', // 增加新的JS类库路径替换规则
        '__STATICS__' => __ROOT__.'/Statics', // 插件文件位置
        '__UPLOADS__' => __ROOT__.'/Uploads', // 增加新的上传路径替换规则
    ),
    //'SHOW_PAGE_TRACE' =>true,
);
$dbConfig = array(
//    'DB_SQL_BUILD_CACHE' => true,
//    'DB_SQL_BUILD_QUEUE' => 'xcache',
);
return array_merge($appConfig,$dbConfig);