<?php
namespace Common\Util;

class PhpWordUtil
{
    private $fontStyle =[ 'name' => 'Microsoft Yahei UI', 'size' => 20, 'color' => '#333', 'bold' => true ];
    private $image;
    
    public function setImage($img_url, $img_size)
    {
        //$image['img_url'], array('width'=>64, 'height'=>64)
        # code...
        if ($img_url) {
            $img['img_url'] = $img_url;
        }
        if ($img_size) {
            $img['img_size'] = $img_size;
        }else{
            $img['img_size'] = array('width'=>200, 'height'=>200);
        }
        $this->image = $img;
    }
    public function setFontStyle($font_style)
    {
        # code...
        $this->fontStyle = $font_style;
    }
    public function makeWord($title,$file_name, $font_style)
    {
        require 'vendor/autoload.php';
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        // 添加文字内容
        // 向空白页添加文字内容，可以设置文字的样式，包括字体、颜色、字号、粗体等等。
        
        $textrun = $section->addTextRun();
        if ($this->image['img_url']) {
            $img = $this->image;
            $section->addImage($img['img_url'],$img['img_size']);
        }

        $textrun->addText($title, $this->fontStyle);
        $file = $file_name;
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $xmlWriter->save("php://output");
    }
}
