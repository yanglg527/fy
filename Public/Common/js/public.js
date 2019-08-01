var TIP_CONNECTION_FAILED = '无法连接服务器,请稍后再试!';

$.toast = function(content, confirm){

    $("#alert-block .content-item").text(content);
    $("#alert-block > div").modal({
        closeViaDimmer: false,
        dimmer: false
    });
    confirm && $("#alert-block").delegate('.am-modal-btn','click',confirm);

};


/**
 *  ajax-post函数
 *  @param ajaxMsg 数据
 **/
$.ajaxFunc =function(ajaxMsg){

    var async =false;
    var contentType = 'application/x-www-form-urlencoded';
    var processData = true;

    if(typeof (ajaxMsg['async']) =="undefined"){
        async =true;
    }

    if(ajaxMsg['upfile']){
        contentType = processData = false;
    }

    $.ajax({

        type: "post",
        url: ajaxMsg['url'],
        data: ajaxMsg['data'],      //不能以data、a等为索引
        cache: false,
        contentType: contentType,
        processData: processData,
        async: async,
        dataType: "json",
        success: function (sender) {

            if(sender['status'] ==-1){

                // showLogin();

            }else if(sender['status'] ==0) {

                ajaxMsg['func'] && ajaxMsg['func'](sender);

            }else {

                $.toast(sender.msg);

            }

        },
        error: function () {

            $.toast(TIP_CONNECTION_FAILED);
            console.log(124);

        }

    });

};



// 检验表单的正确性
$.fn.checkForm = function(){

    var isError = true;

    function node(ele, name){
        return $(ele+"[name='"+name+"']");
    }

    $.each($(this).serializeArray(), function(index, field){

        if(!field.value){

            var msg = node('input', field.name).attr("data-tip") ||
                node('textarea', field.name).attr("data-tip");
            $.toast(msg);
            isError = false;
            return false;

        }

    });

    return isError;
};

/**
 * 初始化popup二级页面
 * @param msg
 * */
$.initPopups = function(msg){

    var popupNode = $("#detail-popup");
    var header;
    // 装popup页面代码的对象
    var popups = {};

    var show_confirm = true;

    if(msg.show_confirm !== undefined){
        show_confirm = msg.show_confirm;
    }

    !show_confirm && function () {
        popupNode.find(".confirm-item").addClass('hidden');
    }();



    $.getPopup = function(type, func){

        $.ajax({
            url: msg.url,
            type: 'post',
            data: {type:type},
            dataType: 'json',
            cache: false,
            success: function(sender){
                popups[type] = sender['data'];
                func(popups[type]);
            }
        });
    };

    function makePopup(html){

        popupNode.find(".popup-header").text(header);
        popupNode.find(".content-popup").html(html);
        popupNode.modal();

    }

    $(msg.list_node).click(function(event){

        if($(this).attr('data-noTarget')){

            return false;

        }

        var headerNode = msg.header_node === undefined ? '.text-item' : msg.header_node;
        var prefix = '';
        header = prefix + $(this).find(headerNode).text();
        var type = $(this).attr('type');
        !popups[type] ? $.getPopup(type, makePopup) : makePopup(popups[type]);

    });


};