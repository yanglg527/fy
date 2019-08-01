<?php
namespace Home\Controller;

use Common\Controller\BaseNavController;
use Common\Controller\BaseNavUserController;
use Think\Controller;
use Think\Image;

/**
 * 网页版登录
 * Class IndexController
 * @package Home\Controller
 */
class ImageController extends BaseNavController//继承 baseHomeController，统一设置用户信息、顶部栏信息
{


    /**
     * 上传头像
     */
    public function ajaxSaveHeadIcon()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = 'Img/HeadIcon/' . uid() . '/'; // 设置附件上传（子）目录

        $avatarData = json_decode($_POST['avatar_data'], true);
        $file = $_FILES['avatar_file'];
        $ext = $ext = strtolower($file['ext']);
        $size = $file['size'];
        $info = $upload->uploadOne($file);
        if (!$info) {// 上传错误提示错误信息
            $this->ajaxMsg(1, "请上传适当大小的头像");
        } else {
            $path = $info['savepath'] . $info['savename'];
            $image = new Image();
            $image->open($upload->rootPath . $path);
            $image->crop($avatarData['width'], $avatarData['height'], $avatarData['x'], $avatarData['y'], 640, 640)->save($upload->rootPath . $path);
            $image->crop(640, 640, 0, 0, 320, 320)->save($upload->rootPath . $path . "-m.png");
            $image->crop(320, 320, 0, 0, 128, 128)->save($upload->rootPath . $path . "-s.png");

            $user = get_user();
            $user['head_icon'] = '/' . $path;
            D('Common/User')->save($user);
            reset_session_user($user);

            //文件上传记录
            $filel = array(
                'savename' => $info['savename'],
                'ext' => $ext,
                'type' => 'image/jpeg',
                'savepath' => '/' . $info['savepath'],
                'uid' => uid(),
                'size' => $size,
                'create_time' => time()

            );
            D('Filelist')->add($filel);

            $this->ajaxMsg(0, __ROOT__ . '/Uploads/' . $path);
        }

    }

    /**
     * 上传认证信息相关文件
     */
    public function ajaxSaveCertification()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = 'Img/Authentication/' . uid() . '/'; // 设置附件上传（子）目录

        $file = $_FILES['img_file'];
        $ext = $ext = strtolower($file['ext']);
        $size = $file['size'];
        $info = $upload->uploadOne($file);
        if (!$info) {// 上传错误提示错误信息
            $this->ajaxMsg(1, "请上传适当大小的封面");
        } else {
            $path = $info['savepath'] . $info['savename'];


            //文件上传记录
            $filel = array(
                'savename' => $info['savename'],
                'ext' => $ext,
                'type' => 'image/jpeg',
                'savepath' => '/' . $info['savepath'],
                'uid' => uid(),
                'size' => $size,
                'create_time' => time()
            );
            D('Filelist')->add($filel);

            $this->ajaxMsg(0, '', array('save_path' => '/' . $path,
                'show_path' => __ROOT__ . '/Uploads/' . $path));
        }
    }


    /**
     * 上传幻灯片
     */
    public function ajaxSaveSlide()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = 'Img/Slides/' . uid() . '/'; // 设置附件上传（子）目录

        $avatarData = json_decode($_POST['avatar_data'], true);
        $file = $_FILES['avatar_file'];
        $ext = $ext = strtolower($file['ext']);
        $size = $file['size'];
        $info = $upload->uploadOne($file);
        if (!$info) {// 上传错误提示错误信息
            $this->ajaxMsg(1, "请上传适当大小的封面");
        } else {
            $path = $info['savepath'] . $info['savename'];
            $image = new Image();
            $image->open($upload->rootPath . $path);
            $image->crop($avatarData['width'], $avatarData['height'], $avatarData['x'], $avatarData['y'], 1350, 608)->save($upload->rootPath . $path);


            //文件上传记录
            $filel = array(
                'savename' => $info['savename'],
                'ext' => $ext,
                'type' => 'image/jpeg',
                'savepath' => '/' . $info['savepath'],
                'uid' => uid(),
                'size' => $size,
                'create_time' => time()
            );
            D('Filelist')->add($filel);

            $this->ajaxMsg(0, '', array('save_path' => '/' . $path,
                'show_path' => __ROOT__ . '/Uploads/' . $path));
        }
    }

    /**
     *
     * 上传课堂封面
     *
     */
    public function ajaxSaveRoomCover()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = 'Img/RoomCover/' . uid() . '/'; // 设置附件上传（子）目录

        $avatarData = json_decode($_POST['avatar_data'], true);
        $file = $_FILES['avatar_file'];
        $ext = $ext = strtolower($file['ext']);
        $size = $file['size'];
        $info = $upload->uploadOne($file);
        if (!$info) {// 上传错误提示错误信息
            $this->ajaxMsg(1, "请上传适当大小的封面");
        } else {
            $path = $info['savepath'] . $info['savename'];
            $image = new Image();
            $image->open($upload->rootPath . $path);
            $image->crop($avatarData['width'], $avatarData['height'], $avatarData['x'], $avatarData['y'], 800, 460)->save($upload->rootPath . $path);
            $image->crop(800, 460, 0, 0, 400, 230)->save($upload->rootPath . $path . "-m.png");
            $image->crop(400, 230, 0, 0, 300, 172)->save($upload->rootPath . $path . "-s.png");


            //文件上传记录
            $filel = array(
                'savename' => $info['savename'],
                'ext' => $ext,
                'type' => 'image/jpeg',
                'savepath' => '/' . $info['savepath'],
                'uid' => uid(),
                'size' => $size,
                'create_time' => time()
            );
            D('Filelist')->add($filel);

            $this->ajaxMsg(0, '', array('save_path' => '/' . $path,
                'show_path' => __ROOT__ . '/Uploads/' . $path));
        }
    }

    /**
     *
     * 上传学校 logo封面
     *
     */
    public function ajaxSaveSchoolLogo()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = 'Img/School/Logo/' . uid() . '/'; // 设置附件上传（子）目录

        $avatarData = json_decode($_POST['avatar_data'], true);
        $file = $_FILES['avatar_file'];
        $ext = $ext = strtolower($file['ext']);
        $size = $file['size'];
        $info = $upload->uploadOne($file);
        if (!$info) {// 上传错误提示错误信息
            $this->ajaxMsg(1, "请上传适当大小的封面");
        } else {
            $path = $info['savepath'] . $info['savename'];
            $image = new Image();
            $image->open($upload->rootPath . $path);
            $image->crop($avatarData['width'], $avatarData['height'], $avatarData['x'], $avatarData['y'], 300, 300)->save($upload->rootPath . $path);
            $image->crop(300, 300, 0, 0, 200, 200)->save($upload->rootPath . $path . "-m.png");
            $image->crop(200, 200, 0, 0, 150, 150)->save($upload->rootPath . $path . "-s.png");


            //文件上传记录
            $filel = array(
                'savename' => $info['savename'],
                'ext' => $ext,
                'type' => 'image/jpeg',
                'savepath' => '/' . $info['savepath'],
                'uid' => uid(),
                'size' => $size,
                'create_time' => time()
            );
            D('Filelist')->add($filel);

            $this->ajaxMsg(0, '', array('save_path' => '/' . $path,
                'show_path' => __ROOT__ . '/Uploads/' . $path));
        }
    }

    /**
     *
     * 上传学校简介
     *
     */
    public function ajaxSaveSchoolIntro()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = 'Img/School/Intro/' . uid() . '/'; // 设置附件上传（子）目录

        $avatarData = json_decode($_POST['avatar_data'], true);
        $file = $_FILES['avatar_file'];
        $ext = $ext = strtolower($file['ext']);
        $size = $file['size'];
        $info = $upload->uploadOne($file);
        if (!$info) {// 上传错误提示错误信息
            $this->ajaxMsg(1, "请上传适当大小的封面");
        } else {
            $path = $info['savepath'] . $info['savename'];
            $image = new Image();
            $image->open($upload->rootPath . $path);
            $image->crop($avatarData['width'], $avatarData['height'], $avatarData['x'], $avatarData['y'], 1200, 380)->save($upload->rootPath . $path);
            $image->crop(1200, 380, 0, 0, 900, 250)->save($upload->rootPath . $path . "-m.png");
            $image->crop(900, 250, 0, 0, 600, 190)->save($upload->rootPath . $path . "-s.png");
            $image->crop(900, 250, 0, 0, 600, 190)->save($upload->rootPath . $path . "-s.png");
            $image->crop(420, 240, 100, 20, 420, 240)->save($upload->rootPath . $path . "-s.png");


            //文件上传记录
//            $filel = array(
//                'savename'=> $info['savename'],
//                'ext'=> $ext,
//                'type'=>'image/jpeg',
//                'savepath'=> '/'.$info['savepath'],
//                'uid' => uid(),
//                'size' => $size,
//                'create_time'=>time()
//            );
//            D('Filelist')->add($filel);

            $this->ajaxMsg(0, '', array('save_path' => '/' . $path,
                'show_path' => __ROOT__ . '/Uploads/' . $path));
        }
    }


    /**
     *
     * 上传课堂
     *
     */
    public function ajaxSaveUediter()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = 'Img/Ueditor/'; // 设置附件上传（子）目录

        $file = $_FILES['upload'];
        $ext = $ext = strtolower($file['ext']);
        $size = $file['size'];
        $info = $upload->uploadOne($file);
        if (!$info) {// 上传错误提示错误信息
            $this->ajaxMsg(1, "请上传适当大小的图片");
        } else {
            $path = $info['savepath'] . $info['savename'];
            //文件上传记录
            $filel = array(
                'savename' => $info['savename'],
                'ext' => $ext,
                'type' => 'image/jpeg',
                'savepath' => '/' . $info['savepath'],
                'uid' => uid(),
                'size' => $size,
                'create_time' => time()
            );
            D('Filelist')->add($filel);

            $this->ajaxMsg(0, __ROOT__ . '/Uploads/' . $path);
        }
    }

    private $tuyahttppath = "/Uploads/Img/Exam/Tuya/Http/";

    public function getOther()
    {
        $url = $_GET['url'];

        $img = file_get_contents($url);
        $path = $this->tuyahttppath . uid() . "/";
        $this->mkDirs('.' . $path);

        $imgname = substr(strrchr($url, "/"), 1);
        file_put_contents('.' . $path . $imgname, $img);


        //文件上传记录
        $filel = array(
            'savename' => $imgname,
            'ext' => 'jpg',
            'type' => 'image/jpeg',
            'savepath' => $url."replace--".$path,
            'uid' => uid(),
            'size' => 0,
            'create_time' => time()
        );
        D('Filelist')->add($filel);

        ajaxMsg(0, __ROOT__ . $path . $imgname);
    }


    /***
     * 先备份原图  原图-time().png
     * 保存涂鸦 覆盖原图
     */

    public function ajaxSaveTuya()
    {

        $file = $_POST['tuya_file'];
        $examPaperId = $_POST['paper_id'];
        $o_url = $_POST['ourl'];//http 的 替换路径
        $subjectId = $_POST['subject_id'];
        $subject = D('ExamSubject')->find($subjectId);
        $uploadPath = $_POST['upload_path'];

        if (__ROOT__ != '/') {
            $uploadPath = str_replace(__ROOT__, '', $uploadPath);
            $o_url = str_replace(__ROOT__, '', $o_url);
        }


        if ($subject['uid'] == uid()) {
            if (strstr($uploadPath, 'http://')) {//如果是跨域图片，需要替换原来的
                if(!$o_url){
                    ajaxMsg('图片加载中，请稍候...');
                }
                //找到当前 paper
                $paper = D('ExamPaper')->find($examPaperId);
                if($paper){
                    $answers = to_json_obj($paper['answers']);
                    foreach($answers as $key=>$answer){
                        foreach($answer as $index=>$an){
                            if(strstr($an, $uploadPath)){

                                //保存涂鸦图片
                                $path = $this->base64_upload($file, "./Uploads/Img/Exam/Tuya", uid());

                                //打开原图
                                $imageO = new Image();
                                $imageO->open('.' . $o_url);
//
//                                //打开涂鸦照片
                                $imageN = new Image();
                                $imageN->open($path);
                                //替换原图
                                $imageN->crop($imageN->width(), $imageN->height(), 0, 0, $imageO->width(), $imageO->height())->save($path);
//
//
//
                                $ans = str_replace($uploadPath, __ROOT__.ltrim($path, "."), $an);
////                                ajaxMsg(1,$o_url);
                                $answers[$key][$index]=$ans;
                                D('ExamPaper')->where('id='.$examPaperId)->save(array('answers'=> to_json_string($answers)));
                                ajaxMsg(0,"success");
                            }

                        }

                    }

                    ajaxMsg(0,"success");
                }else{
                    ajaxMsg(1,"没有找到该试卷");
                }
            } else {//普通图片


                $path = $this->base64_upload($file, "./Uploads/Img/Exam/Tuya", uid());


                $time = time();
                //打开原图，备份
                $imageO = new Image();
                $imageO->open('.' . $uploadPath);
                $imageO->save('.' . $uploadPath . $time . '.png');

                //打开涂鸦照片
                $imageN = new Image();
                $imageN->open($path);
                //替换原图
                $imageN->crop($imageN->width(), $imageN->height(), 0, 0, $imageO->width(), $imageO->height())->save('.' . $uploadPath);


                //文件上传记录
                $filel = array(
                    'savename' => $path,
                    'ext' => 'jpg',
                    'type' => 'image/jpeg',
                    'savepath' => $path . '--replace ' . $uploadPath,
                    'uid' => uid(),
                    'size' => 0,
                    'create_time' => $time
                );
                D('Filelist')->add($filel);
            }

            $this->ajaxMsg(0, '', array('save_path' => '/' . $path,
                'show_path' => __ROOT__ . '/Uploads/' . $path));
        } else {
            ajaxMsg(1, '没有操作权限');
        }
    }

    function base64_upload($base64, $path, $uid = '')
    {
        $this->mkDirs($path);
        $base64_image = str_replace(' ', '+', $base64);
        //post的数据里面，加号会被替换为空格，需要重新替换回来，如果不是post的数据，则注释掉这一行
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)) {
            //匹配成功
            if ($result[2] == 'jpeg') {
                $image_name = $uid . time() . rand(10, 99) . '.jpg';
                //纯粹是看jpeg不爽才替换的
            } else {
                $image_name = $uid . time() . rand(10, 99) . '.' . $result[2];
            }
            $image_file = "{$path}/{$image_name}";
            //服务器文件存储路径
            if (file_put_contents($image_file, base64_decode(str_replace($result[1], '', $base64_image)))) {
                return $image_file;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function base64_to_img($base64_string, $path, $uid = '')
    {
        $this->mkDirs($path);

        if ($base64_string != '') {
            $output_file = $uid . time() . rand(10, 99) . '.jpg';
            $path = $path . $output_file;
            $ifp = fopen($path, "wb");
            fwrite($ifp, base64_decode($base64_string));
            fclose($ifp);
            return ($path);
        }
        return false;
    }

    function mkDirs($dir)
    {
        if (!is_dir($dir)) {
            if (!$this->mkDirs(dirname($dir))) {
                return false;
            }
            if (!mkdir($dir, 0777)) {
                return false;
            }
        }
        return true;
    }


}
