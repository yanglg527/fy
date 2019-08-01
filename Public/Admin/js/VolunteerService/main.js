$(function() {
// 表单验证
var $form = $('#form-data');

$form.validator({
    validateOnSubmit: true,
    submit: function() {
        if(this.isFormValid()) {
            console.log('提交中。。。');
            $('.btn-loading-example').on('click', function(){
                $.AMUI.progress.start();
            })
            $.ajax({
              url : BASEURL + "/Admin/VolunteerService/ajaxSave",
              type : "POST",
              data : $form.serialize(),
              dataType :"json",
              success : function(res){
                  $.AMUI.progress.done();
                  console.log(res);
                  if (0 == res.status){
                        self.location=document.referrer;
                  }else {
                        alert_modal(res.msg);
                     }
                  },
              error : function(error){
                      close_loading();
                      alert_modal(error.msg);
                      console.log(error);
                  }
                })
        }
        return false;
    }
})

var $tooltip = $('<div id="vld-tooltip">提示信息！</div>');
$tooltip.appendTo(document.body);
$form.validator();
var validator = $form.data('amui.validator');
$form.on('focusin focusout', '.am-form-error input', function(e) {
    // console.log(e);
        if (e.type === 'focusin') {
          var $this = $(this);
          var offset = $this.offset();
          var msg = $this.data('foolishMsg') || validator.getValidationMessage($this.data('validity'));
          $tooltip.text(msg).show().css({
            left: offset.left + 10,
            top: offset.top + $(this).outerHeight() + 10
          });
          console.log('假');
          // $('.disabled-btn').attr('disabled', 'disabled');
        } else {
            console.log('hhh');
            // $('.disabled-btn').removeAttr('disabled');
          $tooltip.hide();
        }

        // console.log('验证通过',$form.validator('isFormValid'));
    })


    // 会议记录 富文本
    var $textArea = $('[name=content');
    var editor = UE.getEditor('content',{
        initialFrameHeight: 400,
        initialFrameWidth: 750,
    });
    // var $form = $('#form-data');

    $form.validator({
    submit: function() {
      // 同步编辑器数据
      editor.sync();
      var formValidity = this.isFormValid();
      // 表单验证未成功，而且未成功的第一个元素为 UEEditor 时，focus 编辑器
      if (!formValidity && $form.find('.' + this.options.inValidClass).eq(0).is($textArea)) {
        editor.focus();
      }
      console.warn('验证状态：', formValidity ? '通过' : '未通过');
      return false;
    }
    });
    // 编辑器内容变化时同步到 textarea
    editor.addListener('contentChange', function() {
    editor.sync();

    // 触发验证
    $('[name=content]').trigger('change');
    })
    // end
    // 附件 开始
    $('#easyContainer').easyUpload({
      allowFileTypes: '*.jpg;*.doc;*.pdf;*.png;*.mp4;*.ppt;*.xlsx;*.xls;',//允许上传文件类型，格式';*.doc;*.pdf'
      allowFileSize: 100000,//允许上传文件大小(KB)
      selectText: '选择附件',//选择文件按钮文案
      multi: true,//是否允许多文件上传
      multiNum: 9,//多文件上传时允许的文件数
      showNote: true,//是否展示文件上传说明
      note: '提示：最多上传9个文件，支持格式为doc、pdf、jpg、png、ppt、xls',//文件上传说明
      showPreview: true,//是否显示文件预览
      url: BASEURL+'/Admin/ThreeMeeting/ajaxUploadAnnex',
      fileName: 'file',
      // formParam: {
        // token: $.cookie('token_cookie')//不需要验证token时可以去掉
      // },//文件filename以外的配置参数，格式：{key1:value1,key2:value2}
      timeout: 30000,//请求超时时间
      okCode: 200,
      // 成功回调
      successFunc: function(res) {
          let fileId = [];
          for (var i = 0; i < res['success'].length; i++) {
              fileId.push(res['success'][i]['data']['id']);
          }
          $('#file_id').val(fileId);
      },
      // 失败回调
      errorFunc: function(res) {
        console.log('失败回调', res);
      },
      // 删除回调
      deleteFunc: function(res) {
          let delFileId = [];
          for (var i = 0; i < res['success'].length; i++) {
              delFileId.push(res['success'][i]['data']['id']);
          }
          $('#surplus_file_id').val(delFileId);
      }
    })
    // 附件 end

    // 时间验证
    var $alert = $('#my-alert');
    $('#startDate').datepicker().
      on('changeDate.datepicker.amui', function(event) {
        // 开始结束时间对比 已弃用
        if (event.date.valueOf() > endDate.valueOf()) {
            $tooltip.text('时间格式不合法！').show().css({
              left: offset.left + 10,
              top: offset.top + $(this).outerHeight() + 10
            });
          // $alert.find('p').text('开始日期应小于结束日期！').end().show();
        } else {
          $tooltip.hide();
          startDate = new Date(event.date);
          $('#start_date').val($('#startDate').data('date'));
        }
        $(this).datepicker('close');
      });
    // 弃用 结束时间
    $('#endDate').datepicker().
      on('changeDate.datepicker.amui', function(event) {
          var $this = $(this);
          var offset = $this.offset();
        if (event.date.valueOf() < startDate.valueOf()) {
            $tooltip.text('开始日期应小于结束日期！').show().css({
              left: offset.left + 10,
              top: offset.top + $(this).outerHeight() + 10
            });
        } else {
          $tooltip.hide();
          endDate = new Date(event.date);
          $('#end_date').val($('#endDate').data('date'));
        }
        $(this).datepicker('close');
      })
      // 时间验证 end

      // 放弃保存
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

})

// 提交或保存
function onStatus(status=0)
{
    $('#status').attr('value', status);
}
