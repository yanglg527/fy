$(".page>.banner>.title").html('<i onclick="history.go(-1);"></i>'+sessionStorage.getItem("all_ledger"));
var flag=0;//遮罩层显示标识
document.addEventListener('touchmove', function (event) { 　　 //监听滚动事件
    if(flag==1){　　　　　　　　　　　　　　　　　　　　　　　　　　　　//判断是遮罩显示时执行，禁止滚屏
        event.preventDefault();　　　　　　　　　　　　　　　　　　　//最关键的一句，禁止浏览器默认行为
    }
});
$(".shade").click(function(){//隐藏发布时间显示的遮罩层
    setTimeout(function(){
        $(".shade").addClass("hide");　　　//隐藏遮罩
        flag=0;　　
    },200);　　　　　　　　　　　　　　　//重置为0
});

$(".nav .screen").click(function(){//筛选遮罩层
    $(".screen_shade").removeClass("hide");    //显示遮罩层
    flag=1;
});
$(".screen_shade>.content>.container>.item").click(function(){
    if($(this).hasClass("active")){
        $(this).removeClass("active");
    }else{
        $(this).addClass("active");
    }
});
$(".save").click(function(){//隐藏筛选遮罩层
    $(".screen_shade").addClass("hide");　　　//隐藏遮罩
    flag=0;　　　　　　　　　　　　　　　       //重置为0
});
