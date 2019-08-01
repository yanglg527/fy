<?php
namespace Common\Util;



use Admin\Util\AdminUtil;
use Center\Util\CenterUtil;
use Think\Image;

class VideoUploadUtil
{


    /**
     * 配合 webuploader 使用
     *
     * @param $sub_save_path 保存的子目录
     * @param string $file_name post 名称
     * $coverOpt  裁剪尺寸 默认 320*320
     * @return array{
        'cover',//封面
     *  'show_path',//截图显示地址
     *  'save_path',//实际文件保存地址
     * }
     */
    public static function upload($sub_save_path, $file_name = 'file',$thumbnail1=array('width'=>360,'height'=>'auto'),$thumbnail2=array('width'=>80,'height'=>80))
    {

//        ajaxMsg(1,$_POST['avatar_data']);
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            ajaxMsg(1, "上传失败");
        }
        if (!empty($_REQUEST['debug'])) {
            $random = rand(0, intval($_REQUEST['debug']));
            if ($random === 0) {
                header("HTTP/1.0 500 Internal Server Error");
                ajaxMsg(1, "上传失败");
            }
        }
    
        @set_time_limit(2 * 60 * 60);
        mkDirs("./Uploads/video/" . $sub_save_path);
        mkDirs("./Uploads/video/" . $sub_save_path."/cover/");
        mkDirs("./Uploads/video/" . $sub_save_path . "temp/");
        $targetDir = './Uploads/video/' . $sub_save_path . 'temp/';
        $uploadDir = './Uploads/video/' . $sub_save_path;
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds
        // Get a file name
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES[$file_name]["name"];
        } else {
            $fileName = uniqid("file_");
        }

        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;
        $newname = time() . '-' . mt_rand();
        $oldext = substr(strrchr($fileName, '.'), 1);

        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
        $fileName = $newname . '.' . $oldext;
        $uploadPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;


        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                ajaxMsg(1, "Failed to open temp directory");
            }
            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
                if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
                    continue;
                }
                if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }
        if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {

            ajaxMsg(1, "Failed to open output stream");
        }

        if (!empty($_FILES)) {
           
            if ($_FILES[$file_name]["error"] || !is_uploaded_file($_FILES[$file_name]["tmp_name"])) {
                ajaxMsg(1, "Failed to move uploaded file");
            }
            // Read binary input stream and append it to temp file
            if (!$in = @fopen($_FILES[$file_name]["tmp_name"], "rb")) {
                ajaxMsg(1, "Failed to open input stream");
            }
        } else {
            if (!$in = @fopen("php://input", "rb")) {
                ajaxMsg(1, "Failed to open input stream");
            }
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }
      
        @fclose($out);
        @fclose($in);
        rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");
        $done = true;
        for ($index = 0; $index < $chunks; $index++) {
            if (!file_exists("{$filePath}_{$index}.part")) {
                $done = false;
                break;
            }
        }
     
        if ($done) {
            if (!$out = @fopen($uploadPath, "wb")) {
                ajaxMsg(1, "Failed to open input stream");
            }
            if (flock($out, LOCK_EX)) {
             
                for ($index = 0; $index < $chunks; $index++) {
                 
                   
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
        

        if ($done) {
            $mpeg = ".avi,.mp4,.flv,.mov,.mpg,.wmv,.ogg,.rmvb,.mkv,.3gp,.swf,.asf,.asx,.rm,.mpg,.mpeg,.mpe,.m4v,.vob,.dat,.DAT,.VOB,.M4V,.MPE,.MPEG,.MPG,.RM,.ASX,.ASF,.RMVB,.MKV,.3GP,.AVI,.MP4,.FLV,.MOV,.MPG,.WMV,.OGG,.SWF";
            $oldext = strtolower($oldext);
            if(strpos($mpeg,$oldext)){
                $cmd = 'ffmpeg -i '.$_SERVER['DOCUMENT_ROOT'] . __ROOT__ . '/Uploads/video/'.$sub_save_path . $fileName.' -acodec copy -vcodec copy -f mp4 '.$_SERVER['DOCUMENT_ROOT'] . __ROOT__ . '/Uploads/video/'.$sub_save_path . $fileName.'_z.mp4';
               
                exec($cmd, $status);//这里status=1表示有错，和ajax返回的相反。
              
                if($status){
                    ajaxMsg(1,"转换格式失败");
                }

                //截图
                $cmd = 'ffmpeg -ss 0 -i '.$_SERVER['DOCUMENT_ROOT'] . __ROOT__ . '/Uploads/video/'.$sub_save_path . $fileName.' -vframes 1 -f image2 -y '.$_SERVER['DOCUMENT_ROOT'] . __ROOT__ . '/Uploads/video/'.$sub_save_path .'/cover/'.$fileName.'_z.jpg';
                exec($cmd, $status1);
              
                //删除原来的
              //  @unlink($_SERVER['DOCUMENT_ROOT'] . __ROOT__ . '/Uploads/video/'.$sub_save_path . $fileName);
                //将文件信息存入数据库
                
                
                $imgName = $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . '/Uploads/video/'.$sub_save_path .'/cover/'.$fileName.'_z.jpg';
                $image = new Image();
                $image->open($imgName);
                $heigh = $image->height();
                $width = $image->width();
                if($thumbnail1){
                        $width1 = $thumbnail1['width'];
                        $height1 = $thumbnail1['height'];

                        if($width1 == 'auto'){
                            $width1 = $width * ($height1/$heigh);
                            $image->thumb($width1, $height1,Image::IMAGE_THUMB_CENTER)->save($imgName . "-m.png");
                        }elseif($height1 == 'auto'){
                            $height1 = $heigh * ($width1/$width);
                            $image->thumb($width1, $height1,Image::IMAGE_THUMB_CENTER)->save($imgName . "-m.png");
                        }else{
                            $image->thumb($width1, $height1,Image::IMAGE_THUMB_CENTER)->save($imgName . "-m.png");
                        }
                    }
                    if($thumbnail2){
                        $width1 = $thumbnail2['width'];
                        $height1 = $thumbnail2['height'];


                        if($width1 == 'auto'){
                            $width1 = $width * ($height1/$heigh);
                            $image->thumb($width1, $height1,Image::IMAGE_THUMB_CENTER)->save($imgName . "-s.png");
                        }elseif($height1 == 'auto'){
                            $height1 = $heigh * ($width1/$width);
                            $image->thumb($width1, $height1,Image::IMAGE_THUMB_CENTER)->save($imgName . "-s.png");
                        }else{
                            $image->thumb($width1, $height1,Image::IMAGE_THUMB_CENTER)->save($imgName . "-s.png");
                        }
                    }
                

//              $uid =  uid()?uid():(CenterUtil::center_uid()?CenterUtil::center_uid():AdminUtil::admin_uid());
//
//              //文件上传记录
//              $file = array(
//                  'name' => $fileName,
//                  'ext' => "video/" . $oldext,
//                  'savename'=>$fileName,
//                  'type' => 'video',
//                  'savepath' => $uploadDir,
//                  'uid' => $uid,
//                  'size' => 0,
//                  'create_time' => time()
//              );
//                D('UploadFileLog')->add($file);


                return array('save_path' => '/Uploads/video/'.$sub_save_path . $fileName.'_z.mp4',
                    'show_path'=>  __ROOT__ . '/Uploads/video/'.$sub_save_path.'/cover/' . $fileName.'_z.jpg',
                    'cover' => '/Uploads/video/'.$sub_save_path .'/cover/' . $fileName.'_z.jpg');
            } else {
                @unlink($_SERVER['DOCUMENT_ROOT'] . __ROOT__ . '/Uploads/video/'.$sub_save_path . $fileName);
                ajaxMsg(1, "格式不支持！");
            }
        }else{
            return false;
        }
    }

}
