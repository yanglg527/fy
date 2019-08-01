"use strict";

var axiosIns = axios.create({
    baseURL: window.baseURL,
    timeout: 1000
});

var vm = new Vue({
    el: "#app",

    data: function data() {
        return {
            list: []
        };
    },

    methods: {
        getList: function getList() {
            var _this = this;

            var _getQuery = getQuery(),
                id = _getQuery.id;

            axiosIns.get("Meeting/detail", { params: { id: id } }).then(function (res) {
                if (res.data.code === 200 && res.data.data.mailbox) {
                    console.log("list", res);
                    _this.list = res.data.data.mailbox.map(function (v) {
                        return {
                            id: v.uid,
                            name: v.realname,
                            date: v.date,
                            styleAvatar: "backgroundImage: url(\"" + v.headimgurl + "\")",
                            content: v.content
                        };
                    });
                }
            });
        },
        goback: function goback() {
            window.history.back();
        }
    },

    mounted: function mounted() {
        this.getList();
    }
});