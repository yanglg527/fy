// 配置请求基础路径
window.baseURL = "http://dj.zhzy.net.cn/api"
// 是本地开发接口地址
// window.baseURL = "http://fy.dev.com/api"

// 根据文件后缀计算使用的类名
function typeToClass(path) {
    const suffix = path.split(".").pop()
    // 'jpg', 'gif', 'png', 'jpeg', 'doc', 'pdf', 'xlsx', 'xls', 'docx'
    switch (suffix) {
        case "jpg":
        case "jpeg":
            return "img jpg"
        case "gif":
            return "img gif"
        case "png":
            return "img png"

        case "doc":
        case "docx":
            return "img word"

        case "ppt":
        case "pptx":
            return "img ppt"

        case "xls":
        case "xlsx":
            return "img excel"

        case "pdf":
            return "img pdf"

        default:
            return "img"
    }
}

// 获取url中的query
function getQuery() {
    const search = window.location.search
    if (!search) {
        return
    }
    const queryObj = {}
    const queryArr = search.slice(1).split("&")
    queryArr.forEach(v => {
        const obj = v.split("=")
        queryObj[obj[0]] = obj[1]
    })
    return queryObj
}

/**
 * @param {Object|Number} date 日期对象|时间戳
 * @param {String} fomate 日期格式 yyy:年, mmm:月, ddd:日, www:星期, hh:时, mm:分, ss:秒
 */

function dateFormate(date, formate = "yyy-mmm-ddd 星期www hh:mm:ss") {
    if (!date) {
        throw new Error('[dateFormate]: The first param "date" is required')
    } else if (!(date instanceof Date || typeof date === "number")) {
        throw new Error('[dateFormate]: The first param "date" should be a "object Date()" or "timeStamp"')
    }

    const objDate = date instanceof Date ? date : new Date(date)
    const infoDate = {
        yyy: objDate.getFullYear(),
        mmm: objDate.getMonth() + 1,
        ddd: objDate.getDate(),
        www: ["日", "一", "二", "三", "四", "五", "六"][objDate.getDay()],
        hh: objDate.getHours(),
        mm: objDate.getMinutes(),
        ss: objDate.getSeconds()
    }
    const zero = num => (num > 9 ? num : `0${num}`)
    let formateStr = formate
    for (const key in infoDate) {
        if (typeof infoDate[key] !== "undefined") {
            const reg = new RegExp(`${key}`)
            formateStr = formateStr.replace(reg, key === "www" ? infoDate[key] : zero(infoDate[key]))
        }
    }
    return formateStr
}

/**
 * @name obj2Form
 */
function obj2Form(obj) {
    const form = new FormData()
    for (let key in obj) {
        form.append(key, obj[key])
    }
    return form
}
