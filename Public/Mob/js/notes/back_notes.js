//-----------------个人读书笔记-------------------------
//长按显示
// $(".notes li").longTap(function(){
// 	$(".page .delete .delete-tr").addClass("hide").siblings("img").removeClass("hide");
// 	$(this).parent().find(".san").show();
// })
// //点击删除
// $(".notes li").tap(function(){
// 	if($(".page .delete .delete-tr").is(".hide")){
// 		$(this).find(".san").toggleClass("san-edit");
// 	}
// 	return false;
// })
//删除含有勾的li
$(".page .delete .delete-dr").tap(function(){
	$.each($(".notes li .san"),function(index,val){
		console.log($(val).parents("li"));
		if(!$(val).is(".san-edit")){
			$(val).parents("li").remove();
		}
	})
	$(this).addClass("hide").siblings("img").removeClass("hide");
	$(".notes li").find(".san").hide();
})
