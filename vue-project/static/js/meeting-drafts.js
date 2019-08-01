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

            axiosIns.get("/Meeting/drafts").then(function (res) {
                if (res.data.code === 200) {
                    _this.list = res.data.data;
                }
            });
        },
        goback: function goback() {
            window.history.back();
        },
        navToRecord: function navToRecord(id) {
            window.location.href = "../RecordBookUpdate/index.html?id=" + id;
        }
    },

    mounted: function mounted() {
        this.getList();
    }
});