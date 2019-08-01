var __root__ = "__ROOT__";
$("#btn").click(function(){
    $(".shade").addClass("hide");
});
$("a.icon").click(function(){
    $(".shade").addClass("hide");
    $(".add_shade").removeClass("hide");
});
$(".add_shade .ensure").click(function(){
    var branchName = $("#branch-name").val();
    // $(this).attr("href","temporary_member.html?branchName=" + branchName);
    $(this).attr("href","back_temporary_member.html?branchName=" + branchName);
});
$(".add_shade .cancel").click(function(){
    $(".add_shade").addClass("hide");
});
$(".item-branch").click(function () {
    var branchId = $(this).attr("data-id");
    // window.location.href = "/djptNew/Mob/TemporaryTalk/temporary_chat?branchId=" + branchId;
})