(function (){  
	//注册命名空间 'xyt' 到window对象上    
	window['xyt'] = window['xyt']==undefined?{}:window['xyt'];   
	
	
	// 隐藏系统不支持的无效的UI
	xyt.hideInvalidUi = function (){
		if(mui.os.android) {
			var list = document.querySelectorAll('.ios-only');
			if(list) {
				for(var i = 0; i < list.length; i++) {
					list[i].style.display = 'none';
				}
			}
		}else if(mui.os.ios) {
			var list = document.querySelectorAll('.android-only');
			if(list) {
				for(var i = 0; i < list.length; i++) {
					list[i].style.display = 'none';
				}
			}
		}else{
			var list = document.querySelectorAll('.app-only,.android-only,.ios-only');
			if(list) {
				for(var i = 0; i < list.length; i++) {
					list[i].style.display = 'none';
				}
			}
		}
	}
	
	// 获得列表项模板html
	xyt.getListItemHtml = function (id){
		var htmlString = '';
		if(id){
			htmlString = mui('#'+id)[0].innerHTML;
		}else{
			htmlString = mui('#list-item-template')[0].innerHTML;
		}
		return htmlString.split("##");
	}

})();

Date.prototype.Format = function (fmt) { //author: meizz
    var o = {
        "M+": this.getMonth() + 1, //月份
        "d+": this.getDate(), //日
        "h+": this.getHours(), //小时
        "m+": this.getMinutes(), //分
        "s+": this.getSeconds(), //秒
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度
        "S": this.getMilliseconds() //毫秒
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}