$(".list>li>i").click(function(){
    console.log("xxxxxxxxxxxx");
    var html="";
    var dataId = $(this).attr("data-id");
    console.log("data-id = " + dataId);
    var isActive=$(this).hasClass("active");
    if(isActive){
        console.log("取消选中");
        $(this).removeClass("active");
        $(".search .item"+dataId).css("display","none");
        $(".search .item"+dataId).removeClass("selected-item");
    }else{
        console.log("设置选中")
        $(this).addClass("active");
        var src= $(this).parent("li").children(".portrait").children("img").attr("src");
        html="<div class='selected-item portrait item" + dataId + "' data-id='" + dataId + "'><img src='" + src + "'></div>";
        $(".search>i").css("display","none");
        $(html).insertBefore(".search input[type='search']");
    }
    return false;

    // 原版
    // var html="";
    // var isActive=$(this).hasClass("active");
    // var index=String($(this).parent("li").attr("class"));
    // index=index.slice(index.indexOf("index")+5);
    // if(isActive){
    //     $(this).removeClass("active");
    //     $(".search .index"+index).css("display","none");
    // }else{
    //     $(this).addClass("active");
    //     var src= $(this).parent("li").children(".portrait").children("img").attr("src");
    //     html="<div class='portrait'><img src='" + src + "'></div>";
    //     $(".search>i").css("display","none");
    //     $(html).insertBefore(".search input[type='search']");
    // }
});

// $(".head>a.cancel").click(function(){
//     $(this).attr("href","temporary_branch.html");
// });

// $(".head>a.chat").click(function(){
//     var branchName = $("#branch-name").val();
//     console.log("branchName = " + branchName);
//
//     var selectedItems = $(".selected-item");
//     if(selectedItems.length > 0){
//         var members;
//         for (var i=0 ; i<selectedItems.length; i++){
//             members = members ? (members + "," + $(selectedItems[i]).attr("data-id")) : $(selectedItems[i]).attr("data-id");
//         }
//         console.log("members = " + members);
//         ajaxAddTemporaryBranch(branchName, members);
//     }else{
//         alert("请至少选择一个群成员");
//     }
// });
