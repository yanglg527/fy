"use strict";

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

var axiosIns = axios.create({
    baseURL: window.baseURL,
    timeout: 1000,
    headers: { "Content-Type": "multipart/form-data" }
});

// 按钮状态
var btnStatus = [{ text: "已结束", className: "right grey" }, { text: "我要报名", className: "right red" }, { text: "待开始", className: "right purple" }, { text: "我要报名", className: "right red" }, { text: "已报名", className: "right orange" }];

var vm = new Vue({
    el: "#app",

    data: {
        userEnrollList: [],
        detailId: null,
        detail: null
    },

    computed: {
        btnObj: function btnObj() {
            var _this = this;

            // 底部按钮状态
            if (!this.detail) {
                return btnStatus[0];
            }

            var isEnrolled = this.userEnrollList.findIndex(function (v) {
                return v === _this.detailId;
            }) !== -1;
            if (isEnrolled) {
                return btnStatus[4];
            } else if (this.detail.status === "3") {
                return btnStatus[3];
            } else {
                return btnStatus[this.detail.status - 1];
            }
        },
        userList: function userList() {
            if (!this.detail || !this.detail.users.length) {
                return [];
            }

            return this.detail.users.map(function (v) {
                return _extends({}, v, { img: "url(\"" + v.img + "\")" });
            });
        }
    },

    methods: {
        getUserEnrollInfo: function getUserEnrollInfo() {
            var _this2 = this;

            axiosIns.get("Volunteer/volunteerBaseInfo").then(function (res) {
                if (res.data.code === 200) {
                    _this2.userEnrollList = res.data.data.lists || [];
                }
                _this2.getDetail();
            });
        },
        getDetail: function getDetail() {
            var _this3 = this;

            var id = this.detailId;

            axiosIns.get("Volunteer/detail", { params: { id: id } }).then(function (res) {
                if (res.data.code === 200) {
                    _this3.detail = res.data.data;
                }
            });
        },
        onEnroll: function onEnroll(typeText) {
            var _this4 = this;

            var id = this.detailId;

            if (typeText === "我要报名") {
                console.log("我要报名", id);
                var form = obj2Form({ id: id });
                axiosIns.post("Volunteer/registratioEntrance", form).then(function (res) {
                    if (res.data.code === 200) {
                        _this4.userEnrollList.push(id);
                    }
                });
            }
        },
        goback: function goback() {
            window.history.back();
        }
    },

    mounted: function mounted() {
        this.detailId = getQuery().id;
        this.getUserEnrollInfo();
    }
});