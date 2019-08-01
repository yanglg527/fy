//-----------------------------------发送评论-------------------------------
$(".truth_all .enter .enter_bt").click(function(){
	let li = '<li><div class="logo"><img src="../../images/test/jzj.png" alt="logo"/></div>'+
			 '<article><header><span class="name">赵虹飞</span><span class="position">宣传委员</span><time><span>5分钟</span>前</time></header>'+
			 '<p>'+ $(this).siblings("input").val() +'</p></article></li>';
	$(".truth_all .comment ul").append(li);
})
