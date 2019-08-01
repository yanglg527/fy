var mySwiper = new Swiper('.swiper-container',{
    prevButton:'.swiper-button-prev',
    nextButton:'.swiper-button-next',
});
$(".everyday").tap(function(){
    $(".list").addClass("hide");
    $(".activity").addClass("hide");
    $(".day_list").removeClass("hide");
    $(".day_activity").removeClass("hide");
    $(".list_head .active").removeClass("active");
    $(".list_head .dynamic span").css("display","none");
    $(".list_head #hide").css("display","inline-block");
    $(".list_head .everyday").addClass("active");

});
$(".dynamic").tap(function(){
    $(".day_list").addClass("hide");
    $(".day_activity").addClass("hide");
    $(".list").removeClass("hide");
    $(".activity").removeClass("hide");
    $(".list_head .active").removeClass("active");
    $(".list_head .dynamic span").css("display","inline-block");
    $(".list_head #hide").css("display","none");
    $(".list_head .dynamic").addClass("active");

});