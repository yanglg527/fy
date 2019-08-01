"use strict";

// 配置请求基础路径
window.baseURL = "http://fy.dev.com/api";

// 根据文件后缀计算使用的类名
function typeToClass(path) {
    var suffix = path.split(".").pop();
    // 'jpg', 'gif', 'png', 'jpeg', 'doc', 'pdf', 'xlsx', 'xls', 'docx'
    switch (suffix) {
        case "jpg":
        case "jpeg":
            return "img jpg";
        case "gif":
            return "img gif";
        case "png":
            return "img png";

        case "doc":
        case "docx":
            return "img word";

        case "ppt":
        case "pptx":
            return "img ppt";

        case "xls":
        case "xlsx":
            return "img excel";

        case "pdf":
            return "img pdf";

        default:
            return "img";
    }
}

// 获取url中的query
function getQuery() {
    var search = window.location.search;
    if (!search) {
        return;
    }
    var queryObj = {};
    var queryArr = search.slice(1).split("&");
    queryArr.forEach(function (v) {
        var obj = v.split("=");
        queryObj[obj[0]] = obj[1];
    });
    return queryObj;
}

/**
 * @param {Object|Number} date 日期对象|时间戳
 * @param {String} formate 日期格式 'yyy'-年份,'mmm'-月份,'ddd'-日期,'www'-星期,'hh'-小时,'mm'-分钟,'ss'-秒
 */

function dateFormate(date) {
    var formate = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : "yyy-mmm-ddd 星期www hh:mm:ss";

    var objDate = null;
    if (date instanceof Date) {
        objDate = date;
    } else if (typeof date === "number") {
        objDate = new Date(date);
    } else {
        objDate = new Date();
    }

    var d = {
        yyy: objDate.getFullYear(),
        mmm: objDate.getMonth() + 1,
        ddd: objDate.getDate(),
        www: ["日", "一", "二", "三", "四", "五", "六"][objDate.getDay()],
        hh: objDate.getHours(),
        mm: objDate.getMinutes(),
        ss: objDate.getSeconds()
    };
    var zero = function zero(num) {
        return num > 9 ? num : "0" + num;
    };
    var formateStr = formate;
    for (var key in d) {
        if (d.hasOwnProperty(key)) {
            var reg = new RegExp("" + key);
            formateStr = formateStr.replace(reg, key === "www" ? d[key] : zero(d[key]));
        }
    }
    return formateStr;
}

/**
 * @name obj2Form
 */
function obj2Form(obj) {
    var form = new FormData();
    for (var key in obj) {
        form.append(key, obj[key]);
    }
    return form;
}