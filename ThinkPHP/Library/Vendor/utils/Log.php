<?php
require_once 'Tools.php';
class Log{
    static function outLog($api_n,$content,$type){
        $time=Tools::getTime("Y年m月d日G时i分s秒x毫秒");
        $log_str="$time   $api_n\n$content\n------------------\n";
        $file_n="Log__".date("Ymd").".txt";
        $path = realpath(__ROOT__)."/Runtime/Logs/".$type;
        if (! file_exists ( $path )) {
           mkdir ( "$path", 0777, true );
        }
        $file=fopen($path."/$file_n", "a+");
        fwrite($file, $log_str);
        fclose($file);
    }
}
