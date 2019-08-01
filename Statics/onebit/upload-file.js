//插入提示框
$('head').append("<style>.hide{display: none!important;}.crop-dialog{position:fixed;display:inline-block;height:100%;vertical-align:middle;top:0;bottom:0;left:0;right:0;background:rgba(146,146,146,0.4);z-index:100000!important}.crop-dialog .crop-dialog-div{position:relative;display:inline-block;vertical-align:middle;margin-left:-270px;margin-top:-100px;top:50%;left:50%;max-width:100%;width:540px;background:#f8f8f8}.crop-dialog .crop-dialog-div .crop-dialog-hd{padding:15px 10px 5px 10px;font-size:1.8rem;font-weight:500;text-align:center;margin:0}.crop-dialog .crop-dialog-div .crop-dialog-bd{padding:15px 10px;text-align:center;border-bottom:1px solid #dedede;border-radius:2px 2px 0 0}.crop-dialog .crop-dialog-div .crop-dialog-footer{height:44px;overflow:hidden;display:table;width:100%;border-collapse:collapse}.crop-dialog-btn{display:table-cell!important;padding:0 5px;height:44px;-webkit-box-sizing:border-box!important;box-sizing:border-box!important;font-size:1.6rem;line-height:44px;text-align:center;color:#0e90d2;display:block;word-wrap:normal;text-overflow:ellipsis;white-space:nowrap;overflow:hidden;cursor:pointer;border-right:1px solid #dedede}.crop-dialog-btn:active{background:rgba(211,211,211,0.38)}</style>");
$('body').prepend("<div class='crop-dialog hide' id='crop-dialog'><div class='crop-dialog-div'><h4 class='crop-dialog-hd' id='crop-dialog-title'>系统提示</h4><div class='crop-dialog-bd' id='crop-dialog-text'>请先选择文件</div><div class='crop-dialog-footer'><span class='crop-dialog-btn'>确定</span></div></div></div>");
$('#crop-dialog .crop-dialog-btn').click(function () {
    $('#crop-dialog').addClass('hide');
})

$('head').append("<style>.show-img-div{position:fixed;z-index:999999;top:0;bottom:0;left:0;right:0;background:rgba(0,0,0,0.31)}.show-img-div .image-show{position:absolute;left:10%;top:10%;right: 10%;bottom: 10%;overflow: auto;text-align: center}.show-img-div img{width:auto;height:90%;margin: 0 auto}.show-img-div .image-close{color:white;position:absolute;top:10%;right:10%;font-size:20px;height:40px;width:40px;line-height:40px;text-align:center;border-radius:20px;cursor:pointer;background:rgba(0, 0, 0, 0.27)}.show-img-div .image-close:hover{color:#262626;background:rgba(255,255,255,0.22)}</style>");
$('body').prepend('<div class="show-img-div hide"><div class="image-show"><img class="show-img" src="default.png"/></div><div class="image-close">&times;</div></div>');

function alert_crop(content, title) {
    $('#crop-dialog-text').text(content)
    if (title != undefined) {
        $('#crop-dialog-title').text(title)
    }
    $('#crop-dialog').removeClass('hide');
}


