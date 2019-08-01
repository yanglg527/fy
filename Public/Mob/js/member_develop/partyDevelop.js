// 党员发展空间tab选项卡
$(".nav_container>.title:nth-child(1)").click(function(){
	//导航字体颜色变红	
	$(this).siblings(".title").removeClass("active");
	$(this).addClass("active");
// 导航的底部红框
	$(this).siblings(".title").children("p").removeClass("red");
	$(this).children("p").addClass("red");
//切换列表
	$(this).parents(".page").find('.zonelist').hide();
	$(this).parents(".page").find('.apply_mainBody').show();
})
$(".nav_container>.title:nth-child(2)").click(function(){
	//导航字体颜色变红	
	$(this).siblings(".title").removeClass("active");
	$(this).addClass("active");
// 导航的底部红框
	$(this).siblings(".title").children("p").removeClass("red");
	$(this).children("p").addClass("red");
//切换列表

	$(this).parents(".page").find('.zonelist').hide();
	$(this).parents(".page").find('.thought_report').show();
})
$(".nav_container>.title:nth-child(3)").click(function(){
	//导航字体颜色变红	
	$(this).siblings(".title").removeClass("active");
	$(this).addClass("active");
// 导航的底部红框
	$(this).siblings(".title").children("p").removeClass("red");
	$(this).children("p").addClass("red");
//切换列表

	$(this).parents(".page").find('.zonelist').hide();
	$(this).parents(".page").find('.exam_record').show();
})
$(".nav_container>.title:nth-child(4)").click(function(){
	//导航字体颜色变红	
	$(this).siblings(".title").removeClass("active");
	$(this).addClass("active");
// 导航的底部红框
	$(this).siblings(".title").children("p").removeClass("red");
	$(this).children("p").addClass("red");
//切换列表

	$(this).parents(".page").find('.zonelist').hide();
	$(this).parents(".page").find('.party_opinion').show();
})

function show_sub_content(content,size){
	if(content != undefined && content != null){
        content = content.replace(/<.*?>/ig,"");
        if(content.length > size){
        	return content.substring(0,size) + "...";
		}else{
        	return content;
		}

	}else{
		return "";
	}

}

function get_date(time){
	if(time == undefined || time == null){
		return {
            year:'-',
            month: '-',
            day:'-'
        }
	}
    var date = new Date(time*1000);
    return {
    	year:date.getFullYear(),
		month: (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1),
		day:date.getDate()
	}
}

