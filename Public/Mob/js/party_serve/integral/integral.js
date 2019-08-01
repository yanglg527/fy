//点击选择标签的input显示弹框
$(".input>.box .item").click(function(){
    if($(this).attr("class").indexOf("mechanism")!=-1){
        $(".blackspec_box").removeClass("hide");
    }else{
        $(".blacktag_box").removeClass("hide");
    }
});
$(".blacktag_box  .tag_classify span").click(function(){
    $(".blacktag_box .blacktag_content .tag_classify span.active").removeClass("active");
    $(this).addClass("active");
});
$(".blackspec_box .spec_classify span").click(function(){
    $(".blackspec_box .spec_classify span.active").removeClass("active");
    $(this).addClass("active");
});
function cancel(id){
    $("."+id).addClass("hide");
}
function save(id,name){
    $(".box ."+name+">span").text($("."+id+" span.active").text());
    $("."+id).addClass("hide");
}