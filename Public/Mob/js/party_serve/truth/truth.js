//-----------------------------------点赞-------------------------------
$(".truth .like").click(function(){
	if(!$(this).find(".ledger").is(".hide")){
		$(this).find(".ledger").addClass("hide").siblings("img").removeClass("hide");
		$(this).find("span").text(parseInt($(this).find("span").text())+1);
	}
	return false;
})
