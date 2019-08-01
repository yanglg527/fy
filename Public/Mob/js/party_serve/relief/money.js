//------------------------支付方式--------------------------
$(".payment").click(function(){
	$(this).find(".conf>i").toggleClass("back");
	$(this).find(".confirm").toggleClass("hide");
	$(this).find(".fff").toggleClass("hide");
})
//-------------------------捐款金额-------------------------
$(".amount-of-money li").click(function(){
	$(this).toggleClass("kek").siblings("li").removeClass("kek");
	$('#money').val($(this).find('span').text())
})
