//切换
$(".nav a").tap(function(){
	$(this).addClass("coo").siblings().removeClass("coo");
	$(this).children("span").removeClass("hide").parent().siblings().children("span").addClass("hide");
	if($(this).is(".party-building")){
		$(".building").removeClass("hide").siblings(".style").addClass("hide");
	}
	if($(this).is(".party-style")){
		$(".style").removeClass("hide").siblings(".building").addClass("hide");
	}
})
