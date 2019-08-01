$(function(){
    var $form = $('#form-data');

    $form.validator({
        validateOnSubmit: true,
        submit: function() {
            if(this.isFormValid()) {
                loading('請稍等...')
                $('.btn-loading-example').on('click', function(){
                    $.AMUI.progress.start();
                })
                $.ajax({
                  url : BASEURL + "/Admin/Tasks/ajaxSave",
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
            if (e.type === 'focusin') {
              var $this = $(this);
              var offset = $this.offset();
              var msg = $this.data('foolishMsg') || validator.getValidationMessage($this.data('validity'));
              $tooltip.text(msg).show().css({
                left: offset.left + 10,
                top: offset.top + $(this).outerHeight() + 10
              });
            } else {
              $tooltip.hide();
            }
    })

    // 时间验证
    var $alert = $('#my-alert');
    var checkin = $('#startDate').datepicker({
        // 初始化时间验证器
        onRender: function(date, viewMode) {
        // 时间验证数据初始化
        var nowTemp = new Date();
        var nowDay = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0).valueOf();
        var nowMoth = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), 1, 0, 0, 0, 0).valueOf();
        var nowYear = new Date(nowTemp.getFullYear(), 0, 1, 0, 0, 0, 0).valueOf();

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
    }).
      on('changeDate.datepicker.amui', function(event) {
        // 开始结束时间对比 已弃用
        if (event.date.valueOf() > endDate.valueOf()) {
            $tooltip.text('时间格式不合法！').show().css({
              left: offset.left + 10,
              top: offset.top + $(this).outerHeight() + 10
            });
          $alert.find('p').text('开始日期应小于结束日期！').end().show();
        } else {
          $tooltip.hide();
          startDate = new Date(event.date);
          $('#start_time').val($('#startDate').data('date'));
          $('#start_time').blur(); // 自动给input失去焦点
        }
        $(this).datepicker('close');
    }).data('amui.datepicker');

    // 结束时间
    $('#endDate').datepicker({
        onRender: function(date, viewMode) {
            var inTime = checkin.date;
            var inDay = inTime.valueOf();
            var inMoth = new Date(inTime.getFullYear(), inTime.getMonth(), 1, 0, 0, 0, 0).valueOf();
            var inYear = new Date(inTime.getFullYear(), 0, 1, 0, 0, 0, 0).valueOf();
            // 默认 days 视图，与当前日期比较
            var viewDate = inDay;

            switch (viewMode) {
            // moths 视图，与当前月份比较
            case 1:
              viewDate = inMoth;
              break;
            // years 视图，与当前年份比较
            case 2:
              viewDate = inYear;
              break;
            }

            return date.valueOf() <= viewDate ? 'am-disabled' : '';
        }
    }).
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
          $('#end_time').val($('#endDate').data('date'));
          $('#end_time').blur(); // 自动给input失去焦点

        }
        $(this).datepicker('close');
      })
      // 时间验证 end

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
          url: BASEURL+'/Admin/Tasks/ajaxUploadAnnex',
          fileName: 'file',
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

    // 表单 动态加减
    $('#meetingType').change(function(){
        loading('数据加载中...')
        var url = '/Admin/Tasks/getBaseData'
        let val = $(this).children('option:selected').val()
        let text = $(this).children('option:selected').text()

        let branchs = $('#branchs-box') // 党支部
        let branchsSelect = $('#branchs-select')
        let target = $('#target-box')   // 实施人员
        let targetSelect = $('#target-select')
        let group = $('#group-box')     // 党小组
        let groupSelect = $('#group-select')


        if (2 == val && '个人' == text) {
            branchs.css('display','none')
            group.css('display','none')


            let len = $('#target-select option').length
            if (0 == len) {
                let $res = ajaxRequest(url, {type:val});
                let data = $res.data

                // 处理返回的数据
                data.forEach(function(item){
                    targetSelect.append("<option value='"+item.uid+"'>"+item.realname+"</option>")
                });
            }
            target.css('display','')
        }else if (0 == val && '党支部' == text) {
            target.css('display','none')
            group.css('display','none')

            let len = $('#branchs-select option').length
            if (0 == len) {
                let $res = ajaxRequest(url, {type:val});
                let data = $res.data
                // 处理返回的数据
                data.forEach(function(item){
                    branchsSelect.append("<option value='"+item.id+"'>"+item.name+"</option>")
                });
            }
            branchs.css('display','')
        }else if (1 == val && '党小组' == text) {
            branchs.css('display','none')
            target.css('display','none')

            let len = $('#group-select option').length
            if (0 == len) {
                let $res = ajaxRequest(url, {type:val});
                let data = $res.data
                // 处理返回的数据
                data.forEach(function(item){
                    // console.log(item);
                    groupSelect.append("<option value='"+item.id+"'>"+item.branch_name+'>>'+item.name+"</option>")
                });
            }
            group.css('display','')
        }else {
            branchs.css('display','none')
            branchsSelect.removeAttr('required')
            target.css('display','none')
            targetSelect.removeAttr('required')
            group.css('display','none')
            groupSelect.removeAttr('required')
        }
        close_loading()
    })
})

// 提交或保存
function onStatus(status=0)
{
    $('#status').attr('value', status);
}

// 附件删除
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

function ajaxRequest($url, $data){
     let res = $.ajax({
      url : BASEURL + $url,
      type : "POST",
      data : $data,
      async: false,
      // timeout:5,
      dataType :"json",
      beforeSend : function(){
          // console.log('加载中..')
      },complete: function(){
          // console.log('失败了')
          close_loading()
      }
  })
  return res.responseJSON
}
