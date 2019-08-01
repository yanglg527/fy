/**
 * 使用须知：使用此库需同时引入mui.min.js文件
 * 
 * 此函数库实现对mui库的俄日封装
 * 
 */

(function (){
	
	//注册命名空间 'xyt' 到window对象上    
	window['xyt'] = window['xyt']==undefined?{}:window['xyt'];   
	
	/**
	 * 封装mui的可拖拉列表
	 */
	
	/**
	 * 初始化可拖拉列表
	 * @param {Object} option：初始化参数，json格式
	 * option.disableDown:禁止下拉
	 * option.disableUp:禁止上拉
	 * option.pullDownCallback：下拉回调函数
	 * option.pullUpCallback：上拉回调函数
	 */
	xyt.initPullList = function (option){
		if(option.disableDown){
			mui.init({
				pullRefresh: {
					container: '#pullrefresh',
					up : {
				     	height:50,//可选.默认50.触发上拉加载拖动距离
				      	auto:false,//可选,默认false.自动上拉加载一次
				      	contentrefresh : "加载中...",//可选，正在加载状态时，上拉加载控件上显示的标题内容
				      	contentnomore:'喔噢，没数据了',//可选，请求完毕若没有更多数据时显示的提醒内容；
				      	callback :option.pullUpCallback //必选，刷新函数，根据具体业务来编写，比如通过ajax从服务器获取新数据；
				    }
				}
			});
		}else if(option.disableUp){
			mui.init({
				pullRefresh: {
					container: '#pullrefresh',
					down : {
				    	height:50,//可选,默认50.触发下拉刷新拖动距离,
				    	auto: false,//可选,默认false.自动下拉刷新一次
				    	contentdown : "再拉我就刷新了",//可选，在下拉可刷新状态时，下拉刷新控件上显示的标题内容
				    	contentover : "释放立即刷新",//可选，在释放可刷新状态时，下拉刷新控件上显示的标题内容
				    	contentrefresh : "正在刷新...",//可选，正在刷新状态时，下拉刷新控件上显示的标题内容
				     	callback :option.pullDownCallback //必选，刷新函数，根据具体业务来编写，比如通过ajax从服务器获取新数据；
			    	},
				}
			});
		}else{
			mui.init({
				pullRefresh: {
					container: '#pullrefresh',
					down : {
				    	height:50,//可选,默认50.触发下拉刷新拖动距离,
				    	auto: false,//可选,默认false.自动下拉刷新一次
				    	contentdown : "再拉我就刷新了",//可选，在下拉可刷新状态时，下拉刷新控件上显示的标题内容
				    	contentover : "释放立即刷新",//可选，在释放可刷新状态时，下拉刷新控件上显示的标题内容
				    	contentrefresh : "正在刷新...",//可选，正在刷新状态时，下拉刷新控件上显示的标题内容
				     	callback :option.pullDownCallback //必选，刷新函数，根据具体业务来编写，比如通过ajax从服务器获取新数据；
			    	},
					up : {
				     	height:50,//可选.默认50.触发上拉加载拖动距离
				      	auto:false,//可选,默认false.自动上拉加载一次
				      	contentrefresh : "加载中...",//可选，正在加载状态时，上拉加载控件上显示的标题内容
				      	contentnomore:'喔噢，没数据了',//可选，请求完毕若没有更多数据时显示的提醒内容；
				      	callback :option.pullUpCallback //必选，刷新函数，根据具体业务来编写，比如通过ajax从服务器获取新数据；
				    }
				}
			});
		}
	}

    xyt.LIST_SIZE = 10; // 每次请求返回的列表项数量
	/**
	 * 结束下拉刷新
	 * @param {Object} dataLength：当次返回的数据长度
	 */
	xyt.endPullDown = function(dataLength){
		mui('#pullrefresh').pullRefresh().endPulldownToRefresh();
		mui('#pullrefresh').pullRefresh().refresh(true);  // 恢复上拉加载功能(上拉加载功能在上拉加载过程中可能关闭)
	}
	/**
	 * 结束上拉加载
	 * @param {Object} dataLength：当次返回的数据长度
	 */
	xyt.endPullUp = function(dataLength){
		if(dataLength <  xyt.LIST_SIZE){ // 返回的数量小于规定的返回数量，表示后面没有更多数据了
			mui('#pullrefresh').pullRefresh().endPullupToRefresh(true);
		}else{
			mui('#pullrefresh').pullRefresh().endPullupToRefresh(false);
		}
	}
	xyt.endUpdate = function(dataLength, isLoadMore){
		if(isLoadMore){ // 结束上拉加载
			if(dataLength <  xyt.LIST_SIZE){ // 返回的数量小于约定的返回数量，表示后面没有更多数据了
				mui('#pullrefresh').pullRefresh().endPullupToRefresh(true);
			}else{
				mui('#pullrefresh').pullRefresh().endPullupToRefresh(false);
			}
		}else{ // 结束下拉刷新
			setTimeout(function(){
				mui('#pullrefresh').pullRefresh().endPulldownToRefresh(false);
				mui('#pullrefresh').pullRefresh().enablePullupToRefresh();
			}, 500);
		}
	}
	
	
	
	/**
	 * 封装mui打开新窗口函数 和 网页版打开新窗口
	 * @param {Object} pageUrl
	 * @param {Object} pageId
	 * @param {Object} extras 携带的参数json格式
	 */
	xyt.openWindow = function (pageUrl, pageId, extras){
		var url = pageUrl;
		var aniShow = "pop-in";
		if(mui.os.plus){
			if(mui.os.android) {
				if(parseFloat(mui.os.version) < 4.4) {
					aniShow = "slide-in-right";
				}
			}
		}else{
			if(extras){
				url += "?";
				var manyArgFlag = false;
				mui.each(extras,function(key, value){
					if(manyArgFlag){
						url += "&";
					}
					url += key + "=" + value;
					manyArgFlag = true;
				}) 
			}
		}
		mui.openWindow({
			url: url,
			id: pageId,
			extras: extras,
			show: {
				aniShow: aniShow
			},
			waiting: {
				autoShow: false
			}
		});
	}
	
	/**
	 * 通过key获得页面参数
	 * @param {Object} key
	 */
	xyt.getPageParam = function (key) {
	    if(mui.os.plus){
	    	var wv = plus.webview.currentWebview();
            return wv[name];
	    }else{
	    	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
		    var r = window.location.search.substr(1).match(reg);
		    if (r!= null) {
		       return unescape(r[2]);
		    }else{
		       return null;
		    }
	    }
	}
	
	
	
	
	/**
	 * 封装mui的ajax功能
	 */
	var BASE_URL = __root__ + "/";
	var TIME_OUT = 10000; // 访问超时时间10秒
	var SUCCESS_CODE = 0; // 成功访问编码
	var TOKEN_ERROR = 3;  // token错误编码
	  
	/** 以get方式向服务器请求Json数据
	 * 
	 * option{
	 *     api:接口名称(String:api),
	 * 	   isLoadMore:是否是列表加载更多(Boolean:回传给回调函数，帮助回调函数做逻辑处理)，
	 *     data:请求参数(json格式)，
	 * 	   successCallBack:成功回调函数
	 *     failCallBack:失败回调函数
	 * }
	 */
	xyt.ajaxGetJsonData = function (option){
		var httpUrl = BASE_URL + option.api;
		xyt.debugOut("httpUrl = " + httpUrl);
		xyt.debugOutJson(option.data);
		mui.ajax(httpUrl,{
			data:option.data,
			dataType:'json', // 服务器返回json格式数据
			type:option.type==undefined?'post':option.type, // HTTP请求类型
			timeout:TIME_OUT,// 超时时间设置为10秒；
			success:function(data){
				xyt.debugOutJson(data);
	            if(data.status == SUCCESS_CODE){
	            	option.successCallBack(data.data, option.isLoadMore,data.msg);
	            }else{
	            	mui.alert(data.msg);
                    option.failCallBack(data.status, data.msg);
	            }
	        },
			error:function(xhr,type,errorThrown){
				//异常处理；
				mui.alert("服务器暂时无法访问，请稍候重试");
                option.failCallBack(-100, "服务器暂时无法访问，请稍候重试");
			}
		});
	}
	
	/** 以post方式向服务器请求Json数据
	 * 
	 * option{
	 *     api:接口名称(String:api),
	 * 	   isLoadMore:是否是列表加载更多(Boolean:回传给回调函数，帮助回调函数做逻辑处理)，
	 *     data:请求参数(json格式)，
	 * 	   successCallBack:成功回调函数
	 *     failCallBack:失败回调函数
	 * }
	 */
	xyt.ajaxPostJsonData = function (option){
		var httpUrl = BASE_URL + option.api;
		option.data.token = xyt.getData("token");
		xyt.debugOut("httpUrl = " + httpUrl);
		xyt.debugOutJson(option.data);
		mui.ajax(httpUrl,{
			data:option.data,
			dataType:'json',//服务器返回json格式数据
			type:'post',//HTTP请求类型
			timeout:TIME_OUT,//超时时间设置为10秒；
			success:function(data){
	            if(data.resCode == SUCCESS_CODE){
	            	option.successCallBack(data.responseData, option.isLoadMore);
	            }else if(data.resCode == TOKEN_ERROR){
	            	// 此处添加token过期，重新登录逻辑
	            	mui.alert(data.resMsg, "提示", function(){
	            		if (option.api != "getLogin"){
	            			setTimeout(function() {
								var ws=plus.webview.currentWebview();
								plus.webview.close(ws);
							}, 200); 
							mui.openWindow({
								url: "../../../html/login/login.html",
								id: "login",
								waiting: {
									autoShow: false, //自动显示等待框，默认为true
									aniShow:"pop-in",
									duration:300,
								},
							});
						}
	            	});
	            }else{
	            	option.failCallBack(data);
	            }
	        },
			error:function(xhr,type,errorThrown){
				//异常处理；
				mui.alert("网络协议异常");
			}
		});
	}
	
	
	
	
	
	
	
	
	/**
	 * 封装mui的存储功能
	 * 
	 * mui.plus.storage优点：空间大小不限制，设备有多大就多大；缺点，效率差，频繁操作设备会发热
 	 * web浏览器的localstorage：优点效率高，缺点：空间大小受限(一般<=5M)
     * 综上所述，本地存储方案折中采取了plus.storage与localStorage混合的方案：
     * 当localStorage达到存储瓶颈时切换到plus.storage
	 */
	xyt.test = function (){
		console.log("tttttttttttttttttt");
	}
	// 判断是否首次启动APP(基于保存在存储空间中的标志位实现)
	xyt.isFirstLauch = function (){
		var firstLauchFlag = xyt.getData("firstLauchFlag");
		if(firstLauchFlag){
			return false;
		}
		return true;
	}
	
	// 设置已经不是初次启动
	xyt.setNotFirstLauch = function (){
		xyt.setData("firstLauchFlag","true");
	}
	
	// 判断是否自动登录
	xyt.isAutoLogin = function (){
		var autoLoginFlag = xyt.getData("autoLoginFlag");
		if(autoLoginFlag){
			return true;
		}
		return false;
	}
	
	
	// 从存储空间获取对应键的值
	xyt.getData = function (k) {
		if(mui.os.plus){
            return _getDataLocalStorage(k) || _getDataMuiStorage(k);
		}else{
            return _getDataLocalStorage(k);
		}

	};
	
	// 保存数据到存储空间
	xyt.setData = function (k, value) {
		value = JSON.stringify({
			data: value
		});
		k = k.toString();
		try {
			window.localStorage.setItem(k, value);
		} catch (e) {
			console.log(e);
			//超出localstorage容量限制则存到plus.storage中
			//且删除localStorage重复的数据
			removeItem(k);
			plus.storage.setItem(k, value);
		}
	};
	
	// 删除存储空间中对应键的数据
	xyt.removeData = function (k) {
		_removeDataLocalStorage(k)
		return _removeDataMuiStorage(k);
	};
	
	// 清空存储空间中的数据
	xyt.clearData = function () {
		_clearDataLocalStorage();
		return _clearDataMuiStorage();
	};
	
	// 从localStorage读取对应建的值
	function _getDataLocalStorage(k) {
		var jsonStr = window.localStorage.getItem(k.toString());
		return jsonStr ? JSON.parse(jsonStr).data : null;
	};
	
	// 从本地存储空间读取对应建的值
	function _getDataMuiStorage(k) {
		var jsonStr = plus.storage.getItem(k.toString());
		return jsonStr ? JSON.parse(jsonStr).data : null;
	};
	
	// 删除localStorage中对应键的数据
	function _removeDataLocalStorage(k) {
		return window.localStorage.removeItem(k);
	};

	// 删除本地存储空间中对应键的数据
	function _removeDataMuiStorage(k) {
		return plus.storage.removeItem(k);
	};
	
	// 清空localStorage中的数据
	function _clearDataLocalStorage() {
		window.localStorage.clear();
	};

	// 清空本地存储空间中的数据
	function _clearDataMuiStorage() {
		return plus.storage.clear();
	};
	
	

})();
