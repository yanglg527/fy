<?php
namespace Admin\Controller;

use Common\Controller\BaseController;
use Think\Controller;
use Think\Image;

/**
 * 图片上传
 * Class ImageController
 * @package Home\Controller
 */
class FileController extends BaseAdminController//继承 baseHomeController，统一设置用户信息、顶部栏信息
{

    //上传视频文件或压缩文件zip包，分段上传
    public function upload(){
//        die('{"jsonrpc" : "2.1", "result" : '.to_json_string($_POST).', "id" : "id"}');
        // Make sure file is not cached (as it happens for example on iOS devices)
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        // Support CORS
        // header("Access-Control-Allow-Origin: *");
        // other CORS headers if any...
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            ajaxMsg(1,"上传失败");
            exit; // finish preflight CORS requests here
        }
        if ( !empty($_REQUEST[ 'debug' ]) ) {
            $random = rand(0, intval($_REQUEST[ 'debug' ]) );
            if ( $random === 0 ) {
                header("HTTP/1.0 500 Internal Server Error");
                ajaxMsg(1,"上传失败");
            }
        }

        // header("HTTP/1.0 500 Internal Server Error");
        // exit;
        // 5 minutes execution time
        @set_time_limit(10 * 60);
        // Uncomment this one to fake upload time
        // usleep(5000);
        // Settings
        // $targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
        mkDirs("./Uploads/Files/");
        mkDirs("./Uploads/Files/temp/");
        mkDirs("./Uploads/Files/cover/");
        $targetDir = './Uploads/Files/temp/';
        $uploadDir = './Uploads/Files/';
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds
        // Get a file name
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
        } else {
            $fileName = uniqid("file_");
        }

        // Chunking might be enabled
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;
        $newname = time().'-'.mt_rand();
        $oldext = substr(strrchr($fileName, '.'), 1);
//        $oldext = "zip";

        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
        $fileName = $newname.'.'.$oldext;
        $uploadPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        // Remove old temp files
        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                ajaxMsg(1,"Failed to open temp directory");
