<?php
namespace ImgToStr;

use Common\Controller\BaseController;
use Common\Util\ImageUploadUtil;
use Think\Controller;
use Weixin\Util\QYConfig;
use Admin\Util\AdminUtil;

/**
 *
 */
class ToStrController
{
    function _initialize()
    {


    }
    function tostr(){

        require_once('D:\phpStudys\WWW\fuyou\Application\ImgToStr\AipOcr.php');
////        // 你的 APPID AK SK
       $APP_ID = '15546023';
       $API_KEY = 'FTRiW25v7qmR5MFYGtYRTBMI';
       $SECRET_KEY = 'fCTDwC3Y7SVSWmyyKl3zZGsu2pEU5wfL';

        $client = new AipOcr($APP_ID, $API_KEY, $SECRET_KEY);
        $url = "https://image.baidu.com/search/detail?ct=503316480&z=0&ipn=d&word=%E8%BA%AB%E4%BB%BD%E8%AF%81%E5%9B%BE%E7%89%87&step_word=&hs=0&pn=4&spn=0&di=54370&pi=0&rn=1&tn=baiduimagedetail&is=0%2C0&istype=2&ie=utf-8&oe=utf-8&in=&cl=2&lm=-1&st=-1&cs=3387134982%2C2837425777&os=470393383%2C310294056&simid=1834624967%2C2178591313&adpicid=0&lpn=0&ln=293&fr=&fmq=1549957586591_R&fm=result&ic=&s=undefined&hd=&latest=&copyright=&se=&sme=&tab=0&width=&height=&face=undefined&ist=&jit=&cg=&bdtype=11&oriquery=&objurl=http%3A%2F%2F5b0988e595225.cdn.sohucs.com%2Fimages%2F20190124%2F7d694da6724842a788cb762a83f40d55.jpeg&fromurl=ippr_z2C%24qAzdH3FAzdH3Fooo_z%26e3Bf5i7_z%26e3Bv54AzdH3FwAzdH3Fdl8a0890d_8daammnl0&gsm=0&rpstart=0&rpnum=0&islist=&querylist=&force=undefined";

//        // 调用通用文字识别, 图片参数为远程url图片
        $client->basicGeneralUrl($url);
    }
}