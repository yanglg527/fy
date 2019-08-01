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
        if (data) {
            $.ajax({
                url: DOMAIN + "/Vote/Index/ajaxSave",
                type: "POST",
                data: data,
                dataType: "json",
                success: function(res) {
                    console.log('push', res)
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

        if ($("#title").val() === "") {
            alert("投票主题不能为空")
            return
        }

        if ($("#branchs").val() === "") {
            alert("必须指定参与投票的支部")
            return
        }

        if ($("#roles").val() === "") {
            alert("必须指定参与投票的角色")
            return
        }

        let m = $('#selective').val().split(",");
        if (m.length < 2) {
            alert("必须选择两位以上党员")
            return
        }
        let multiplyCount = $('#multiply_count').val()
        if( multiplyCount === '' || multiplyCount > backupText.length){
            alert("党员投票数不合法")
            return
        }

        if ($("#start-date-input").val() === '') {
            alert("截止日期不能为空")
            return
        }

        // if (editor.txt.html() === "<p><br></p>") {
        //     alert("未填写内容简介")
        //     return
        // }
        //
        $("#editor-content").val(editor.txt.html())
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
            url: "/Vote/Index/ajaxSaveVoteCover",
            type: "post",
            processData: false,
            contentType: false,
            dataType: "JSON",
            data: fd,
            success: function(res) {
                console.log(res)
                if (0 === res.status) {
                    $(".g-attachment-list")
                    .find('.img')
                    .css('background-image','url('+res.data.show_path+')')
                    $('#cover').val(res.data.show_path)
                    return
                }
                alert(res.msg)
            },
            error: function(err) {
                console.log("err", err)
                alert("上传发生意外，请稍后再试！")
            }
        })
    })

    // 重新选择封面图片
    $(".g-attachment-list").on("click", ".clickCover", function() {
        $(".add-input").click();
    })

    // 投票时间回显
    $("#start-date-input").change(function(event) {
        $(".start-date-text").html($("#start-date-input").val())
    })
    // 全选
    $('.select-all').click(function(event) {
        /* 弃用 全选OR反选 */
        // $('.form-object-user').find('.check-item').click();
        // 全选OR取消全选
        let select = $('.select-all').find('.check-ico').hasClass("active")
        $checkAllDom = $('.form-object-user').find('.check-item')
        console.log('dom', $checkAllDom)
        if (isSelect) {
            $checkAllDom.each(function(index, el) {
                if ($(el).find('.check-ico').hasClass('active')) {
                    el.click()
                }
            });
            $('.select-all').find('.check-ico').removeClass('active')
        }else {
            $checkAllDom.each(function(index, el) {
                if (!$(el).find('.check-ico').hasClass('active')) {
                    el.click()
                }
            });
            $('.select-all').find('.check-ico').addClass('active')
        }
        isSelect = !isSelect
    });
})

/**
 * 恶心的遮罩层
 * @param  {string} boxClassName 类名 .form-range|.form-roles|.form-object|.form-object-user
 * @param  {bool} bool   true 开启遮罩 false 关闭遮罩
 */
function onShowChange(boxClassName, bool) {
    if (bool) {
        $(boxClassName).addClass("active")
        // 选中初始化
        console.log('选中初始', isSelect)
        if (boxClassName === '.form-object-user' && isSelect) {
            $('.select-all').find('.check-ico').addClass("active")
        }
    } else {
        let valueText

        switch (boxClassName) {
            // 投票范围
            case '.form-range':
                // 数据绑定到表单中
                $("#branchs").val(branchsValue)
                valueText = branchsText
                // 重新加载 监督员列表
                getUserBybranchId(branchsValue.toString(), '.form-supervisor')
                break;

            // 查看权限
            case '.form-roles':
                $("#roles").val(rolesValue)
                valueText = rolesText
                break;

            // 投票对象
            case '.form-object':
                valueText = backupBranchText
                // 重新加载列表数据
                getUserBybranchId(backupBranchValue.toString(), '.form-object-user')
                break;

            // 已选对象
            case '.form-object-user':
                $("#selective").val(backupText)
                valueText = backupText
                $('#multiply_count').val(backupText.length)
                break;

            // 监督员
            case '.form-supervisor':
            console.log(boxClassName, supervisorText)
                $("#supervisor").val(supervisorValue)
                valueText = supervisorText
            break;
        }

        // 拼装类名
        let valueTextDom = boxClassName + '-value-text'
        $(valueTextDom).html(valueText.toString())
        $(boxClassName).removeClass("active")
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
        $(".g-search-box-small").removeClass("focus")
    }
}

/**
 * 万恶的遮罩层数据更改处理的方法
 * @param  {object} obj          [description]
 * @param  {int}    id           [description]
 * @param  {string} name         [description]
 * @param  {object} boxClassName [description]
 */
