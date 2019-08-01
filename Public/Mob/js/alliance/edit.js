$("head>title").html(sessionStorage.getItem("edie_title"));
//var editer=$('#edit');
////给上传的图片生成一个地址
//function getObjectURL(file) {
//  var url = null ;
//  if (window.createObjectURL!=undefined) { // basic
//      url = window.createObjectURL(file) ;
//  } else if (window.URL!=undefined) { // mozilla(firefox)
//      url = window.URL.createObjectURL(file) ;
//  } else if (window.webkitURL!=undefined) { // webkit or chrome
//      url = window.webkitURL.createObjectURL(file) ;
//  }
//  return url ;
//}
////在页面生成一个张图片
//function InsertImage(_this){
//  var filepath =getObjectURL(_this.files[0]);
//
//  editer.focus(filepath);
//  document.execCommand('InsertImage', false, filepath);
//}