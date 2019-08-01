function CropPlugin(option) {
    var myCrop = {};
    var crop = {};
    require.config({
        baseUrl: __root__ + '/Statics/photoCrop/js/',
        urlArgs: "bust=" + new Date
    })
    var croping = false;
    var Orientation = null;

    require(["jquery", 'hammer', 'tomPlugin', "tomLib", 'hammer.fake', 'hammer.showtouch'], function ($, hammer, plugin, T) {
        document.addEventListener("touchmove", function (e) {
            e.preventDefault();
        })
        var width = window.innerWidth

        //初始化图片大小300*300
        var opts = {
                cropWidth: width * 0.8,
                cropHeight: width * 0.8
            },
            $file = $('#file'),
            previewStyle = {
                x: 0,
                y: 0,
                scale: 1,
                rotate: 0,
                ratio: 1
            },
            transform = T.prefixStyle("transform"),
            $previewResult = $("#previewResult"),
            $previewBox = $(".preview-box"),
            $getFile = $("#getFile"),
            $preview = $("#preview"),
            $uploadPage = $("#uploadPage"),
            $mask = $(".upload-mask"),
            $loading = $(".upload-loading"),
            maskCtx = $mask[0].getContext("2d"),
            $imgCropPreview = $('#img-crop-preview'),
            $needCropImg = $("#needCropImg");


        //这是插件调用主体
        myCrop = T.cropImage({
            bindFile: $file, //绑定Input file
            enableRatio: false, //是否启用高清,高清得到的图片会比较大
            canvas: $(".photo-canvas")[0], //放一个canvas对象
            cropWidth: opts.cropWidth, //剪切大小
            cropHeight: opts.cropHeight,
            bindPreview: $preview, //绑定一个预览的img标签
            useHammer: true, //是否使用hammer手势，否的话将不支持缩放
            oninit: function () {

            },
            onChange: function () {
                $loading.show();
                resetUserOpts();
            },
            onLoad: function (data) {
                //用户每次选择图片后执行回调
                previewStyle.ratio = data.ratio;
                $preview.attr("src", data.originSrc).css({
                    width: data.width,
                    height: data.height
                }).css(transform, 'scale(' + 1 / previewStyle.ratio + ')');
                myCrop.setCropStyle(previewStyle)
                $loading.hide();
            }
        });

        function resetUserOpts() {
            $(".photo-canvas").hammer('reset');
            previewStyle = {
                scale: 1,
                x: 0,
                y: 0,
                rotate: 0
            };
            //			$imgCropPreview.hide();
            $preview.attr("src", '')
        }

        $(".photo-canvas").hammer({
            gestureCb: function (o) {
                //每次缩放拖拽的回调
                $.extend(previewStyle, o);
                //                console.log("用户修改图片",previewStyle)
                $preview.css(transform, "translate3d(" + previewStyle.x + 'px,' + previewStyle.y + "px,0) rotate(" + previewStyle.rotate + "deg) scale(" + (previewStyle.scale / previewStyle.ratio) + ")")
            }
        })
        //选择图片

        //获取图片并关闭弹窗返回到表单界面
        $getFile.on("click", function () {
            var cropInfo;
            $uploadPage.hide();
            myCrop.setCropStyle(previewStyle)
            //自定义getCropFile({type:"png",background:"red",lowDpi:true})
            cropInfo = myCrop.getCropFile({});
            $previewResult.attr("src", cropInfo.src).show();
            $imgCropPreview.show();

            console.log('拿到Base64了,传给后台吧？') //src.substr(22)
            if (option.onResult != undefined) {
                option.onResult({
                    base64: cropInfo.src,
                    path: ""
                });
            }

        })

        //上传文件按钮&&关闭弹窗按钮
        $(document).delegate("#file", "change", function () {
            croping = false;
            showCropModal();
        }).delegate("#closeCrop", "click", function () {
            croping = false;
            $uploadPage.hide();
            $imgCropPreview.show();
        })
        //		$previewResult.on('click', showCropModal)

        function showCropModal() {
            croping = true;
            $uploadPage.show();
            //			$imgCropPreview.hide();
            croping = true;
            setTimeout(function () {
                $uploadPage.show();
                $mask.prop({
                    width: $mask.width(),
                    height: $mask.height()
                })
                maskCtx.fillStyle = "rgba(0,0,0,0.5)";
                maskCtx.fillRect(0, 0, $mask.width(), $mask.height());
                maskCtx.strokeStyle = 'white';
                maskCtx.lineWidth = '2'
                maskCtx.clearRect(($mask.width() - opts.cropWidth) / 2, ($mask.height() - opts.cropHeight) / 2, opts.cropWidth, opts.cropHeight)
                maskCtx.strokeRect(($mask.width() - opts.cropWidth) / 2 - 1, ($mask.height() - opts.cropHeight) / 2 - 1, opts.cropWidth + 2, opts.cropHeight + 2); //Add a subpath with four points
            })

        }

        crop.start = function start() {
            $('#file').val('')
            $('#file').click();
        }

    })

    function start() {
        crop.start();
    }

    return {
        start: start
    };
}