// // 党员发展
//
// $('.nav_ul li').click(function(){
// 	//点击当个li，背景颜色加深
// 	$(this).addClass("whenTab").siblings("li").removeClass("whenTab");
// 	$(this).children(".container").children("p").addClass("active");
// 	//点击更换图片
// 	$(this).find(".pic1").addClass("hide");
// 	$(this).find(".pic2").removeClass("hide");
// 	$(this).siblings("li").find(".pic1").removeClass("hide");
// 	$(this).siblings("li").find(".pic2").addClass("hide");
// 	//改变p标签字体等样式
// 	$(this).children(".container").children("p").addClass("active");
// 	$(this).siblings("li").children(".container").children("p").removeClass("active");
// 	$(".nav_ul li .container>span").addClass("active");
// 	$(this).children(".container").children("span").removeClass("active");
// 	//切换列表
// 	var index=$(this).index();
// 	$(this).parents(".page").find(".content_ul").eq(index).show().siblings(".content_ul").hide();
// });
// //群众头部切换
// $(".mass_ul>li:first-child>.nav>.item:not(:first-child)").click(function(){
// 	var spans=$(".mass_ul>li:first-child>.nav>.content>span");
// 	var text=""
// 	for(var i=0;i<spans.length;i++){
// 		if($(spans[i]).css("display")!=="none"){
// 			text=$(spans[i]).attr("class");
// 			text=text=="one"?"一":text=="two"?"二":text=="three"?"三":"四";
// 		}
// 	}
// 	var class_name=$(this).text();
// 	$(this).text(text);
// 	class_name=class_name=="一"?"one":class_name=="二"?"two":class_name=="三"?"three":"four";
// 	$(".mass_ul>li:first-child>.nav>.content>span").addClass("hide");
// 	$(".mass_ul>li:first-child>.nav>.content>."+class_name).removeClass("hide");
// });

// 党员发展

$(".nav_ul li").click(function() {
  //点击当个li，背景颜色加深
  $(this)
    .addClass("whenTab")
    .siblings("li")
    .removeClass("whenTab");
  $(this)
    .children(".container")
    .children("p")
    .addClass("active");
  //点击更换图片
  $(this)
    .find(".pic1")
    .addClass("hide");
  $(this)
    .find(".pic2")
    .removeClass("hide");
  $(this)
    .siblings("li")
    .find(".pic1")
    .removeClass("hide");
  $(this)
    .siblings("li")
    .find(".pic2")
    .addClass("hide");
  //改变p标签字体等样式
  $(this)
    .children(".container")
    .children("p")
    .addClass("active");
  $(this)
    .siblings("li")
    .children(".container")
    .children("p")
    .removeClass("active");
  $(".nav_ul li .container>span").addClass("active");
  $(this)
    .children(".container")
    .children("span")
    .removeClass("active");
  //切换列表
  var index = $(this).index();
  console.log(index);
  $(this)
    .parents(".page")
    .find(".content_ul")
    .eq(index)
    .removeClass("hide")
    .siblings(".content_ul")
    .addClass("hide");
   console.log($(this).parents(".page").find(".content_ul"));
  if (index == 5) {
    window.location.href = 'normShow.html'
  }
  // if(index == 0){
  //     $('.mui-iframe-wrapper').css('display','block');
  // }else{
  // 	 $('.mui-iframe-wrapper').css('display','none');
  // }
});
////群众头部切换
//$(".mass_ul>li>.nav").click(function(){
//
//});
