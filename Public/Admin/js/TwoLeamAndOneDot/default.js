$(function() {
    var nowTemp = new Date();
       var nowDay = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0).valueOf();
       var nowMoth = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), 1, 0, 0, 0, 0).valueOf();
       var nowYear = new Date(nowTemp.getFullYear(), 0, 1, 0, 0, 0, 0).valueOf();
       var $myStart2 = $('#startDate');

       var checkin = $myStart2.datepicker({
         onRender: function(date, viewMode) {
           // 默认 days 视图，与当前日期比较
           var viewDate = nowDay;

           switch (viewMode) {
             // moths 视图，与当前月份比较
             case 1:
               viewDate = nowMoth;
               break;
             // years 视图，与当前年份比较
             case 2:
               viewDate = nowYear;
               break;
           }

           return date.valueOf() < viewDate ? 'am-disabled' : '';
         }
       }).on('changeDate.datepicker.amui', function(ev) {
           if (ev.date.valueOf() > checkout.date.valueOf()) {
             var newDate = new Date(ev.date)
             newDate.setDate(newDate.getDate() + 1);
             checkout.setValue(newDate);
         }
            $('#start_time').val($('#startDate').data('date'))
           checkin.close();
           // $('#endDate')[0].focus();
       }).data('amui.datepicker');
});

function confirm_model(){
  $('#add_content_modal').modal('close');
  var image_path=$("#upload_img_path").val();
       var html='';
       if (cz_type == 'add') {
       if (image_path == null || image_path == '') {
         html=$("#temp2").html();
       }else{
         html=$("#temp1").html();
         html = html.replace("#save_img_url#",image_path);
         html = html.replace("#img_url#",$("#upload_img_show")[0].src);

       };
       html = html.replace("#id#",type);
       html = html.replace("#content#",$("#content").val());
       if (type == 1) {
         $("#add_btn_1").before(html);
       }else if(type == 2){
         $("#add_btn_2").before(html);
       }else if(type == 3){
         $("#add_btn_3").before(html);
       };
      }else if(cz_type == 'edit'){
        var html='';
       if (image_path == null || image_path == '') {
        html=$("#temp4").html();
        div_obj.attr('data-type','temp2');
       }else{
        html=$("#temp3").html();
        div_obj.attr('data-type','temp1');
        html = html.replace("#save_img_url#",image_path);
        html = html.replace("#img_url#",$("#upload_img_show")[0].src);
       };
       html = html.replace("#content#",$("#content").val());
       div_obj.find('.am-thumbnail').remove();
       div_obj.append(html);
     };
}
function edit_content(obj) {
  cz_type = 'edit';
    div_obj=$(obj).parent().parent().parent();
    var data_type=$(obj).parent().parent().parent().attr('data-type');

    var content=$(obj).parent().find('p').html();
    $("#add_content_modal_title").text('修改');
    if (data_type == 'temp1') {
    var img_save=$(obj).parent().parent().find('img').attr('alt');
  var img_show=$(obj).parent().parent().find('img')[0].src;
        $("#upload_img_show").attr("src",img_show);
        $("#upload_img_path").val(img_save);
    }else{
        $("#upload_img_show").attr("src","{$article.cover_path|show_img}");
        $("#upload_img_path").val('');
    };
    $("#content").val(content);
    $('#add_content_modal').modal();
}

function del_content(obj){
  $(obj).parent().parent().parent().remove();
}

function submit_info(status) {
    let data={};
    let cq=[];
    let qj=[];
    let qx=[];
    data['theme']=$('#theme').val();
    if(data['theme'] == ''){
        alert_modal("主题不能为空");
        return false;
    }
    $('#cq input[name="cq"]:checked').each(function(){
        cq.push($(this).val());
    });
    $('#qj input[name="qj"]:checked').each(function(){
        qj.push($(this).val());
    });
    $('#qx input[name="qx"]:checked').each(function(){
        qx.push($(this).val());
    });
    data['id'] = $('#do_id').val();
    data['branchId'] = $('#branchId').val();
    data['type']=$('#type option:selected').val();
    data['step']=$('#step').val();
    data['start_time']=$('#start_time').val();
    data['mission']=$('#mission').val();
    data['claim']=$('#claim').val();
    data['info']=ue.getContent();
    data['file_id'] = $('#file_id').val();
    data['surplus_file_id'] = $('#surplus_file_id').val();
    data['cq']=cq;
    data['qj']=qj;
    data['qx']=qx;
    data['status']=status;

    $.ajax({
        data: {'data':data},
        type: 'POST',
        dataType: 'json',
        url: BASEURL + '/Admin/TwoLeamAndOneDot/ajaxSave',
        beforeSend: function () {
            loading();
        },
        success: function (data) {
            close_loading();
            if (data['status'] == 0) {
                self.location=document.referrer;
            } else {
                alert_modal(data.msg);
            }
        },
        error: function (data) {
            close_loading();
            alert_modal(data.msg);
        }
    })
};
$('#btn-cancel').click(function () {
    $('#confirm-text').text('确认取消编辑吗?');
    $('#my-confirm').modal({
        relatedTarget: this,
        onConfirm: function (options) {
            javascript :history.back(1);
        },
        onCancel: function () {

        }
    })
})

$(function() {
  $('#upload-form').validator({
    onValid: function(validity) {
      $(validity.field).closest('.am-margin-top').find('.am-alert').hide();
    },

    onInValid: function(validity) {
      var $field = $(validity.field);
      var $group = $field.closest('.am-margin-top');
      var $alert = $group.find('.am-alert');
      // 使用自定义的提示信息 或 插件内置的提示信息
      var msg = $field.data('validationMessage') || this.getValidationMessage(validity);

      if (!$alert.length) {
        $alert = $('<div class="am-alert am-alert-danger"></div>').hide().
          appendTo($group);
      }

      $alert.html(msg).show();
    }
  })
})

function delFile(id){
    $('#my-confirm').modal({
      relatedTarget: this,
      onConfirm: function(options) {
          $('.annex-item-'+id).remove();
          let surplusFileId = $('#surplus_file_id');
          let delFileId;
          let val = surplusFileId.val();
          if (val == '') {
              surplusFileId.val(id);
              return true;
          }
          delFileId = val.split(',');
          delFileId.push(id)
          surplusFileId.val(delFileId);
      },
      // closeOnConfirm: false,
      onCancel: function() {
          console.log('取消了');
      }
    });
}

function mutuallyExclusive(type, uid)
{
    console.log('用户的id是',uid);
    switch(type)
    {
    case 'cq':
        $("input[name='qx'][value='"+uid+"']").attr("checked", false);
        $("input[name='qj'][value='"+uid+"']").attr("checked", false);
        break;
    case 'qj':
        $("input[name='onall']").attr("checked", false);
        $("input[name='qx'][value='"+uid+"']").attr("checked", false);
        $("input[name='cq'][value='"+uid+"']").attr("checked", false);
        break;
    case 'qx':
        $("input[name='onall']").attr("checked", false);
        $("input[name='cq'][value='"+uid+"']").attr("checked", false);
        $("input[name='qj'][value='"+uid+"']").attr("checked", false);
        break;
    default:
    }
}
function onAll(){
    $("input[name='cq']").attr("checked", true);
}
