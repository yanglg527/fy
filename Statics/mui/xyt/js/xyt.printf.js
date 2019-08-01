(function (){  
	//注册命名空间 'xyt' 到window对象上    
	window['xyt'] = window['xyt']==undefined?{}:window['xyt'];   
	  
	
	// 开关常量定义
	var IS_DEBUG = true;
	
	/**
	 * 在控制台输出打印调试信息
	 * @param {Object} info
	 */
	xyt.debugOut = function(info){
		if(IS_DEBUG){
			console.log(info);
		}
	}
	
	
	xyt.debugOutJson = function(jsonObject){
		if(IS_DEBUG){
			console.log("jsonObject = " + JSON.stringify(jsonObject));
		}
	}
	
})();