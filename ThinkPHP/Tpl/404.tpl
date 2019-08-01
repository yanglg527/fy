<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>

</body>
</html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">
        a, fieldset, img {
            border: 0;
        }

        a {
            color: #221919;
            text-decoration: none;
            outline: none;
        }

        a:hover {
            color: #3366cc;
            text-decoration: underline;
        }

        body {
            font-size: 24px;
            color: #B7AEB4;
        }

        body a.link, body h1, body p {
            -webkit-transition: opacity 0.5s ease-in-out;
            -moz-transition: opacity 0.5s ease-in-out;
            transition: opacity 0.5s ease-in-out;
        }

        #wrapper {
            text-align: center;
            /*margin: 100px auto;*/
            /*width: 594px;*/
        }

        a.link {
            text-shadow: 0px 1px 2px white;
            font-weight: 600;
            color: #3366cc;
            opacity: 0;
        }

        h1 {
            text-shadow: 0px 1px 2px white;
            font-size: 24px;
            opacity: 0;
        }

        img {
            -webkit-transition: opacity 1s ease-in-out;
            -moz-transition: opacity 1s ease-in-out;
            transition: opacity 1s ease-in-out;
            height: 202px;
            width: 199px;
            opacity: 0;
        }

        p {
            text-shadow: 0px 1px 2px white;
            font-weight: normal;
            font-weight: 200;
            opacity: 0;
            color: darkgrey;
        }

        span{
            font-size: 14px;
            color: black;
        }
        .fade {
            opacity: 1;
        }

        @media only screen and (min-device-width: 320px) and (max-device-width: 480px) {
            #wrapper {
                margin: 40px auto;
                text-align: center;
                width: 280px;
            }
        }
    </style>
</head>

<body>

<div id="wrapper">
    <a href="index"><img src="404_icon.png" class="fade"></a>

    <div>
        <h1 class="fade">找不到您的页面了!</h1>
        <?php if(isset($e['file'])) {?>
        <div class="info" style="text-align: left;">
            <div class="title">
                <h3>错误位置</h3>
            </div>
            <div class="text">
                <span>FILE: <?php echo $e['file'] ;?> &#12288;LINE: <?php echo $e['line'];?></span>
            </div>
        </div>
        <?php }?>
        <?php if(isset($e['trace'])) {?>
        <div class="info" style="text-align: left;">
            <div class="title">
                <h3>TRACE</h3>
            </div>
            <div class="text">
                <span><?php echo nl2br($e['trace']);?></span>
            </div>
        </div>
        <?php }?>
        <p class="fade">页面错误<a style="color:#ff6600;margin-left: 20px;" href="index">到这里去看看！</a></p>
        <a class="link" href="/" onclick="history.go(-1)" style="opacity: 1;">返回?</a>
    </div>
</div>
</body>
</html>