var OneBitImageUploader = {

    create: function (option) {
        var oldOption = {
            server: '',
            swfPath: '',
            objDiv: '',
            uploadTitle: '点击上传封面',
            inputFileName: 'save_path',
            inputCoverName: 'cover',
            inputCoverDefault: '',
            inputFileDefault: '',
            showSrcDefault: '',
            emptySrc:'',
            width: '150px',
            height: '120px',
            type: 'upload',
            accept: {
                title: 'image',
                extensions: '*',
                mimeTypes: 'image/*'
            }

        }
        option.inputFileName = option.inputFileName == undefined ? oldOption.inputFileName : option.inputFileName;
        option.inputFileDefault = option.inputFileDefault == undefined ? oldOption.inputFileDefault : option.inputFileDefault;
        option.inputCoverName = option.inputCoverName == undefined ? oldOption.inputCoverName : option.inputCoverName;
        option.inputCoverDefault = option.inputCoverDefault == undefined ? oldOption.inputCoverDefault : option.inputCoverDefault;
        option.showSrcDefault = option.showSrcDefault == undefined ? oldOption.showSrcDefault : option.showSrcDefault;
        option.accept = option.accept == undefined ? oldOption.accept : option.accept;
        option.server = option.server == undefined ? oldOption.server : option.server;
        option.swfPath = option.swfPath == undefined ? oldOption.swfPath : option.swfPath;
        option.width = option.width == undefined ? oldOption.width : option.width;
        option.height = option.height == undefined ? oldOption.height : option.height;
        option.uploadTitle = option.uploadTitle == undefined ? oldOption.uploadTitle : option.uploadTitle;
        option.type = option.type == undefined ? oldOption.type : option.type;
        
        option.emptySrc = option.emptySrc == undefined ? oldOption.showSrcDefault : option.emptySrc;


        $('head').append("<style>.info-cover {  cursor: pointer;  position: relative;  }  .info-cover-span {  background-color: rgba(0, 0, 0, 0.3); height: 30px;line-height: 30px; position: absolute;  bottom: 0px;left: 0;  margin: 0 !important;  font-size: 14px !important;  text-align: center;  color: white !important;  cursor: pointer;  }  .hide{  display: none!important;  }</style>");
        $('body').prepend("<div id='" + option.objDiv.substr(1) + "-image-picker' class='hide'>选择文件并上传</div>");
        var html = "<div class='upload_image_div' style='width:" + option.width + ";height:" + option.height + ";position: relative;'>" +
            "<input type='hidden' name='" + option.inputFileName + "' value='" + option.inputFileDefault + "'>" +
            "<input type='hidden' name='" + option.inputCoverName + "' value='" + option.inputCoverDefault + "'>" +
            "<div class='upload_image'  style='width:" + option.width + ";height:" + option.height + "'>" +
            "<img src='" + option.showSrcDefault + "' class='info-cover' style='width:" + option.width + ";height:" + option.height + ";position: relative;'/>" +
            "<span class='info-cover-span info-cover-text' style='width: 100%' >" + option.uploadTitle + "</span>" +
            "<span class='info-cover-span info-cover-process hide' style='background: skyblue;'>0%</span></div></div>";

        $(option.objDiv).html(html);

        var crop = option.crop;

        var accept = option.accept;

        var uploader = {
        };
        uploader.isUploading = false;
        if (option.type == 'upload') {

            var uploader = WebUploader.create({
                // swf文件路径
                swf: option.swfPath,
                // 文件接收服务端。
                //后台接口
                server: option.server,
                // 选择文件的按钮。可选。
                // 内部根据当前运行是创建，可能是input元素，也可能是flash.
                pick: {
                    id: "#" + option.objDiv.substr(1) + "-image-picker",
                    //只能选择一个文件上传
                    multiple: false
                },
                auto: true,
                // 开起分片上传。
                chunked: true,
                chunkSize: 2 * 1024 * 1024,
                // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
                resize: false,
                // 禁掉全局的拖拽功能。这样不会出现图片拖进页面的时候，把图片打开。
                disableGlobalDnd: true,
                fileNumLimit: 1,
                fileSizeLimit: 400 * 1024 * 1024,
                // 550 M
                fileSingleSizeLimit: 128 * 1024 * 1024,
                // 55 M
                accept: accept,
                compress: {
                    width: 3600,
                    height: 2046,

                    // 图片质量，只有type为`image/jpeg`的时候才有效。
                    quality: 100,

                    // 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
                    allowMagnify: false,

                    // 是否允许裁剪。
                    crop: false,

                    // 是否保留头部meta信息。
                    preserveHeaders: true,

                    // 如果发现压缩后文件大小比原来还大，则使用原来图片
                    // 此属性可能会影响图片自动纠正功能
                    noCompressIfLarger: false,

                    // 单位字节，如果图片大小小于此值，不会采用压缩。
                    compressSize: 0
                }

            });
            // 当有文件被添加进队列的时候
            uploader.on('fileQueued', function (file) {
            });
            // 文件上传过程中创建进度条实时显示。
            uploader.on('uploadProgress', function (file, percentage) {
                //console.log("percentage = " + percentage * 100 + "%");
                uploader.isUploading = true;
                percentage = parseInt(percentage * 100)
                $(option.objDiv + ' .info-cover-process').css("width", percentage + "%");
                $(option.objDiv + ' .info-cover-process').text(percentage + "%");
            });

            uploader.on('uploadSuccess', function (file, response) {
                //console.log("success" + JSON.stringify(response));
                uploader.isUploading = false;
                $(option.objDiv + ' .info-cover-process').css("width", "100%");
                $(option.objDiv + ' .info-cover-process').text("0%");
                $(option.objDiv + ' .info-cover-process').addClass("hide");
                if (response.status == 0) {
                    option.showImage = response.data.show_path;
                    $(option.objDiv + ' .info-cover').attr('src', response.data.show_path);
                    $(option.objDiv + ' input[name="' + option.inputFileName + '"]').val(response.data.save_path);
                    $(option.objDiv + ' input[name="' + option.inputCoverName + '"]').val(response.data.cover);
                    $(option.objDiv + ' .info-cover-text').text("上传成功");
                } else {
                    alert_crop(response.msg);
                    $(option.objDiv + ' .info-cover-text').text("上传失败");
                }
            });


            uploader.on('uploadError', function (file) {
                uploader.isUploading = false;
                $(option.objDiv + ' .info-cover-text').text("上传失败");
                closeUploader(uploader);
            });

            uploader.on('uploadComplete', function (file) {
                closeUploader(uploader);
            });
            uploader.on('uploadAccept', function (file, response) {

            });


            if (crop != undefined) {
                //裁剪图片
                var cropHtml = "<div id='" + option.objDiv.substr(1) + "-image-crop' class='crop-image-div hide' style='z-index: 99999!important;'> " +
                    "<div class='crop-div'>" +
                    " <div class='crop-title-div' >" +
                    "<label class='title'>封面上传</label>  " +
                    "<a href='javascript: void(0)' class='crop-close' style='color: grey;font-size: 16px' >&times;</a>" +
                    "</div><div class='crop-content'>" +
                    "<div class='avatar-upload'>" +
                    "<input class='avatar-data' name='avatar_data' type='hidden'></br>" +
                    "<input class='avatar-input'  name='avatar_file' type='file'>" +
                    "<input class='avatar_ratio' name='avatar_ratio' type='hidden' value='" + crop.ratioWidth + "/" + crop.ratioHeight + "'>" +
                    "</div>" +
                    "<div style='padding: 10px;width:100%;text-align: center;display: block' >" +
                    "<div  class='container container-cover' style='width: 560px'>" +
                    "<img src='" + __root__ + "/Statics/onebit/default.png' class='crop_show_img' />" +
                    "</div>" +
                    "</div>" +
                    "<div class='row avatar-btns' style='text-align: center'>" +
                    "<button type='button' class='am-btn am-btn-success am-radius avatar-save'>确定并上传</button></div></div></div></div>";

                $('head').append("<style>.crop-image-div{position: fixed;left: 0;right: 0;bottom: 0;top: 0;background: rgba(146, 146, 146, 0.4);z-index: 99999!important;}.crop-image-div .crop-title-div{background-color:#fff;border-bottom:1px solid #eee;height: 35px;line-height: 35px;;position: relative;}.crop-image-div .crop-title-div a{position: absolute;right: 10px;color: grey;}.crop-image-div .crop-title-div .title{position: absolute;width: 100%;text-align: center;height: 35px;line-height: 35px;font-size: 16px;font-weight: 600;color:black;}.crop-image-div .crop-div{width: 600px;height: 460px;position: absolute;left: 50%;margin-left: -300px;top: 150px;background: white;}.crop-image-div .crop-content{height: auto;position: relative;padding: 10px;}.crop-content .container-cover{width: 100%;height: 300px;border: 1px solid lightgrey;background-color: #fcfcfc}</style>");

                $('body').prepend(cropHtml);
                //是否裁剪图片
                var options = {
                    aspectRatio: crop.ratioWidth / crop.ratioHeight,
                    strict: false,
                    preview: '.img-preview',
                    crop: function (e) {
                        $('input[name="avatar_data"]').val(JSON.stringify(e));
                    }
                };

                var $image = $("#" + option.objDiv.substr(1) + "-image-crop .crop_show_img");
                var console = window.console || {
                        log: function () {
                        }
                    };

                var URL = window.URL || window.webkitURL;
                var blobURL;
                $image.on({}).cropper(options);

                $("#" + option.objDiv.substr(1) + "-image-crop .avatar-input").change(function () {

                    var val = $(this).val();
                    var files = this.files;
                    var file;
                    if (!$image.data('cropper')) {
                        return;
                    }
                    if (files && files.length) {
                        file = files[0];
                        if (/^image\/\w+$/.test(file.type)) {
                            blobURL = URL.createObjectURL(file);
                            $image.one('built.cropper', function () {

                                // Revoke when load complete
                                URL.revokeObjectURL(blobURL);
                            }).cropper('reset').cropper('replace', blobURL);
                        } else {
                            window.alert('Please choose an image file.');
                        }
                    }
                })
                $("#" + option.objDiv.substr(1) + "-image-crop  .avatar-save").click(function () {
                    var val = $("#" + option.objDiv.substr(1) + "-image-crop  .avatar-input").val();
                    var files = $("#" + option.objDiv.substr(1) + "-image-crop  .avatar-input").get(0).files;
                    var dddd = $("#" + option.objDiv.substr(1) + "-image-crop  .avatar-data").val();
                    var avatar_ratio = $("#" + option.objDiv.substr(1) + "-image-crop  .avatar_ratio").val();
                    if (files && val != '') {
                        uploader.on('uploadBeforeSend', function (object, data) {
                            $(option.objDiv + ' .info-cover-text').text("");
                            $(option.objDiv + ' .info-cover-process').removeClass("hide");
                            data.avatar_data = dddd;
                            data.avatar_ratio = avatar_ratio;
                            $("#" + option.objDiv.substr(1) + "-image-crop").addClass('hide')
                            console.log("uploadBeforeSend" + JSON.stringify(data));
                        });
                        uploader.addFiles(files);
                    } else {
                        alert_crop("请先选择文件");
                    }

                })
                $("#" + option.objDiv.substr(1) + "-image-crop  .crop-close").click(function () {
                    $("#" + option.objDiv.substr(1) + "-image-crop").addClass('hide');
                })
                $(option.objDiv + ' .upload_image').click(function () {
                    if (crop.reset != undefined && crop.reset == true) {//需要充值图片
                        // $(option.objDiv + ' .crop_show_img').attr('src', option.showSrcDefault);
                        // $(option.objDiv + ' input[name="' + option.inputFileName + '"]').val('');
                        // $(option.objDiv + ' input[name="' + option.inputCoverName + '"]').val('');
                        // $("#" + option.objDiv.substr(1) + "-image-crop .avatar-input").val('');
                        // $("#" + option.objDiv.substr(1) + "-image-crop .cropper-canvas").html('');
                        // $("#" + option.objDiv.substr(1) + "-image-crop .cropper-crop-box").remove();
                    }

                    $("#" + option.objDiv.substr(1) + "-image-crop").removeClass('hide');
                })
            } else {
                uploader.on('uploadBeforeSend', function (object, data) {
                    uploader.isUploading = true;
                    $(option.objDiv + ' .info-cover-text').text("");
                    $(option.objDiv + ' .info-cover-process').removeClass("hide");
                    $("#" + option.objDiv.substr(1) + "-image-crop").addClass('hide')
                    //console.log("uploadBeforeSend" + JSON.stringify(data));
                });
                //点击图片触发
                $(option.objDiv + ' .upload_image').click(function () {
                    $('#' + option.objDiv.substr(1) + '-image-picker input[name="file"]').click();
                })
            }

            uploader.setCropShow = function (src) {
                $("#" + option.objDiv.substr(1) + "-image-crop  .crop_show_img").attr('src', src);
            };
        } else {
            $('head').append("<style>.info-cover-text{display:none}</style>");
            function show_image(path){
                $('.show-img-div .image-show .show-img').attr('src',path);
                $('.show-img-div').removeClass('hide');
            }
            $('.show-img-div .image-close').click(function(){
                $('.show-img-div').addClass('hide');
            })

            //点击图片触发
            $(option.objDiv + ' .upload_image img').click(function () {
                var image = option.showSrcDefault;
                try{
                    if(option.showImage != undefined)
                        image = option.showImage;
                }catch(e){
                }
                show_image(image)

            })

        }





        return uploader;
    }
};

