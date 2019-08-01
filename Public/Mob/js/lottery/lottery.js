var x = null;
//点击出现红包
$(".lottery-area li").tap(function(){
	var index = $(this).index();
	console.log("index = " + index);
	ajaxGetPrize(index);
})

//点击知道了关闭红包
$(".remind .zhidao").tap(function(){
	$(".remind").hide();
	$(".lottery-area li").find(".no-open").addClass("hide");
	$(".lottery-area li").find(".has-been-opened").removeClass("hide");
})

function ajaxGetPrize(index){
	$.ajax({
		type: "post",
		url: "/djptNew/Mob/Lottery/ajaxGetPrize",
		cache: false,
		data:{
			"index": index
		},
		dataType: "json",
		success: function (res) {
			if(res['status'] == 0){
				// 设置各卡牌的值
				console.log("peizeConfigList = " + JSON.stringify(res['data']['prizeConfigList']));
				setCardValue(index, res['data']['prizeConfigList']);
				// 显示抽奖结果
				//判断是0分，还是有1-10分
				// var index = parseInt($(this).find(".text span").text());
				// if(x == null){
				// 	x = index;
				// }
				console.log("win point = " + res['data']['prize']['point']);
				if(res['data']['prize']['point'] > 0){
					$(".remind").show().find(".yes").removeClass("hide").siblings("div").addClass("hide");
				}else{
					$(".remind").show().find(".no").removeClass("hide").siblings("div").addClass("hide");
				}
				$(".remind").find("p>span").text(x);
			}else if(res['status'] == 1){
				alert(res['msg']);
			}
		},
		error: function () {
			alert("网络异常...");
		}
	});

	function setCardValue(index, prizeList){
		// $("#item" + index).innerText = prizeList['point'];
		console.log("list length = " + prizeList.length);
		for(var i=0; i<prizeList.length; i++){
			console.log("point = " + prizeList[i]['point']);
			console.log("jiu item text = " + $("#item" + index));
			// $("#item" + index).innerText = prizeList[i]['point'];
		}
	}

}