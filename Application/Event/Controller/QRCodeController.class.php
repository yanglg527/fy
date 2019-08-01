<?php
/**
 * Created by PhpStorm.
 * User: baohua
 * Date: 16/10/28
 * Time: 下午8:20
 */
namespace Event\Controller;

use Common\Controller\BaseAuthController;
use Weixin\Util\QYConfig;

class QRCodeController extends BaseAuthController
{

    private $domain="";
    private $encodeCode = "djpt__++";

    function _initialize()
    {
//        parent::_initialize();
        $this->setHeader('活动报名');
        $this->domain = C('DOMAIN');
    }

    public function signQrcode()
    {
        $id = I('id');

        $event = D('Event')->find($id);
        if ($event) {
            $signCode = $event['sign_code'];
            if(!$event['sign_code']){
                $signCode = md5($id.$this->encodeCode);
                D('Event')->where("id=$id")->save(array('sign_code'=> $signCode));
            }

            $text = "http://".$this->domain."/Event/Index/sign?state=$signCode";
            $size = '30';
            $level = 'L';
            $logo = false;
            $padding = 1;


            $imagePath = '/Uploads/Event/QRcode/Sign/';
            $path = ".$imagePath";

            mkDirs($path);
            $QR = $path . $signCode.'.png';
//            $imagePath = $imagePath. $signCode.'.png';
            vendor("phpqrcode.phpqrcode");
//            echo "1";
            \QRcode::png($text, $QR, $level, $size, $padding);
//            echo "2";
            if (!$logo) {
                $QR = imagecreatefromstring(file_get_contents($QR));
                $logo = imagecreatefromstring(file_get_contents($logo));
                $QR_width = imagesx($QR);
                $QR_height = imagesy($QR);
                $logo_width = imagesx($logo);
                $logo_height = imagesy($logo);
                $logo_qr_width = $QR_width;
                $scale = $logo_width / $logo_qr_width;
                $logo_qr_height = $logo_height / $scale;
                $from_width = ($QR_width - $logo_qr_width) / 2;
                $image = imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
            }
            header("Content-Type:image/png;charset=utf-8");
//        exit('http://localhost:8099/'.__ROOT__.$iamgePath);
            imagepng($QR);
        }else{
            exit("参数有误");
        }

    }
    public function signOutQrcode()
    {
        $id = I('id');

        $event = D('Event')->find($id);
        if ($event) {
            $signCode = $event['sign_code'];
            if(!$event['sign_code']){
                $signCode = md5($id.$this->encodeCode);
                D('Event')->where("id=$id")->save(array('sign_code'=> $signCode));
            }

            $text = "http://".$this->domain."/Event/Index/signOut?state=$signCode";
            $size = '30';
            $level = 'L';
            $logo = false;
            $padding = 1;


            $imagePath = '/Uploads/Event/QRcode/SignOut/';
            $path = ".$imagePath";
            mkDirs($path);
            $QR = $path . $signCode.'.png';
//            $imagePath = $imagePath. $signCode.'.png';
            vendor("phpqrcode.phpqrcode");
            \QRcode::png($text, $QR, $level, $size, $padding);
            if (!$logo) {
                $QR = imagecreatefromstring(file_get_contents($QR));
                $logo = imagecreatefromstring(file_get_contents($logo));
                $QR_width = imagesx($QR);
                $QR_height = imagesy($QR);
                $logo_width = imagesx($logo);
                $logo_height = imagesy($logo);
                $logo_qr_width = $QR_width;
                $scale = $logo_width / $logo_qr_width;
                $logo_qr_height = $logo_height / $scale;
                $from_width = ($QR_width - $logo_qr_width) / 2;
                $image = imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
            }
            header("Content-Type:image/png;charset=utf-8");
//        exit('http://localhost:8099/'.__ROOT__.$iamgePath);
            imagepng($QR);
        }
    }
}