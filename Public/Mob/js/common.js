//点赞之星、创新之星
function like_href(_this, text) {
	sessionStorage.setItem("like_star", text);
	$(_this).attr("href", '../ledger_list/ledger.html');
}
//所有台账列表跳转
function ledger_href(_this, text) {
	sessionStorage.setItem("all_ledger", text);
	$(_this).attr("href", '../ledger_list/ledger_list.html');
}
//领导表率空间跳转留言板
function change_bg(_this, src, href) {
	$(_this).children(".black-img").children("img").attr("src", src);
	setTimeout(function() {
		window.location.href = href;
	}, 200);
}
//四个指标数组
var branch = ['凝聚•支部自觉落实“三会一课”组织生活......', '凝聚支部党员干事创业、默默奉献的工作活力', '凝聚支部攻坚克难、闯关夺隘、推进党的......', '凝聚支部打造党建品牌、挖掘工作亮点、塑......'];
var ground = ['坚持党的领导，落实党的理论和路线方针政策', '坚持全面从严治党，落实三个六对照法则', '坚持民主集中制，确保党的活力与团结', '发挥领导核心作用，推进党业融合'];
var member = ['带头践行党员宗旨，主动担当，争创佳绩', '带头学习提高，坚定理想信念，严肃参加组......', '带头服务群众，服务社会，弘扬正气', '带头遵纪守法，坚守廉洁自律'];
var leader = ['模范执行党的路线方针政策和上级各项决议', '模范践行“两学一做”要求，做“四讲四有”党员', '模范遵守党的纪律和规矩，做链接从税表率', '模范履行岗位职责，提升工作质效'];
//四个指标和台账列表关联函数
function homepage_ind(_this, arr) {
	sessionStorage.setItem("one", arr[0]);
	sessionStorage.setItem("two", arr[1]);
	sessionStorage.setItem("three", arr[2]);
	sessionStorage.setItem("four", arr[3]);
	$(_this).attr("href", '../ledger_list/ledger_list_filtrate.html');
}

function get_head_url(url) {
	if(url == '' || url == null || url == undefined) {
		return __root__ + '/Public/Common/img/common-head.png';
	} else if(url.indexOf('http:') > -1 || url.indexOf('https:') > -1) {
		return url;
	} else if(url.indexOf('/Uploads') > -1) {
		return　 __root__ + '' + 　url;
	} else {
		return __root__ + '/Uploads/' + 　url;
	}
}

function get_content(content, count) {
				if(content == undefined || content == null) {
					return "";
				}
				if(count == undefined) {
					count = 20;
				}
				var c = delStyle(content);
//				c = delHtmlTag(c);
				var lenght = c.length;
				if(lenght >= count) {
					return c.substring(0, count) + "...";
				} else {
					return c;
				}
			}

			function delHtmlTag(str) {
				str = str.replace(/\s+/g, "");
				return str.replace(/<[^>]+>/g, ""); //去掉所有的html标记
			}

			function delStyle(str) {
				return str.replace(/<style(([\s\S])*?)<\/style>/g, ""); //去掉所有的html标记
			}
function get_img_url(url) {
	if(url == '' || url == null || url == undefined) {
		return __root__ + '/Public/Common/img/common.png';
	} else if(url.indexOf('http:') > -1 || url.indexOf('https:') > -1) {
		return url;
	} else if(url.indexOf('/Uploads') > -1) {
		return　 __root__ + '' + 　url;
	} else {
		return __root__ + '/Uploads/' + 　url;
	}
}

function show_name(name) {
	if(name == null || name == undefined) {
		return '';
	} else {
		return name;
	}
}

function show_count(count) {
	if(count == null || count == undefined) {
		return 0;
	} else {
		return count;
	}
}

function hasClass(obj, cls) {
        return obj.className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'));
    }
    function addClass(obj,cls){
    	if (!hasClass(obj, cls)) {
            obj.className += (" " + cls);
        }
    }
     function removeClass(obj,cls){
    	if (hasClass(obj, cls)) {
            var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
            obj.className = obj.className.replace(reg, ' ');
        }
    }