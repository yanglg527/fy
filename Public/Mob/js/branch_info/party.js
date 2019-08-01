
//organizations.html  珠海党部
$(".branch .statistics").click(function(){
	$(".branch dl").hide();
	$(this).addClass("yanse").siblings().removeClass("yanse");
})
$(".branch .list").click(function(){
	$(".branch dl").show();
	$(this).addClass("yanse").siblings().removeClass("yanse");
})

//personal.html  个人信息页
$(".remind .determine").click(function(){
	$(".remind").hide();
});

//branchs.html 支部列表
$(".city .statistics").click(function(){
	$(".city ul").hide();
	$(this).addClass("yanse").siblings().removeClass("yanse");
})
$(".city .list").click(function(){
	$(".city ul").show();
	$(this).addClass("yanse").siblings().removeClass("yanse");
})

//branchInfo.html  市局支部
$(".cityCouncil>li>a").click(function(){
	$(this).addClass("bor").parent().siblings().find("a").removeClass("bor");

	var id = "#page" + $(this).parent().index();
	console.log(id)
	//console.log(id)
	//$(id).show();
	//$(id).siblings().hide();
	$(id).show().siblings().hide();
})
