var editer=$('#edit');
//给上传的图片生成一个地址
function getObjectURL(file) {
    var url = null ;
    if (window.createObjectURL!=undefined) { // basic
        url = window.createObjectURL(file) ;
    } else if (window.URL!=undefined) { // mozilla(firefox)
        url = window.URL.createObjectURL(file) ;
    } else if (window.webkitURL!=undefined) { // webkit or chrome
        url = window.webkitURL.createObjectURL(file) ;
    }
    return url ;
}
//在页面生成一个张图片
function InsertImage(_this,id){

    inputImgToBase64(_this,function (base64) {
        // var filepath =getObjectURL(_this.files[0]);
        // editer.focus(filepath);
        isFocus(id)
       
   
        document.execCommand('InsertImage', false, base64);
    })
    // var filepath =getObjectURL(_this.files[0]);
    // console.log(filepath);
    // editer.focus(filepath);
    // document.execCommand('InsertImage', false, filepath);
}

function isFocus(id,img){
    if(id != undefined && id != null) {
        if (document.activeElement.id == 'id') {
            alert(document.activeElement.id)
        }
        else {
           
            document.getElementById(id).focus();
        }
    }
}

function htmlInsertImage(_this,id) {

    inputImgToBase64(_this, function (base64) {
        // var filepath =getObjectURL(_this.files[0]);
        // editer.focus(filepath);
        htmlisFocus(id)
    
        $('#'.id).append('<img src="'+base64+'"/>');
        //document.execCommand('htmlInsertImage', false, base64);
    })
}
function htmlisFocus(id){
    if(id != undefined && id != null) {
        if (document.activeElement.id == id) {

        }
        else {

            document.getElementById(id).focus();
        }
    }
}