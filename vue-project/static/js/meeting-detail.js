"use strict";

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

var axiosIns = axios.create({
    baseURL: window.baseURL,
    timeout: 1000
});

var vm = new Vue({
    el: "#app",

    data: function data() {
        return {
            detail: null,
            showCommentbox: false,
            commentText: "",
            hasMail: false
        };
    },

    computed: {
        collectionClass: function collectionClass() {
            if (!this.detail) {
                return "bottom-item collection";
            }
            return this.detail["is_collection"] === "1" ? "bottom-item is-collection" : "bottom-item collection";
        },
        likeClass: function likeClass() {
            if (!this.detail) {
                return "bottom-item like";
            }
            return this.detail["is_like"] === "1" ? "bottom-item is-like" : "bottom-item like";
        }
    },

    methods: {
        getDetail: function getDetail() {
            var _this = this;

            var _getQuery = getQuery(),
                id = _getQuery.id;

            axiosIns.get("Meeting/detail", { params: { id: id } }).then(function (res) {
                if (res.data.code === 200) {
                    _this.detail = resultFormate(res.data.data);
                    if (res.data.data.mailbox && !!res.data.data.mailbox.length) {
                        _this.hasMail = true;
                    }
                }
            });
        },
        onCommentToggle: function onCommentToggle(boolean) {
            this.showCommentbox = boolean;
        },
        onCommentSubmit: function onCommentSubmit() {
            var _this2 = this;

            if (!this.commentText.trim()) {
                alert("内容不能为空");
                return;
            }
            this.showCommentbox = false;

            var form = new FormData();
            form.append("id", this.detail.id);
            form.append("comment", this.commentText);
            axiosIns.post("/Meeting/addComment", form, {
                headers: {
                    "Content-Type": "multipart/form-data"
                }
            }).then(function (res) {
                if (res.code === 200) {
                    alert("评论已提交，等待审核");
                    _this2.goback();
                }
            });
        },


        // 点击收藏或赞
        onLikeOrCollection: function onLikeOrCollection(type) {
            var _this3 = this;

            var key = "is_" + type;
            var url = type === "like" ? "/Meeting/likeAction" : "/Meeting/collectionAction";
            var newStatus = this.detail[key] === "1" ? "0" : "1";
            var form = new FormData();
            form.append("id", this.detail.id);
            form.append(key, newStatus);

            axiosIns.post(url, form, { headers: { "Content-Type": "multipart/form-data" } }).then(function (res) {
                if (res.data.code === 200) {
                    _this3.detail[key] = newStatus;
                }
            });
        },
        gotoMailList: function gotoMailList() {
            var _getQuery2 = getQuery(),
                id = _getQuery2.id;

            window.location.href = "../MailList/index.html?id=" + id;
        },
        goback: function goback() {
            window.history.back();
        }
    },

    mounted: function mounted() {
        this.getDetail();
    }
});

function resultFormate(data) {
    return _extends({}, data, {
        attend: data.cx && data.cx.map(function (v) {
            return _extends({}, v, { img: v.img ? "url(\"" + v.img + "\")" : "" });
        }),
        sitIn: data.lx && data.lx.map(function (v) {
            return _extends({}, v, { img: v.img ? "url(\"" + v.img + "\")" : "" });
        }),
        leave: data.qj && data.qj.map(function (v) {
            return _extends({}, v, { img: v.img ? "url(\"" + v.img + "\")" : "" });
        }),
        absent: data.qx && data.qx.map(function (v) {
            return _extends({}, v, { img: v.img ? "url(\"" + v.img + "\")" : "" });
        }),
        comment: data.comment_list && data.comment_list.map(function (v) {
            return _extends({}, v, { date: dateFormate(Number(v.create_at) * 1000, "yyy-mmm-ddd hh:mm:ss"), img: "url(\"" + v.headimgurl + "\")" });
        }),
        files: data.files && data.files.map(function (v) {
            return _extends({}, v, { className: typeToClass(v.files_path), downloadPath: v.files_path_yulan });
        })
    });
}