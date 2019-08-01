"use strict";

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

/**
 * @desc
 * 列表项有两个部分信息，基本信息和与用户相关的状态
 * 关键是与用户相关的部分，需要通过匹配当前用户的已报名服务列表与服务列表
 * 计算出每一项服务对应的状态
 */

var axiosIns = axios.create({
    baseURL: window.baseURL,
    timeout: 1000,
    headers: { "Content-Type": "multipart/form-data" }
});

// 服务单项按钮状态
var itemStatus = [{ text: "已结束", className: "btn grey" }, { text: "进行中", className: "btn yellow" }, { text: "待开始", className: "btn purple" }, { text: "报名", className: "btn red" }, { text: "已报名", className: "btn orange" }];

var vm = new Vue({
    el: "#app",

    data: {
        tabKey: "all",

        // 标签上的数量
        all: 0,
        ingTotal: 0,
        inTotal: 0,
        endTotal: 0,

        allList: [],
        ingList: [],
        inList: [],
        endList: [],

        userEnrollList: [],

        allPage: 1,
        ingPage: 1,
        inPage: 1,
        endPage: 1,

        allNoMore: false,
        ingNoMore: false,
        inNoMore: false,
        endNoMore: false,

        isLoading: false,

        scrollTimer: null
    },

    computed: {
        tabsWithCount: function tabsWithCount() {
            return [{ key: "all", text: "\u5168\u90E8(" + this.all + ")" }, { key: "ing", text: "\u8FDB\u884C\u4E2D(" + this.ingTotal + ")" }, { key: "in", text: "\u5DF2\u53C2\u4E0E(" + this.inTotal + ")" }, { key: "end", text: "\u5DF2\u7ED3\u675F(" + this.endTotal + ")" }];
        },
        showList: function showList() {
            var _this = this;

            // 根据用户已报名列表和服务列表计算每个服务单项的状态
            var currList = this[this.tabKey + "List"];
            return currList.map(function (v) {
                var isEnrolled = _this.userEnrollList.findIndex(function (id) {
                    return id === v.id;
                }) !== -1;
                if (isEnrolled) {
                    return _extends({}, v, { btnStatus: itemStatus[4] });
                } else if (v.status === "3") {
                    return _extends({}, v, { btnStatus: itemStatus[3] });
                } else {
                    return _extends({}, v, { btnStatus: itemStatus[v.status - 1] });
                }
            });
        }
    },

    methods: {
        getUserEnrollInfo: function getUserEnrollInfo() {
            var _this2 = this;

            axiosIns.get("Volunteer/volunteerBaseInfo").then(function (res) {
                console.log("info", res);
                if (res.data.code === 200) {
                    _this2.all = res.data.data.all;
                    _this2.ingTotal = res.data.data.ingTotal;
                    _this2.inTotal = res.data.data.inTotal;
                    _this2.endTotal = res.data.data.endTotal;
                    _this2.userEnrollList = res.data.data.lists || [];
                }
                _this2.getList();
            });
        },
        getList: function getList() {
            var _this3 = this;

            if (this.isLoading) {
                return;
            }

            this.isLoading = true;

            var url = "Volunteer/index";
            axiosIns.get(url, { params: { type: this.tabKey, page: 1 } }).then(function (res) {
                if (res.data.code === 200) {
                    console.log("list", res);
                    _this3[_this3.tabKey + "List"] = res.data.data;
                }
            }).finally(function () {
                _this3.isLoading = false;
            });
        },
        getMore: function getMore() {
            var _this4 = this;

            if (this.isLoading || this[this.tabKey + "NoMore"]) {
                return;
            }

            this.isLoading = true;

            var url = "Volunteer/index";
            axiosIns.get(url, { params: { type: this.tabKey, page: this[this.tabKey + "Page"] + 1 } }).then(function (res) {
                if (res.data.code === 200) {
                    _this4[_this4.tabKey + "List"] = _this4[_this4.tabKey + "List"].concat(res.data.data);
                    _this4[_this4.tabKey + "Page"]++;
                } else if (res.data.code === 404) {
                    _this4[_this4.tabKey + "NoMore"] = true;
                }
            }).finally(function () {
                _this4.isLoading = false;
            });
        },
        onTab: function onTab(key) {
            this.tabKey = key;

            if (!this[this.tabKey + "List"].length) {
                this.getList();
            }
        },
        onEnroll: function onEnroll(typeText, id) {
            var _this5 = this;

            if (typeText === "报名") {
                console.log("报名", id);
                var form = obj2Form({ id: id });
                axiosIns.post("Volunteer/registratioEntrance", form).then(function (res) {
                    if (res.data.code === 200) {
                        _this5.userEnrollList.push(id);
                    }
                });
            }
        },
        onScroll: function onScroll(e) {
            var offsetHeight = e.target.offsetHeight;
            var contentHeight = e.target.scrollHeight;
            var maxScrollTop = contentHeight - offsetHeight;
            var scrollTop = e.target.scrollTop;

            if (maxScrollTop - scrollTop < 100) {
                var self = this;
                self.scrollTimer && clearTimeout(self.scrollTimer);
                self.scrollTimer = setTimeout(function () {
                    self.getMore();
                }, 500);
            }
        },
        onNavToDetail: function onNavToDetail(id) {
            window.location.href = "../VolunteerDetail/index.html?id=" + id;
        },
        goback: function goback() {
            window.history.back();
        }
    },

    mounted: function mounted() {
        this.getUserEnrollInfo();
    }
});