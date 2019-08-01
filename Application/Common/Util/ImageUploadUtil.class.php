<?php
namespace Common\Util;


use Admin\Util\AdminUtil;
use Center\Util\CenterUtil;
use Think\Image;

class ImageUploadUtil
{


    /**
     * 配合 webuploader 使用的
     *
     *
     * @param $sub_save_path
     * @param string $file_name post 路径
     * @return array{
        'cover',
     *  'show_path',//显示图片的路径
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

        @set_time_limit(10 * 60);
        mkDirs("./Uploads/image/" . $sub_save_path);
        mkDirs("./Uploads/image/" . $sub_save_path . "temp/");
        $targetDir = './Uploads/image/' . $sub_save_path . 'temp/';
        $uploadDir = './Uploads/image/' . $sub_save_path;
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds
        // Get a file name
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES[$file_name]["name"];
        } else {
            $fileName = uniqid("file_").'.jpg';
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
            $excel = ".png,.jpg,.gif,.bmp,.jpeg,.tiff,.pcx,.tga,.exif,.fpx,.svg,.PNG,.JPG,.JPEG,.GIF,.BMP";
            $oldext = strtolower($oldext);
            if(!$oldext || $oldext == '.'){
                $oldext = 'jpg';
                $real_path = $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . '/Uploads/image/'.$sub_save_path . $fileName;
                $m = "mv $real_path $real_path.jpg";
                $fileName = $fileName.'jpg';
                exec($m, $status);
            }
            if (strpos($excel, $oldext)) {

                //文件上传记录
                $file = array(
                    'name' => $fileName,
                    'ext' => "image/" . $oldext,
                    'savename'=>$fileName,
                    'type' => 'image',
                    'savepath' => $uploadDir,
//                    'uid' => uid()?uid():(CenterUtil::center_uid()?CenterUtil::center_uid():AdminUtil::admin_uid()),
                    'size' => 0,
                    'create_time' => time()
                );
//                D('UploadFileLog')->add($file);

                //生成小，中图
                $real_path = $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . '/Uploads/image/'.$sub_save_path . $fileName;
                $exif = exif_read_data($real_path);
                $ort = $exif['Orientation'];

                switch ($ort) {
                    case 3:
                        ImageUploadUtil::pictureFlip($real_path,180);
                        break;
                    case 6:
                        ImageUploadUtil::pictureFlip($real_path,-90);
                        break;
                    case 8:
                        ImageUploadUtil::pictureFlip($real_path,90);
                        break;
                    default: break;
                }
                $image = new Image();
                $image->open($real_path);
                $heigh = $image->height();
                $width = $image->width();
                $size = filesize($real_path);

                $data_json = I('post.avatar_data');
                if($data_json){
                    $avatarData = json_decode($_POST['avatar_data'], true);
                    $scale = I('avatar_ratio','1/1');
                    $scales = explode("/",$scale);
                    $ratio = intval($scales[1])/intval($scales[0]);
                    $avatarData['width'] = $avatarData['width'].' ';
                    $avatarData['height'] = $avatarData['height'].' ';
                    $w = explode(".",$avatarData['width']);
                    $avatarData['width'] = intval($w[0]);
                    $h = explode(".",$avatarData['height']);
                    $avatarData['height'] = intval($h[0]);
                    $avatarData['x'] = $avatarData['x'].' ';
                    $avatarData['y'] = $avatarData['y'].' ';
                    $x = explode(".",$avatarData['x']);
                    $avatarData['x'] = intval($x[0]);
                    $y = explode(".",$avatarData['y']);
                    $avatarData['y'] = intval($y[0]);



                    $image->crop($avatarData['width'], $avatarData['height'], $avatarData['x'], $avatarData['y'], $avatarData['width'], $avatarData['height'])->save($uploadDir . $fileName);
                    $image->crop($avatarData['width'], $avatarData['height'], 0, 0, 320, intval(320*$ratio))->save($uploadDir . $fileName . "-m.png");
                    $image->crop(320, 320*$ratio, 0, 0, 128, intval(128*$ratio))->save($uploadDir . $fileName ."-s.png");

                    return array('save_path' => '/Uploads/image/'.$sub_save_path . $fileName,
                        'show_path'=>  __ROOT__ . '/Uploads/image/'.$sub_save_path . $fileName."-m.png",
                        'cover' => '/Uploads/image/'.$sub_save_path . $fileName."-m.png",
                        'file_name'=>$fileName,
                        'size'=>$size);
                }else{

                    if($thumbnail1){
                        $width1 = $thumbnail1['width'];
                        $height1 = $thumbnail1['height'];

                        if($width1 == 'auto'){
                            $width1 = $width * ($height1/$heigh);
                            $image->thumb($width1, $height1,Image::IMAGE_THUMB_CENTER)->save($uploadDir . $fileName . "-m.png");
//                            $image->crop($width, $heigh, 0, 0, $width1, $height1)->save($uploadDir . $fileName . "-m.png");
                        }elseif($height1 == 'auto'){
                            $height1 = $heigh * ($width1/$width);
                            $image->thumb($width1, $height1,Image::IMAGE_THUMB_CENTER)->save($uploadDir . $fileName . "-m.png");
//                            $image->crop($width, $heigh, 0, 0, $width1, $height1)->save($uploadDir . $fileName . "-m.png");
                        }else{
                            $image->thumb($width1, $height1,Image::IMAGE_THUMB_CENTER)->save($uploadDir . $fileName . "-m.png");
//                            $image->crop($width, $heigh, 0, 0, $width1, $height1)->save($uploadDir . $fileName . "-m.png");
                        }
                    }
                    if($thumbnail2){
                        $width1 = $thumbnail2['width'];
                        $height1 = $thumbnail2['height'];


                        if($width1 == 'auto'){
                            $width1 = $width * ($height1/$heigh);
                            $image->thumb($width1, $height1,Image::IMAGE_THUMB_CENTER)->save($uploadDir . $fileName . "-s.png");
//                            $image->crop($width, $heigh, 0, 0, $width1, $height1)->save($uploadDir . $fileName . "-m.png");
                        }elseif($height1 == 'auto'){
                            $height1 = $heigh * ($width1/$width);
                            $image->thumb($width1, $height1,Image::IMAGE_THUMB_CENTER)->save($uploadDir . $fileName . "-s.png");
//                            $image->crop($width, $heigh, 0, 0, $width1, $height1)->save($uploadDir . $fileName . "-m.png");
                        }else{
                            $image->thumb($width1, $height1,Image::IMAGE_THUMB_CENTER)->save($uploadDir . $fileName . "-s.png");
//                            $image->crop($width, $heigh, 0, 0, $width1, $height1)->save($uploadDir . $fileName . "-m.png");
                        }
                    }

                    if($thumbnail1) {
                        return array('save_path' => '/Uploads/image/'.$sub_save_path . $fileName,
                            'show_path'=>  __ROOT__ . '/Uploads/image/'.$sub_save_path . $fileName.'-m.png',
                            'cover' => '/Uploads/image/'.$sub_save_path . $fileName.'-m.png',
                            'file_name'=>$fileName,
                            'size'=>$size);
                    }elseif($thumbnail2) {
                        return array('save_path' => '/Uploads/image/'.$sub_save_path . $fileName,
                            'show_path'=>  __ROOT__ . '/Uploads/image/'.$sub_save_path . $fileName.'-s.png',
                            'cover' => '/Uploads/image/'.$sub_save_path . $fileName.'-m.png',
                            'file_name'=>$fileName,
                            'size'=>$size);
                    }else{
                        return array('save_path' => '/Uploads/image/'.$sub_save_path . $fileName,
                            'show_path'=>  __ROOT__ . '/Uploads/image/'.$sub_save_path . $fileName,
                            'cover' => '/Uploads/image/'.$sub_save_path . $fileName,
                            'file_name'=>$fileName,
                            'size'=>$size);
                    }


                }



            } else {
//                @unlink($_SERVER['DOCUMENT_ROOT'] . __ROOT__ . '/Uploads/image/'.$sub_save_path . $fileName);
                ajaxMsg(1, "格式不支持！");
            }
        }else{
            return false;
        }
    }

    /**
     * 图片旋转处理
     * @param  string  $oldFile 源图片路径
     * @param  string  $newFile 保存路径
     * @param  integer $degrees 旋转角度
     * @return boolean 图片是否旋转成功
     */
    static function pictureFlip($oldFile,$degrees){
        if(!empty($degrees)) {
            //读取源图片
            $data = @getimagesize($oldFile);//@是忽略报错
            if ($data == false) return false;
            //根据源图片创建保存文件格式
            switch ($data[2]) {
                case 1:
                    $src_f = imagecreatefromgif($oldFile);
                    break;
                case 2:
                    $src_f = imagecreatefromjpeg($oldFile);
                    break;
                case 3:
                    $src_f = imagecreatefrompng($oldFile);
                    break;
            }
            if ($src_f == "") {//图片格式
//                return false;
            }else{
                $rotate = imagerotate($src_f, $degrees, 0);//旋转图片
                switch ($data[2]) {
                    case 1:
                        imagegif($rotate,$oldFile);
                        break;
                    case 2:
                        imagejpeg($rotate,$oldFile);
                        break;
                    case 3:
                        imagepng($rotate,$oldFile);
                        break;
                }
                @imagedestroy($rotate);
            }


//            ajaxMsg(1,$data[2]);
            return true;
        }else{
            return true;
        }
    }



    public static function uploadFormImg($savePath){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 20 * 1024 * 1024;// 设置附件上传大小
        $upload->exts = array('png','jpg','gif','bmp');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = 'image/'.$savePath.'/'; // 设置附件上传（子）目录
        ajaxMsg(1,to_json_string($_FILES));
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

            ajaxMsg(0,"",array('path'=> 'http://'.$_SERVER["HTTP_HOST"].'/'.__ROOT__.$path,
                'name'=>$file['name']));
        }
    }

}
