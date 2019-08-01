//***************************台账2*****************************************
//***************************指示列表***************************

// $(".nav>.pointDes").text(sessionStorage.getItem("one"));

//点击选择标签区域，弹出遮罩层，标签选择项

$(".chooseParty").click(function(event){
    $(".blackgroup_box").show();
});

//定义一个全局变量获取当前span的内容

var spanText;

//点击当前标签，加上名字叫做active的类名，字体跟背景变色；其他的删除active类名,
//点击加颜色，再点击，颜色消失，再点击，颜色加....
$(".group_classify>span").click(function(){
    if($(this).hasClass("active")){
        $(this).removeClass("active");   
    }
    else{
        $(this).addClass("active");
        $(this).siblings().removeClass("active");
        $(this).siblings().find("span").removeClass("active");
        spanText=$(this).text();
    }
})
//点击确定提交，遮罩层，标签选择像区域消失

$(".group_sure").click(function(){
      //对是否选择了标签做出判断
    if($(".group_classify>span").hasClass("active")){
        $(".blackgroup_box").hide();
        $(this).parents(".blackgroup_box").siblings(".inputBox").find(".choosegroup_span").text(spanText);
    }
    else{
        $(".blackgroup_box").hide();
    }

})

//点击取消按钮，遮罩层，标签选择像区域消失

$(".group_submit>.group_cancle").click(function(){
    $(".blackgroup_box").hide();
})


//************************请选择标签弹框(遮罩层)**********************


$(".chooseTag").click(function(event){
    $(".blacktag_box").show();
});

//定义一个全局变量获取当前span的内容

var spanText;

//点击当前标签，加上名字叫做active的类名，字体跟背景变色；其他的删除active类名

$(".tag_classify>span").click(function(){
    if($(this).hasClass("active")){
        $(this).removeClass("active");   
    }
    else{
        $(this).addClass("active");
        $(this).siblings().removeClass("active");
        $(this).siblings().find("span").removeClass("active");
        spanText=$(this).text();
    }
})

//点击确定提交，遮罩层，标签选择像区域消失

$(".tag_sure").click(function(){
    //对是否选择了标签做出判断
    if($(".tag_classify>span").hasClass("active")){
        $(".blacktag_box").hide();
        $(this).parents(".blacktag_box").siblings(".inputBox").find(".choosetag_span").text(spanText);
    }
    else{
        $(".blacktag_box").hide();
    }

});

//点击取消按钮，遮罩层，标签选择像区域消失

$(".tag_submit>.tag_cancle").click(function(){
    $(this).parents(".blacktag_box").hide();
})
//************************请选择规定或创新弹框(遮罩层)**********************
$(".chooseSpecify").click(function(event){
    $(".blackspec_box").show();
});

//定义一个全局变量获取当前span的内容

var pText;

//点击当前标签，加上名字叫做active的类名，字体跟背景变色；其他的删除active类名

$(".spec_classify>p").click(function(){
    if($(this).hasClass("active")){
        $(this).removeClass("active");   
    }
    else{
        $(this).addClass("active");
        $(this).siblings("p").removeClass("active");
        pText=$(this).text();
    }
})

//点击确定提交，遮罩层，标签选择像区域消失

$(".spec_sure").click(function(){
    //对是否选择了标签做出判断
    if($(".spec_classify>p").hasClass("active")){
        $(".blackspec_box").hide();
        $(this).parents(".blackspec_box").siblings(".inputBox").find(".chooseSpecify_span").text(pText);
    }
    else{
        $(".blackspec_box").hide();
    }

});

//点击取消按钮，遮罩层，标签选择像区域消失

$(".spec_submit>.spec_cancle").click(function(){
    $(this).parents(".blackspec_box").hide();
})
//***************************日历***************************************



