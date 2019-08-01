/**
 * Created by jervis on 2017/5/10.
 */
function inputImgToBase64(filefield, callBack){

    if(window.File && window.FileReader && window.FileList && window.Blob){
        // var filefield = document.getElementById(fileId),
            file = filefield.files[0];

        processfile(file,callBack);
    }else{
        alert("Upload picture is not fully supported in this browser");
    }

}
function processfile(file,callBack) {
    var reader = new FileReader();
    reader.onload = function (event) {
        var blob = new Blob([event.target.result]);

        window.URL = window.URL || window.webkitURL;

        var blobURL = window.URL.createObjectURL(blob);

        var image = new Image();
        image.src = blobURL;
        image.onload = function() {
            var resized = resizeMe(image);
             
            callBack(resized);
        }
    };
    reader.readAsArrayBuffer(file);
}

function resizeMe(img) {
    //压缩的大小
    var max_width =1024;
    var max_height =2048;

    var canvas = document.createElement('canvas');
    var width = img.width;
    var height = img.height;

    if(width > height) {
        if(width > max_width) {
            height = Math.round(height *= max_width / width);
            width = max_width;
        }
    }
    else{
        if(height > max_height) {
            width = Math.round(width *= max_height / height);
            height = max_height;
        }
    }

    canvas.width = width;
    canvas.height = height;

    var ctx = canvas.getContext("2d");
    ctx.drawImage(img, 0, 0, width, height);
    //压缩率
    return canvas.toDataURL("image/jpeg",0.7);
}