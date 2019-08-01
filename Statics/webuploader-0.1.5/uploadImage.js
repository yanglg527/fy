/**
 * Created by Administrator on 2017-01-12.
 */
function createImageUploader(swfUrl, serverUrl, pick, startCallBack, processCallBack, finishCallBack, errorCallBack){
    var obj = $(pick);
    // 初始化Web Uploader
    var uploader = WebUploader.create({
        // 选完文件后，是否自动上传。
        auto: true,
        // swf文件路径
        swf: swfUrl,
        // 文件接收服务端。
        server: serverUrl,
        // 选择文件的按钮。可选。
        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
        pick: pick,
        // 只允许选择图片文件。
        accept: {
            title: 'Images',
            extensions: 'gif,jpg,jpeg,bmp,png',
            mimeTypes: 'image/*'
        }
    });

//        // 当有文件添加进来的时候
        uploader.on( 'fileQueued', function( file ) {
            startCallBack(obj,file);
        });

    // 文件上传过程中创建进度条实时显示。
    uploader.on( 'uploadProgress', function( file, percentage ) {
        processCallBack(obj, file, percentage)

    });

    // 文件上传成功，给item添加成功class, 用样式标记上传成功。
    uploader.on( 'uploadSuccess', function( file, data) {
        finishCallBack(obj,file, data);
    });

    // 文件上传失败，显示上传出错。
    uploader.on( 'uploadError', function( file ) {
        errorCallBack(obj,file);
    });
    return uploader;

}
