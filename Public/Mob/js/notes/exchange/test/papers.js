$(".normal .danxuan dd").click(function() {
    // $(this).parent().find('input').removeAttr('checked');
    $(this)
        .find(".choose")
        .removeClass("hide")
        .siblings(".air")
        .addClass("hide")
    $(this)
        .find("input")
        .attr("checked", "checked")
    $(this)
        .addClass("answer-col")
        .siblings("dd")
        .removeClass("answer-col")
    $(this)
        .siblings("dd")
        .find(".air")
        .removeClass("hide")
        .siblings(".choose")
        .addClass("hide")
        $(this).siblings("dd").find('input') .attr("checked", undefined)
    
})

$(".normal .duoxuan dd").click(function() {
    // $(this).parent().find('input').removeAttr('checked');
    //要判断是选中还是没选中
    if (
        $(this)
            .find("input")
            .attr("checked") == "checked"
    ) {
        $(this)
            .find(".choose")
            .addClass("hide")
            .siblings(".air")
            .removeClass("hide")
        $(this)
            .find("input")
            .attr("checked", "")
        $(this).removeClass("answer-col") //.siblings("dd").removeClass("answer-col")
        //	$(this).siblings(".choose").addClass("hide"); //.siblings("dd").find(".air").removeClass('hide')
    } else {
        $(this)
            .find(".choose")
            .removeClass("hide")
            .siblings(".air")
            .addClass("hide")
        $(this)
            .find("input")
            .attr("checked", "checked")
        $(this).addClass("answer-col") //.siblings("dd").removeClass("answer-col")
        //	$(this).siblings(".choose").addClass("hide"); //.siblings("dd").find(".air").removeClass('hide')
    }
})

//公开模式的题目
$(".open .danxuan dd").click(function() {
	//console.log($(this).parent().find('input'))
	var right_answer_str = $(this)
	.parent()
	.find('input[name="right_answer_str"]')
	.val()
// console.log(right_answer_str)
var currentval = $(this)
	.find("input")
	.val() //当前选项

	if($(this).find("input").attr("checked") != undefined){
        //取消选中
		console.log('undefined')
		$(this).find("input").attr("checked", undefined)
		if (currentval === right_answer_str) {
			//取消绿色的
			$(this)
			.removeClass("right-answer-col")
			$(this).find(".choose-right").addClass("hide").siblings(".air").removeClass("hide")
		}else{
            //取消红色的
            $(this)
            .removeClass("answer-col")
            $(this).find(".choose").addClass("hide").siblings(".air").removeClass("hide")
		}
	}else{
		console.log('noooooooooooooooundefined')
	// console.log(currentval)
$(this).find("input").attr("checked", "checked")
    var rightdd = $(this)
        .parent()
        .find("dd")[right_answer_str] //正确答案的那个dd
    if (currentval === right_answer_str) {
        //选中显示绿色 #0cb07d

        $(this)
            .addClass("right-answer-col")
            .siblings("dd")
            .removeClass("right-answer-col")
        $(this)
            .find(".choose-right")
            .removeClass("hide")
            .siblings(".air")
            .addClass("hide")
        $(this)
            .siblings("dd")
            .find(".air")
            .removeClass("hide")
            .siblings(".choose")
            .addClass("hide")
        $(this)
            .siblings("dd")
            .removeClass("answer-col")
    } else {
        //正确答案显示绿色 当前显示红色 点击红色，其他红色要消失
        $(this)
            .find(".choose")
            .removeClass("hide")
            .siblings(".air")
            .addClass("hide")
        $(this)
            .addClass("answer-col")
            .siblings("dd")
            .removeClass("answer-col")
        $(this)
            .siblings("dd")
            .find(".air")
            .removeClass("hide")
            .siblings(".choose")
            .addClass("hide")
            $(this).siblings("dd").find('input').attr('checked',undefined) //其他红色不选中
        // $(this).find(".air").addClass('hide')
        $(rightdd).addClass("right-answer-col")
        $(rightdd)
            .find(".choose-right")
            .removeClass("hide")
            .siblings(".air")
            .addClass("hide")
            $(rightdd).find('input').attr('checked','checked')
    }



	}
   
    // $(this).find(".choose").removeClass("hide").siblings(".air").addClass("hide");
    // $(this).find("input").attr("checked",'checked');
    // $(this).addClass("answer-col").siblings("dd").removeClass("answer-col");
    // $(this).siblings("dd").find(".air").removeClass('hide').siblings(".choose").addClass("hide");
})

$(".open .duoxuan dd").click(function() {
    console.log(
        $(this)
            .find("input")
            .attr("checked")
    )
    if ($(this).find("input").attr("checked") != undefined) {
        $(this)
            .find("input")
            .attr("checked", undefined)
        $(this).find(".choose").addClass("hide").siblings(".air").removeClass("hide")
        $(this)
            .find(".choose-right")
            .addClass("hide")
            .siblings(".air")
            .removeClass("hide")
        $(this).removeClass("answer-col")
        $(this).removeClass("right-answer-col")
    } else {
        $(this)
            .find("input")
            .attr("checked","checked")
        var itemIndex = parseInt(
            $(this).find("input").val(),10)
        console.log("item", itemIndex)
        var right_answer_map = JSON.parse(
            $(this)
                .parent()
                .find('input[name="right_answer_str"]')
                .val()
        )
        if (right_answer_map[itemIndex] === 1) {
            $(this)
                .addClass("right-answer-col")
                // .siblings("dd")
                // .removeClass("right-answer-col")
            $(this)
                .find(".choose-right")
                .removeClass("hide")
                .siblings(".air")
                .addClass("hide")
            // $(this)
            //     .siblings("dd")
            //     .find(".air")
           
            //     .addClass("hide")
            // $(this)
            //     .siblings("dd")
            //     .removeClass("answer-col")
        } else {
            //正确答案显示绿色 当前显示红色
            $(this)
                .find(".choose")
                .removeClass("hide")
                .siblings(".air")
                .addClass("hide")
            $(this)
                .addClass("answer-col")
                // .siblings("dd")
                // .removeClass("answer-col")
            // $(this).siblings("dd").find(".air").removeClass('hide').siblings(".choose").addClass("hide")
            // $(this).find(".air").addClass('hide')
            // $(rightdd).addClass("right-answer-col")
            // $(rightdd)
            //     .find(".choose-right")
            //     .removeClass("hide")
            //     .siblings(".air")
            //     .addClass("hide")
        }
    }

    //	console.log("is right",right_answer_map,)

    //console.log($(this).find("input").attr('checked'))
    // console.log("*****"+right_answer_str)
    // $(this).parent().find('input').removeAttr('checked');
    //要判断是选中还是没选中

    // if($(this).find("input").attr('checked')=='checked'){

    // 	$(this).find(".choose").addClass("hide").siblings(".air").removeClass("hide");
    // 	$(this).find("input").attr("checked",'');
    // 	$(this).removeClass("answer-col"); //.siblings("dd").removeClass("answer-col")
    // //	$(this).siblings(".choose").addClass("hide"); //.siblings("dd").find(".air").removeClass('hide')
    // }else{
    // 	$(this).find(".choose").removeClass("hide").siblings(".air").addClass("hide");
    // 	$(this).find("input").attr("checked",'checked');
    // 	$(this).addClass("answer-col"); //.siblings("dd").removeClass("answer-col")
    // //	$(this).siblings(".choose").addClass("hide"); //.siblings("dd").find(".air").removeClass('hide')
    // }
})