function onCheckChange(obj, id, name, boxClassName) {
    let checkIco = $(obj).find(".check-ico")

    switch (boxClassName) {
        // 投票范围
        case '.form-range':
            // console.log("插入前", branchsValue, branchsText)
            // add or del
            if (checkIco.hasClass("active")) {
                let index = branchsValue.findIndex(n => n == id)
                branchsValue.splice(index, 1)
                branchsText.splice(index, 1)
                checkIco.removeClass("active")
            } else {
                branchsValue.push(id)
                branchsText.push(name)
                checkIco.addClass("active")
            }
            // console.log("插入后", branchsValue, branchsText)
            break;

        // 查看权限
        case '.form-roles':
            // console.log("插入前", rolesValue, rolesText)
            // add or del
            if (checkIco.hasClass("active")) {
                let index = rolesValue.findIndex(n => n == id)
                rolesValue.splice(index, 1)
                rolesText.splice(index, 1)
                checkIco.removeClass("active")
            } else {
                rolesValue.push(id)
                rolesText.push(name)
                checkIco.addClass("active")
            }
            // console.log("插入后", rolesValue, rolesText)
            break;

        // 投票对象
        case '.form-object':
            // console.log("插入前", backupBranchValue, backupBranchText)
            // add or del
            if (checkIco.hasClass("active")) {
                let index = backupBranchValue.findIndex(n => n == id)
                backupBranchValue.splice(index, 1)
                backupBranchText.splice(index, 1)
                checkIco.removeClass("active")
            } else {
                backupBranchValue.push(id)
                backupBranchText.push(name)
                checkIco.addClass("active")
            }
            // console.log("插入后", backupBranchValue, backupBranchText)
            break;

        // 已选对象
        case '.form-object-user':
            // console.log("插入前", backupValue, backupText)
            // add or del
            if (checkIco.hasClass("active")) {
                let index = backupValue.findIndex(n => n == id)
                backupValue.splice(index, 1)
                backupText.splice(index, 1)
                checkIco.removeClass("active")
            } else {
                backupValue.push(id)
                backupText.push(name)
                checkIco.addClass("active")
            }
            break;

            // 可见范围
            case '.form-visiblerange':
                console.log(id, name)
                // visiblerangeValue = id
                // visiblerangeText = name
                $('#visiblerange').val(id)
                $('.form-visiblerange-value-text').html(name)
                $(boxClassName).removeClass("active")
            break;

            // 监督员
            case '.form-supervisor':
                // add or del
                if (checkIco.hasClass("active")) {
                    let index = supervisorValue.findIndex(n => n == id)
                    supervisorValue.splice(index, 1)
                    supervisorText.splice(index, 1)
                    checkIco.removeClass("active")
                } else {
                    supervisorValue.push(id)
                    supervisorText.push(name)
                    checkIco.addClass("active")
                }
                break;
    }
}

/**
 * 按支部获取用户
 * @param  {[type]} ids [description]
 * @return {[type]}     [description]
 */
function getUserBybranchId(ids, classDom){
    if (ids) {
        $.ajax({
            url: "/Vote/Index/getUserBybranchId?branchId="+ids,
            type: "get",
            processData: false,
            contentType: false,
            dataType: "JSON",
            success: function(res) {
                // console.log('res', res)
                if (0 === res.status) {
                    // 遮罩层DOM
                    var dom = $(classDom)
                    let html = ''

                    if(res.data){
                        let resData = res.data
                        dom.find('.check-item').remove()
                        let classname = '.form-object-user'
                        for(j = 0; j < resData.length; j++) {
                            html += "<div class=\"check-item\" onclick=\"onCheckChange(this, "
                            + resData[j]['uid'] +",'"
                            + resData[j]['realname'] +"', '"+classDom+"')\"><i class=\"check-ico\"></i><span class=\"check-text\">"
                            +resData[j]['realname']+"</span></div>"
                        }
                        resetSelected(classDom)
                    }else{
                        // 无数据
                        html = '<div class="g-no-data"></div>'
                    }
                    dom.find('.content-body').append(html)
                }
            },
            error: function(err) {
                // console.log("err", err)
                alert("服务器繁忙，请稍后再试！")
            }
        })
    }
    return
}

/**
 * 清除已选数据
 */
function resetSelected(classDom){
    switch (classDom) {
        case '.form-supervisor':
            supervisorValue = []
            supervisorText = []
            $('.form-supervisor-value-text').html('')
            $('#supervisor').val("")
            break;

        case '.form-object-user':
            backupValue = []
            backupText = []
            $('.form-object-user-value-text').html('')
            $('#selective').val("")
            break;
    }
}
