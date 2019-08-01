var height = document.documentElement.clientHeight;
$(".warn_list").css("height", (height - parseInt($(".warn_list").offset().top)));
var mySwiper = new Swiper('.swiper-container', {
    slidesPerView: 7,
    slidesPerGroup: 7,
});
$(".header>.mission").click(function () {
    $(".header .active").removeClass("active");
    $(".header>.mission").addClass("active");
    $(".header>.mission>p").addClass("active");
    $(".list>.mission_list").css("display", "block");
    $(".list>.like_list").css("display", "none");
    $(".list>.comment_list").css("display", "none");
    $(".list>.jiancha_list").css("display", "none");
    ajax_loading(0);
});
$(".header>.like").click(function () {
    $(".header .active").removeClass("active");
    $(".header>.like").addClass("active");
    $(".header>.like>p").addClass("active");
    $(".header>.like>.icon_bg").addClass("focus");
    $(".list>.like_list").css("display", "block");
    $(".list>.mission_list").css("display", "none");
    $(".list>.comment_list").css("display", "none");
    $(".list>.jiancha_list").css("display", "none");
    // ajax_like_loading();
//clear_list('branch_list');
    ajax_branch_loading(1, 'branch_list');
});
$(".header>.comment").click(function () {
    $(".header .active").removeClass("active");
    $(".header>.comment").addClass("active");
    $(".header>.comment>p").addClass("active");
    $(".header>.comment>.icon_bg").addClass("focus");
    $(".list>.comment_list").css("display", "block");
    $(".list>.mission_list").css("display", "none");
    $(".list>.like_list").css("display", "none");
    $(".list>.jiancha_list").css("display", "none");
    // ajax_comment_loading();
   // clear_list('party_list');
    ajax_branch_loading(2, 'party_list');
});
$(".header>.jiancha").click(function () {
    $(".header .active").removeClass("active");
    $(".header>.jiancha").addClass("active");
    $(".header>.jiancha>p").addClass("active");
    $(".header>.jiancha>.icon_bg").addClass("focus");
    $(".list>.jiancha_list").css("display", "block");
    $(".list>.mission_list").css("display", "none");
    $(".list>.like_list").css("display", "none");
    $(".list>.comment_list").css("display", "none");
    // ajax_comment_loading();
 //   clear_list('jiancha_list');
    ajax_branch_loading(3, 'jiancha_list');
});

function clear_list(dom_id) {
    if (dom_id) {
        $('#' + dom_id).html('')
      //  console.log($('#' + dom_id).siblings('div'))
        // $('#' + dom_id).siblings('div')[0].remove()
    }

}







