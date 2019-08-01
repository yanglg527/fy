var u = navigator.userAgent;
var device =""; //当前设备信息
if (u.indexOf('Android') > -1 || u.indexOf('Linux') > -1) {//安卓手机
    device = "Android";
} else if (u.indexOf('iPhone') > -1) {//苹果手机
    device = "iPhone";
} else if (u.indexOf('Windows Phone') > -1) {//winphone手机
    device = "Windows Phone";
}
var height=window.screen.availHeight;
$(".swiper-container").css("height",height);
if(device=="iPhone"){
    $(".page2").css("top",height+"px");
    $(".page3").css("top",height*2+"px");
}
var mySwiper = new Swiper('.swiper-container',{
    direction : 'vertical',
});
var login_left=$(".page1 .login_bg .pei").offset().left;
var login_top=$(".page1 .login_bg .pei").offset().top;
var right_left=$(".page1 .right_bg .pei").offset().left;
var right_top=$(".page1 .right_bg .pei").offset().top;
$(".page1 .login_bg .line").css({"width":(right_left-login_left)+"px","height":(right_top-login_top)+"px"});
var like_bgleft=$(".page1 .like_bg .bg_pei").offset().left;
var like_bgtop=$(".page1 .like_bg .bg_pei").offset().top;
$(".page1 .right_bg .line").css({"width":(right_left-like_bgleft)+"px","height":(like_bgtop-right_top+10)+"px"});
var like_left=$(".page1 .like_bg .pei").offset().left;
var like_top=$(".page1 .like_bg .pei").offset().top;
$(".page1 .like_bg .line").css({"width":(like_left-like_bgleft)+"px","height":(like_top-like_bgtop)+"px"});
var center_left=$(".page1 .center_bg .pei").offset().left;
var center_top=$(".page1 .center_bg .pei").offset().top;
$(".page1 .like_bg .center_line").css({"width":(center_left-like_left+5)+"px","height":(center_top-like_top+7)+"px"});
