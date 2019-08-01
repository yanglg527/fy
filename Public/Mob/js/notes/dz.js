/**
 * Created by jervis on 2017/5/4.
 */
$('.list').on('click','.collect', function () {
    var id = $(this).attr('data-id');
    var c = this
    $.ajax({
        data: {
            id: id
        },
        type: 'POST',
        dataType: 'json',
        url: __root__ + '/Mob/PartyClass/ajax_correct',
        success: function (data) {
            if (data['status'] == 0) {
                alert(data.msg);
                if(data.data.is_correct == 1){
                    $(c).addClass('active')
                }else{
                    $(c).removeClass('active')
                }
                $(c).find('.count').text(data.data.count)
            } else {
                alert(data.msg);
            }
        },
        error: function (data) {
            alert('网络异常');
        }
    })
})

$('.list').on('click','.like', function () {
    var id = $(this).attr('data-id');
    var c = this
    $.ajax({
        data: {
            id: id
        },
        type: 'POST',
        dataType: 'json',
        url:  __root__ + '/Mob/PartyClass/ajax_dz',
        success: function (data) {
            if (data['status'] == 0) {
                alert(data.msg);
                if(data.data.is_dz == 1){
                    $(c).addClass('active')
                }else{
                    $(c).removeClass('active')
                }
                $(c).find('.count').text(data.data.count)
            } else {
                alert(data.msg);
            }
        },
        error: function (data) {
            alert('网络异常');
        }
    })
})