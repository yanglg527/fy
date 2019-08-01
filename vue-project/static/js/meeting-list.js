"use strict";

var axiosIns = axios.create({
    baseURL: window.baseURL,
    timeout: 1000
});

var vm = new Vue({
    el: "#app",

    data: {
        tabKey: "m",
        tabs: [{ key: "m", text: "本月台账" }, { key: "y", text: "本年台账" }, { key: "r", text: "笔记排名" }],
        keyword: "",

        mList: [], //月台账
        yList: [], //年台账
        rList: [], //排名

        mPage: 1,
        yPage: 1,
        rPage: 1,

        isLoading: false,

        scrollTimer: null,

        isEditable: false,
        showAddLayer: false
    },

    computed: {
        showingList: function showingList() {
            return this[this.tabKey + "List"];
        }
    },

    methods: {
        getList: function getList() {
            var _this = this;

            var url = "/Meeting/index";
            axiosIns.get(url, { params: { keyword: this.keyword, type: this.tabKey, p: 1 } }).then(function (res) {
                if (res.data.code === 200) {
                    console.log("list", res);
                    _this[_this.tabKey + "List"] = resultFormate(res.data.data.meetinglist);
                } else if (res.data.code === 404) {
                    _this[_this.tabKey + "List"] = [];
                }
                _this.isEditable = res.data.data.allowcreate === 1;
            });
        },
        getMore: function getMore() {
            var _this2 = this;

            if (this.isLoading) {
                return;
            }

            this.isLoading = true;

            var url = "/Meeting/index";
            axiosIns.get(url, { params: { keyword: this.keyword, type: this.tabKey, p: this[this.tabKey + "Page"] + 1 } }).then(function (res) {
                if (res.data.code === 200) {
                    _this2[_this2.tabKey + "List"] = _this2[_this2.tabKey + "List"].concat(resultFormate(res.data.data.meetinglist));
                    _this2[_this2.tabKey + "Page"]++;
                }
            }).finally(function () {
                _this2.isLoading = false;
            });
        },
        onTab: function onTab(key) {
            this.tabKey = key;

            if (this[key + "List"].length === 0) {
                this.getList();
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


        // 点击收藏或赞
        onLikeOrCollection: function onLikeOrCollection(id, type) {
            var item = this[this.tabKey + "List"].find(function (v) {
                return v.id === id;
            });
            if (!item) {
                return;
            }

            var url = type === "like" ? "Meeting/likeAction" : "Meeting/collectionAction";
            var form = new FormData();
            var formKey = "is_" + type;
            var statusKey = type === "like" ? "isLike" : "isCollection";
            form.append("id", id);
            form.append(formKey, !item[statusKey] ? "1" : "0");

            axiosIns.post(url, form, { headers: { "Content-Type": "multipart/form-data" } }).then(function (res) {
                if (res.data.code === 200) {
                    item[statusKey] = !item[statusKey];
                    item[type] += item[statusKey] ? 1 : -1;
                }
            });
        },
        onAddToggle: function onAddToggle(boolean) {
            this.showAddLayer = boolean;
        },
        onNavToDetail: function onNavToDetail(id) {
            window.location.href = "../MeetingDetail/index.html?id=" + id;
        },
        goback: function goback() {
            window.history.back();
        }
    },

    mounted: function mounted() {
        this.getList();
    }
});

// 将后台的数据转化为前端需要的数据
function resultFormate(list) {
    if (!list || list.length === 0) {
        return;
    }

    return list.map(function (item) {
        var classes = ["type one", "type two", "type three", "type four"];
        return {
            id: item.id,
            date: item.create_at,
            type: item.meeting_type,
            typeClass: classes[item.meeting_type_id],
            img: "url('../../static/images/meeting-default-image.png')",
            avatar: "url(" + item.img + ")",
            title: item.theme,
            author: item.realname,
            branch: item.branch_name,
            read: item.pageviews,
            collection: item.collection,
            comment: item.comment,
            like: item.likenum,
            isCollection: item.is_collection === "1",
            isLike: item.is_like === "1",
            likeId: item.like_id, // 用于更新点赞
            collectionId: item.collection_id // 用于更新收藏
        };
    });
}