//                die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
            }
            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
                // If temp file is current file proceed to the next
                if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
                    continue;
                }
                // Remove temp file if it is older than the max age and is not the current file
                if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }
        // Open temp file
        if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {

            ajaxMsg(1,"Failed to open output stream");
//            die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }

        if (!empty($_FILES)) {
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                ajaxMsg(1,"Failed to move uploaded file");
//                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
            }
            // Read binary input stream and append it to temp file
            if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                ajaxMsg(1,"Failed to open input stream");
//                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        } else {
            if (!$in = @fopen("php://input", "rb")) {
                ajaxMsg(1,"Failed to open input stream");
//                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        }
        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($out);
        @fclose($in);
        rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");
        $index = 0;
        $done = true;
        for( $index = 0; $index < $chunks; $index++ ) {
            if ( !file_exists("{$filePath}_{$index}.part") ) {
                $done = false;
                break;
            }
        }

        if ( $done ) {
            if (!$out = @fopen($uploadPath, "wb")) {
                ajaxMsg(1,"Failed to open input stream");
//                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            }
            if ( flock($out, LOCK_EX) ) {
                for( $index = 0; $index < $chunks; $index++ ) {
                    if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {
                        break;
                    }
                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }
                    @fclose($in);
                    @unlink("{$filePath}_{$index}.part");
                }
                flock($out, LOCK_UN);
            }
            @fclose($out);
        }


        // Return Success JSON-RPC response

        if ( $done ) {
            $mpeg = ".avi,.mp4,.flv,.mov,.mpg,.wmv";
            $zips = "zip,ZIP";
            if(strpos($mpeg,$oldext)){
                //echo('{"code": 301, "message": "上传成功,转换格式中"}');-y -ab 56 -qscale 5 -ar 22050
                // swf: mp4 to swf -> -y -ab 32 -ar 22050 -qscale 10  -r 15
                //mp4 : mp4 to mp4 ->  -c:v libx264 -strict -2
                //      wmv to mp4 ->  -c:v libx264 -strict -2
                $done = false;
                $cmd = 'ffmpeg -i /var/www/html'.__ROOT__ .'/Uploads/Files/'.$fileName.' -c:v libx264 -strict -2 /var/www/html'.__ROOT__ .'/Uploads/Files/'.$newname.'_z.mp4';
                exec($cmd, $status);//这里status=1表示有错，和ajax返回的相反。
//                ajaxMsg(1,"转换格式失败");
                if($status){
                    ajaxMsg(1,"转换格式失败");
//                    die('{"code": 0, "message": "转换格式失败'. $status.'"}');
                }

                //echo('{"code": 302, "message": "转换成功,正在截图"}');
                $cmd = 'ffmpeg -ss 0 -i /var/www/html'.__ROOT__ .'/Uploads/Files/'.$fileName.' -vframes 1 -f image2 -y /var/www/html'.__ROOT__ .'/Uploads/Files/cover/'.$newname.'.jpg';
                //echo $cmd;
                exec($cmd, $status);
                //@unlink('/var/www/web/Uploads/weikeyun/'.$fileName);
                @unlink('/var/www/html'.__ROOT__ .'/Uploads/Files/'.$fileName);
                //将文件信息存入数据库

                //文件上传记录
                $file = array(
                    'savename' => $newname,
                    'ext' => 'video/'.$oldext,
                    'type' => 'video',
                    'savepath' => $uploadPath,
                    'uid' => admin_uid(),
                    'size' => 0,
                    'create_time' => time()
                );
                $Filelist = D('Filelist');
                $Filelist->add($file);
                $done = true;
                ajaxMsg(0,'处理完成',array('path'=>__ROOT__.'/Uploads/Files/'.$newname.'_z.mp4'));
//                echo('{"code": 1, "message": "处理完成"}');
            }elseif(strpos($zips,$oldext)){
                $zip = new \ZipArchive(); //首先实例化这个类
                if ($zip->open(C('UPLOADPATH').'weikeyun/'.$fileName) === TRUE) {
                    $t = $zip->getFromName('main.htm');//判断zip里面是否存在main.htm文件
                    if(!$t){
                        $ffname = substr(I('name'),0,strpos(I('name'), '.'));//取得文件名，即zip文件里可能的文件夹名
                        $tt = $zip->getFromName($ffname.'/main.htm');
                        if(!$tt){
                            @unlink(C('UPLOADPATH').'weikeyun/'.$fileName);
                            die('{"code": 0, "message": "未找到main.htm文件！"}');
                        }
                    }
                    $zip->close();
                } else {
                    $done = false;
                    die('{"code": 0, "message": "zip文件不完整！"}');
                }

                //文件上传记录
                $file = array(
                    'savename' => $newname,
                    'ext' => "zip/".$oldext,
                    'type' => 'zip',
                    'savepath' => $uploadPath,
                    'uid' => admin_uid(),
                    'size' => 0,
                    'create_time' => time()
                );
                ajaxMsg(0,'处理完成',array('path'=>__ROOT__.'Uploads/Files/'.$newname.'.zip'));
//                ajaxMsg(0,"success".$oldext);
            }else{
                @unlink(C('UPLOADPATH').'weikeyun/'.$fileName);
                ajaxMsg(1,"格式不支持！");
//                die('{"code": 0, "message": "格式不支持！"}');
            }
        }//if
    }

    public function ajaxSaveExcel(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 20 * 1024 * 1024;// 设置附件上传大小
        $upload->exts = array('xls','xlsx');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = 'Admin/File/'; // 设置附件上传（子）目录
        $file = $_FILES['file'];
        $info = $upload->uploadOne($file);
        if (!$info) {// 上传错误提示错误信息
            ajaxMsg(1, '上传文件过大了');
        } else {
            $ext = $ext = strtolower($file['ext']);
            $size = $file['size'];
            $path = $info['savepath'] . $info['savename'];
            //文件上传记录
            $filel = array(
                'savename' => $info['savename'],
                'ext' => $ext,
                'type' => 'file',
                'savepath' => '/' . $info['savepath'],
                'uid' => uid(),
                'size' => $size,
                'create_time' => time()
            );
            D('Filelist')->add($filel);

            ajaxMsg(0,"",array('path'=> $path,
                'name'=>$file['name']));
        }


    }


}