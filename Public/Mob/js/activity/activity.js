

//--------------------list-management.html-----------------------
//------------初始化
$(".list-management ul").hide();
$(".list-management .drop").addClass("rotate");
$(".list-management .confirm").addClass("hide");
//------------点击横岗收缩
$(".list-management .title").click(function(){
	$(this).siblings().toggle();
	$(this).find(".drop").toggleClass("rotate");
})
//------------点击父元素全改
$(".list-management .title .conf").click(function(){
	if($(this).parent().siblings().find(".confirm").is(".hide")){
		$(this).parent().siblings().find(".confirm").removeClass("hide");
		$(this).find(".confirm").removeClass("hide");
	}else{
		$(this).parent().siblings().find(".confirm").addClass("hide");
		$(this).find(".confirm").addClass("hide");
	}
	return false;
})
//------------点击单个人，不满，横岗hide，全满，横岗show
$(".list-management ul li").click(function(){
	$(this).find(".confirm").toggleClass("hide");
	var bur = true;
	$.each($(this).parent().find(".confirm"),function(index,val){
		if($(val).is(".hide")){
			bur = false;
		}
	})
	if(bur){
		$(this).parent().siblings().find(".confirm").removeClass("hide");
	}else{
		$(this).parent().siblings().find(".confirm").addClass("hide");
	}
});
//提交报名
$(".submit").click(function(){
	$(this).text("报名成功");
	$(this).attr("href","list-browse.html");
})