<?php
/**
 * 文件上传
 */
namespace Api\Controller;

use Common\Controller\BaseAuthController;
use Common\Controller\BaseController;
use Think\Image;
class UploadController extends BaseAuthController
{
    protected $domain;
    protected $config = array(
        'maxSize' => 50000000, // 50M=50000000字节
        'exts'=> array('jpg', 'png', 'jpeg', 'mp3', 'mp4', 'ppt', 'docx', 'xlsx', 'zip', 'rar'),
        'rootPath'=> './Uploads/', //保存根路径
        'savePath'=> '', //保存路径
        'autoSub'=> true, //自动子目录保存文件
        'subName'=> 'default',
    );

    function _initialize()
    {
        parent::_initialize();
        $this->domain = C('DOMAIN');
    }

    /**
     * bse64 文件上传
     */
    public function byBase64()
    {
        checkRequestType('post');
        $fileBase64 = I('post.file');
        if(!$fileBase64) responseError('缺少必要参数');
        $url = base64ImageContent($fileBase64, 'base64/');
        if (false !== $url) {
            responseSuccess(array('url' => $url));
        }
        responseError('图片上传失败！请稍后重试');
    }

    /**
     * formData 文件上传
     * @return [type] [description]
     */
    public function byFormData()
    {
        checkRequestType('post');
        try {
            $this->config['subName'] = 'byFormData';
            $res = $this->upload();
            $url = array('url' => $this->domain.'/Uploads/'.$res['savepath'].$res['savename']);
            responseSuccess($url);
        } catch (\Exception $e) {
            responseError($e->getError());
        }
    }

    protected function upload()
    {
      $upload = new \Think\Upload($this->config); // 实例化上传类
      $info = $upload->upload();
      if(!$info) {// 上传错误提示错误信息
          throw new \Exception($upload->getError());
      }else{// 上传成功 获取上传文件信息
          $res = array(
              'key' => $info['file']['key'],
              'ext' => $info['file']['ext'],
              'type' => $info['file']['type'],
              'name' => $info['file']['name'],
              'savename' => $info['file']['savename'],
              'savepath' => $info['file']['savepath'],
          );
        return $res;
      }
    }
}
