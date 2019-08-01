// 状态切换
function stateSwitch(status){
    console.log(status);
    var cover = $('.cover');
    var type = $('.active').attr('date-type');
    if (1 == status) {
        cover.addClass('right');
    }else {
        cover.removeClass('right');
    }
    console.log('type', type);
    // 已有数据 不用加载
    if (pageData['tab'+type+status]) {
        console.log('不用加载数据', pageData);
        render(pageData['tab'+type+status]);
        return;
    }

    let res = ajaxLoadData(type, status, page);
    if (0 === res.status) {
        pageData['tab'+type+status] = res.data;
        console.log('返回数据',res.data);
        render(res.data);
    }
}

// 渲染数据
function render(data){
    console.log('data', pageData);
    $('.total').html('任务总数：' + data.total);
    $('.ing').html('进行中：' + data.ing);
    $('.end').html('已完成：' + data.end);
    var html = '';
    if (!data.list) {
        console.log('无数据渲染');
        html = '<div class="g-no-data"></div>';
    }else {
        $.each(data.list, function(i, item){
            html += "<div class=\"item\"><a class=\"top\" href=\'"
            +baseurl+"/Mob/Tasks/detail?id="
            +item.id+"\'><h3 class='title'>"
            +item.tasks_title+"</h3><p class='info'><span>发布人："
            +item.send_user+"</span><span>发表日期："
            +item.create_at+"</span></p><hr class='cutting' /><div class='main-content'><div class='left'><p class='content-item'>开始时间："
            +item.start_date+"</p><p class='content-item'>结束时间："
            +item.end_date+"</p><p class='content-item'>工作内容："
            +item.tasks_contents+"</p></div><div class='right-btn "
            +item.class+"\'>"
            +item.status+"</div></div></a></div>";
        });
    }
    $('.todo-list__list').html(html);
}

/**
 * 异步加载数据
 * @param  int type   类型 0支部|1小组|2个人|3纪检
 * @param  int status 为 2 时只获取 进行中的数据
 * @param  int page   页码 默认 1
 */
function ajaxLoadData(type, status, page=1, keyword=''){
    console.log('加载中..');
    var ret = $.ajax({
        url: baseurl + '/Mob/Tasks/ajaxGetList',
        async: false,
        type: 'POST',
        dataType: 'json',
        data: { type: type, status:status, page:page, keyword:keyword},
        complete:function(){
            console.log('请求结束..');
        }
    })
    return ret.responseJSON;
}

/**
 * 显示遮罩层
 */
function onAddToggle(){
    let addContainer = $('.add-container')
    let active = addContainer.hasClass('active')

    if (active) {
        addContainer.removeClass('active')
        return ;
    }
    addContainer.addClass('active')
    return ;
}

/**
 * 任务类型遮罩层
 */
function onChooseTypeToggle(){
    let chooseType = $('.choose-type')
    let active = chooseType.hasClass('active')
    $('.add-container').removeClass('active')
    if (active) {
        chooseType.removeClass('active')
        return ;
    }
    chooseType.addClass('active')
    return ;
}
