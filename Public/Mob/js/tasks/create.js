$(function() {
    // 富文本初始化
    var E = window.wangEditor
    var editor = new E("#editor")
    editor.customConfig.menus = ["head", "bold", "italic", "underline", "image"]
    editor.create()
    // 富文本 结束

    $(".back-btn").click(function(event) {
        /* 返回上一层 */
        window.history.back()
    })

    /**
     * 发布按钮
     */
    $(".publish").click(function(event) {
        let data = validator()
        console.log("data", data)

        if (data) {
            $.ajax({
                url: DOMAIN + "/Mob/Tasks/ajaxSave",
                type: "POST",
                data: data,
                dataType: "json",
                success: function(res) {
                    console.log("res", res)
                    if (res.status === 0) {
                        alert("发布成功")
                        window.history.back()
                    } else {
                        alert("发布失败,请稍后重试")
                    }
                },
                error: function(error) {
                    console.log("error", error)
                    alert("发布失败,请稍后重试")
                }
            })
        }
    })

    /**
     * 表单数据验证
     */
    function validator() {
        $form = $("#form-data")
        let formData = []

        if ($("#tasks-title").val() === "") {
            alert("未填写任务标题")
            return
        }

        if (valueIndex == false && $(".arrow").length !== 0) {
            alert("请选择实施主体")
            return
        }

        let startDate = dateStrChangeTimeTamp($("#start-date-input").val())
        let endDate = dateStrChangeTimeTamp($("#end-date-input").val())
        if (startDate - 1 > endDate) {
            alert("开始或结束时间不合法")
            return
        }

        if ($("#tasks-contents").val() === "") {
            alert("未填写任务内容")
            return
        }

        if (editor.txt.html() === "<p><br></p>") {
            alert("未填写会具体工作事项")
            return
        }
        // 将富文本数据插入表单中
        $("#workItem").val(editor.txt.html())

        // $('#start-date-input').val()
        $("#status").val(2)

        formData = $form.serialize()
        return formData
    }

    /**
     * 附件上传
     */
    $(".add-input").change(function() {
        let fd = new FormData()
        fd.append("file", $(".add-input").get(0).files[0])
        $.ajax({
            url: "/Mob/Tasks/ajaxUploadAnnex",
            type: "post",
            processData: false,
            contentType: false,
            dataType: "JSON",
            data: fd,
            success: function(res) {
                console.log(res)
                if (res.status === 0) {
                    Html =
                        '<div class="item" data-id="' +
                        res.data.id +
                        '"><i class="img ' +
                        res.data.type +
                        '"></i><span class="name">' +
                        res.data.name +
                        '</span><i class="remove"></i></div>'
                    $(".g-attachment-list").prepend(Html)
                    addFiles(res.data["id"])
                    $(".loading").addClass("hidden")
                    console.log("上传成功", res.data)
                } else {
                    // $('.loading .name').html(res.data.msg);
                    alert(res.msg)
                    $(".progress-tips").html(0)
                    $(".progress-percent").css("width", "1%")
                    var loadingtxt = window.setTimeout(function() {
                        $(".loading").addClass("hidden")
                    }, 2000)
                }
            },
            error: function(err) {
                console.log("err", err)
                alert("上传发生意外，请稍后再试！")
            }
        })
    })

    // 删除 remove
    $(".g-attachment-list").on("click", ".remove", function() {
        console.log("删除附件", event)
        var fileID = $(this)
            .parent(".item")
            .attr("data-id")
        if (fileID) {
            delFiles(fileID)
        }
        $(this)
            .parent(".item")
            .remove()
    })

    // 开始时间回显
    $("#start-date-input").change(function(event) {
        $(".start-date-text").html($("#start-date-input").val())
    })
    // 结束时间回显
    $("#end-date-input").change(function(event) {
        $(".end-date-text").html($("#end-date-input").val())
    })

    /**
     * 事件触发
     * 解决中午输入下多次触发问题
     * @param  {[type]} search [description]
     * @return {[type]}        [description]
     */
    $('#search-inp').off()
    .on('input',function(){
        if($(this).prop('comStart')) return;
        //输入完成后进行的操作
        console.log("input",$(this).val());
        let keyword = $(this).val()
        searchAction(keyword)

    })
    .on('compositionstart',function(){
        $(this).prop('comStart', true);
    })
    .on('compositionend',function(){
        $(this).prop('comStart', false);
        //输入完成后进行的操作
        console.log("compositionend",$(this).val());
        let keyword = $(this).val()
        searchAction(keyword)
    })
})

/**
 * 搜索过滤器
 */
function searchAction(keyword){
    if (!keyword){
        $('.content-body').find('.check-item').css('display','')
        return
    }
    // 所有列表Dom
    var items = $('.content-body').find('.check-item')
    items.each(function(index, el) {
        if ($(el).find('.check-text').text().search(keyword) == -1) {
            $(el).css('display','none')
        }else{
            $(el).css('display','')
        }
    });
}

/**
 * 实施人员
 * @param  {boolean} bool true打开 false关闭
 */
function onShowChange(bool) {
    console.log("dasdasd")
    if (bool) {
        $(".form-cover").addClass("active")
    } else {
        // 数据绑定到表单中
        $("#implementer").val(valueIndex)
        $(".arrow")
            .find(".value-text")
            .html(valueText.toString())
        $(".form-cover").removeClass("active")
        $(".g-search-box-small").removeClass("focus")
    }
}

/**
 * 过滤框事件
 * @param  {[type]} bool [description]
 * @return {[type]}      [description]
 */
function onSearchStatus(bool) {
    if (bool) {
        $(".g-search-box-small").addClass("focus")
    } else {
        // 如果输入框中有值 什么都不处理
        if ($('#search-inp').val()) {
            return
        }
        // 搜索框失去焦点
        $('.content-body').find('.check-item').css('display','')
        $(".g-search-box-small").removeClass("focus")
    }
}


/**
 * 实施人员选中事件
 * @param  {[type]} id [description]
 * @return {[type]}      [description]
 */
function onCheckChange(obj, id, name) {
    console.log("onCheckChange", id)
    let checkIco = $(obj).find(".check-ico")

    console.log("插入前", valueIndex, valueText)
    if (checkIco.hasClass("active")) {
        let index = valueIndex.findIndex(n => n == id)
        valueIndex.splice(index, 1)
        valueText.splice(index, 1)
        checkIco.removeClass("active")
    } else {
        valueIndex.push(id)
        valueText.push(name)
        checkIco.addClass("active")
    }
    console.log("插入后", valueIndex, valueText)
    // $($.parseHTML(this) + "> i").addClass('active')
}

/**
 * 添加文件ID
 */
function addFiles(fileid) {
    var fileId = $("#file_id").val()
    fileId = fileId ? fileId.split(",") : []
    fileId.push(fileid)
    $("#file_id").val(fileId)
}
/**
 * 删除文件ID
 */
function delFiles(fileid) {
    var fileId = $("#surplus_file_id").val()
    fileId = fileId ? fileId.split(",") : []
    fileId.push(fileid)
    $("#surplus_file_id").val(fileId)
}

/**
 * 日期转时间戳 单位：毫秒
 * @param  {string} dateStr
 * @return {int}
 */
function dateStrChangeTimeTamp(dateStr) {
    dateStr = dateStr.substring(0, 19)
    dateStr = dateStr.replace(/-/g, "/")
    var timeTamp = new Date(dateStr).getTime()
    return timeTamp
}
