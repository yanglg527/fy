
//branch.html  珠海党部
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

//city.html 支部列表
$(".city .statistics").click(function(){
	$(".city ul").hide();
	$(this).addClass("yanse").siblings().removeClass("yanse");
})
$(".city .list").click(function(){
	$(".city ul").show();
	$(this).addClass("yanse").siblings().removeClass("yanse");
})

//cityCouncil.html  市局支部
$(".cityCouncil a").click(function(){
	$(this).addClass("bor").parent().siblings().find("a").removeClass("bor");
	$(".contenter div").eq($(this).parent().index()).show().siblings().hide();
})