//插入视频播放器
$('head').append("<style>.video-div{position:fixed;z-index:999999;top:0;bottom:0;left:0;right:0;background:rgba(0,0,0,0.31)}.video-div .video-js{position:absolute;left:50%;top:150px;margin-left:-340px}.video-div .video-close{color:white;position:absolute;top:150px;left:50%;margin-left:305px;font-size:20px;height:30px;width:30px;line-height:30px;text-align:center;border-radius:15px;cursor:pointer;background: rgba(0, 0, 0, 0.26)}.video-div .video-close:hover{color:#dedede;background:rgba(255,255,255,0.22)}</style>");
$('body').prepend('<div class="video-div hide"><video id="video-show" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="none" width="680" height="400" poster="http://video-js.zencoder.com/oceans-clip.png" data-setup="{}"><source src="http://vjs.zencdn.net/v/oceans.mp4" type="video/mp4" /></video><div class="video-close">&times;</div></div>');
try{
    videojs.options.flash.swf = __root__ + "/Statics/onebit/video-js/video-js.swf";
}catch(e){

}




var OneBitVideoUploader = {

    create: function (option) {
        var oldOption = {
            server: '',
            swfPath: '',
            objDiv: '',
            inputFileName: 'save_path',
            inputCoverName: 'cover',
            inputCoverDefault: '',
            inputFileDefault: '',
            showSrcDefault: '',
            showVideoDefault: '',
            width: '150px',
            height: '120px',
            type: 'upload',
            accept: {
                title: 'video',
                extensions: '*',
                mimeTypes: 'video/*'
            }

        }
        option.inputFileName = option.inputFileName == undefined ? oldOption.inputFileName : option.inputFileName;
        option.inputFileDefault = option.inputFileDefault == undefined ? oldOption.inputFileDefault : option.inputFileDefault;
        option.inputCoverName = option.inputCoverName == undefined ? oldOption.inputCoverName : option.inputCoverName;
        option.inputCoverDefault = option.inputCoverDefault == undefined ? oldOption.inputCoverDefault : option.inputCoverDefault;
        option.showSrcDefault = option.showSrcDefault == undefined ? oldOption.showSrcDefault : option.showSrcDefault;
        option.accept = option.accept == undefined ? oldOption.accept : option.accept;
        option.server = option.server == undefined ? oldOption.server : option.server;
        option.swfPath = option.swfPath == undefined ? oldOption.swfPath : option.swfPath;
        option.width = option.width == undefined ? oldOption.width : option.width;
        option.height = option.height == undefined ? oldOption.height : option.height;
        option.type = option.type == undefined ? oldOption.type : option.type;
        option.showVideoDefault = option.showVideoDefault == undefined ? oldOption.showVideoDefault : option.showVideoDefault;

        var root = '';
        try{
            __root__;
            root = __root__;
        }catch(e){

        }

        $('head').append("<style>.info-cover {  cursor: pointer;  position: absolute; top:0;left:0 }.info-video{position: absolute;left: 50%;margin-left: -25px;margin-top: -25px;top: 50%}  .info-cover-span {  background-color: rgba(0, 0, 0, 0.3);  position: absolute;  bottom: 0px;  margin: 0 !important;  font-size: 14px !important;  text-align: center;  color: white !important;  cursor: pointer;  }  .hide{  display: none!important;  }</style>");
        $('body').prepend("<div id='" + option.objDiv.substr(1) + "-video-picker' class='hide'>选择文件并上传</div>");
        var html = "<div class='upload_image_div' style='width:" + option.width + ";height:" + option.height + ";position: relative;'>" +
            "<input type='hidden' name='" + option.inputFileName + "' value='" + option.inputFileDefault + "'>" +
            "<input type='hidden' name='" + option.inputCoverName + "' value='" + option.inputCoverDefault + "'>" +
            "<div class='upload_image'  style='width:" + option.width + ";height:" + option.height + "'>" +
            "<img src='" + option.showSrcDefault + "' class='info-cover' style='width:" + option.width + ";height:" + option.height + "'/>" +
            "<img src='" + root + "/Statics/onebit/video-js/viedo-play.png' class='info-video' style='width:50px;height:50px'/>" +
            "<span class='info-cover-span info-cover-text' style='width: 100%' >点击上传视频</span>" +
            "<span class='info-cover-span info-cover-process hide' style='background: skyblue;'>0%</span></div></div>";

        $(option.objDiv).html(html);

        var crop = option.crop;

        var accept = option.accept;
        var uploader = {};

        if (option.type == 'upload') {
            uploader.isUploading = false;
            var uploader = WebUploader.create({
                // swf文件路径
                swf: option.swfPath,
                // 文件接收服务端。
                //后台接口
                server: option.server,
                // 选择文件的按钮。可选。
                // 内部根据当前运行是创建，可能是input元素，也可能是flash.
                pick: {
                    id: "#" + option.objDiv.substr(1) + "-video-picker",
                    //只能选择一个文件上传
                    multiple: false
                },
                auto: true,
                // 开起分片上传。
                chunked: true,
                chunkSize: 2 * 1024 * 1024,
                // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
                resize: false,
                // 禁掉全局的拖拽功能。这样不会出现图片拖进页面的时候，把图片打开。
                disableGlobalDnd: true,
                fileNumLimit: 1,
                fileSizeLimit: 400 * 1024 * 1024,
                // 550 M
                fileSingleSizeLimit: 128 * 1024 * 1024,
                // 55 M
                accept: accept,
                compress: {
                    width: 3600,
                    height: 2046,

                    // 图片质量，只有type为`image/jpeg`的时候才有效。
                    quality: 100,

                    // 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
                    allowMagnify: false,

                    // 是否允许裁剪。
                    crop: false,

                    // 是否保留头部meta信息。
                    preserveHeaders: true,

                    // 如果发现压缩后文件大小比原来还大，则使用原来图片
                    // 此属性可能会影响图片自动纠正功能
                    noCompressIfLarger: false,

                    // 单位字节，如果图片大小小于此值，不会采用压缩。
                    compressSize: 0
                }

            });
            // 当有文件被添加进队列的时候
            uploader.on('fileQueued', function (file) {
            });
            // 文件上传过程中创建进度条实时显示。
            uploader.on('uploadProgress', function (file, percentage) {
                //console.log("percentage = " + percentage * 100 + "%");
                uploader.isUploading = true;
                percentage = parseInt(percentage * 100)

                $(option.objDiv + ' .info-cover-process').css("width", percentage + "%");

                if (percentage >= 100) {
                    $(option.objDiv + ' .info-cover-process').text("视频转换中，请稍候");
                } else {
                    $(option.objDiv + ' .info-cover-process').text(percentage + "%");
                }
            });

            uploader.on('uploadSuccess', function (file, response) {
                //console.log("success" + JSON.stringify(response));
                uploader.isUploading = false;
                $(option.objDiv + ' .info-cover-process').css("width", "100%");
                $(option.objDiv + ' .info-cover-process').text("0%");
                $(option.objDiv + ' .info-cover-process').addClass("hide");
                if (response.status == 0) {
                    $(option.objDiv + ' .info-cover').attr('src', response.data.show_path);
                    $(option.objDiv + ' input[name="' + option.inputFileName + '"]').val(response.data.save_path);
                    $(option.objDiv + ' input[name="' + option.inputCoverName + '"]').val(response.data.cover);
                    $(option.objDiv + ' .info-cover-text').text("上传成功");
                } else {
                    alert_crop(response.msg);
                    $(option.objDiv + ' .info-cover-text').text("上传失败");
                }
            });


            uploader.on('uploadError', function (file) {
                uploader.isUploading = false;
                $(option.objDiv + ' .info-cover-text').text("上传失败");
                closeUploader(uploader);
            });

            uploader.on('uploadComplete', function (file) {
                closeUploader(uploader);
            });
            uploader.on('uploadAccept', function (file, response) {
            });


            uploader.on('uploadBeforeSend', function (object, data) {
                uploader.isUploading = true;
                $(option.objDiv + ' .info-cover-text').text("");
                $(option.objDiv + ' .info-cover-process').removeClass("hide");
                $("#" + option.objDiv.substr(1) + "-image-crop").addClass('hide')
                //console.log("uploadBeforeSend" + JSON.stringify(data));
            });
            //点击图片触发
            $(option.objDiv + ' .upload_image').click(function () {
                $('#' + option.objDiv.substr(1) + '-video-picker input[name="file"]').click();
            })
        } else {
            uploader.isUploading = false;
            $('head').append("<style>.info-cover-text{display:none}</style>");

            var isIncludeJs;
            try{
                isIncludeJs =  videojs; // 如果已定义则不抛错，返回 true
            }catch(e){
                // 不 return 结果为 undefined ，逻辑为 false ，所以这句可省略
                isIncludeJs =  false; // 未定义抛错返回 false 。
            }


            if (isIncludeJs == false) {
                //点击图片触发
                $(option.objDiv + ' .upload_image').click(function () {
                    alert_crop("请在本 js 前引入onebit/video-js/video.js和其css");
                })
            }else{

                var myPlayer = videojs('video-show');
            }

            $('.video-div .video-close').click(function () {
                $('.video-div').addClass('hide');
                myPlayer.pause();
            })

            function show_video(cover, src) {
                $('.video-div').removeClass('hide');
                myPlayer.poster(cover)
                myPlayer.src(src);
                myPlayer.play();
            }

            //点击图片触发
            $(option.objDiv + ' .upload_image img').click(function () {
                show_video(option.showSrcDefault, option.showVideoDefault)
            })

        }



        return uploader;
    }
};


function closeUploader(uploader) {
    // 移除所有缩略图并将上传文件移出上传序列
    for (var i = 0; i < uploader.getFiles().length; i++) {
        // 将图片从上传序列移除
        uploader.removeFile(uploader.getFiles()[i]);
    }
    // 重置uploader，目前只重置了文件队列
    uploader.reset();
    // 更新状态等，重新计算文件总个数和总大小
}


//点击文件触发
$('.upload_file').click(function () {
    $('#picker input[name="file"]').click();
})