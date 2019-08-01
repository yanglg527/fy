function change_send(){
    $("footer>.foot>.send").css({"background-color":"#ce3d3a","color":"#ffffff"});
}

// $("#btn-send").click(function () {
//     // alert("aaa");
//     var msg = $("#msg").val();
//     if(msg){
//         var branchId = $("#branch-id").val();
//         console.log("branchId = " + branchId);
//         $.ajax({
//             data: {
//                 "msg" : msg,
//                 "branchId" : branchId
//             },
//             type: 'POST',
//             dataType: 'json',
//             url: '/djptNew/Mob/TemporaryTalk/ajaxSendMsg',
//             success: function (data) {
//                 console.log("success");
//                 if (data['status'] == 0) {
//                     $("#msg").val("");
//                     mui('#pullrefresh').pullRefresh().pullupLoading();
//                 }
//             },
//             error: function (data) {
//                 alert('网络异常');
//             }
//         });
//     }else{
//         alert("不能发送空白内容");
//     }
// })


