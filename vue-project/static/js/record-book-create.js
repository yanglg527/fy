"use strict";

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

var axiosIns = axios.create({
    baseURL: window.baseURL,
    timeout: 1000,
    headers: { "Content-Type": "multipart/form-data" }
});

var vm = new Vue({
    el: "#app",

    data: {
        show: {
            meetingType: false,
            organization: false,
            presenter: false,
            recorder: false,
            attend: false,
            sitIn: false,
            leave: false,
            absent: false,
            task: false
        },
        form: {
            meetingType: "", // 会议类型
            organization: "", // 党组织
            date: dateFormate(null, "yyy-mmm-ddd"), // 召开日期
            address: "", // 召开地址
            groupName: "", // 党小组名称
            theme: "", // 会议主题
            presenter: "", // 主持人
            recorder: "", // 记录人
            issue: "", // 会议议题
            attend: [], // 出席人
            sitIn: [], // 列席人
            leave: [], // 请假人
            absent: [], //缺席人
            task: "",
            files: [] // 附件
        },

        groupUsers: [],
        meetingTypes: [],
        sitIns: [],
        tasks: [],

        keyword: "",
        isSearchFocus: false
    },

    computed: {
        dateText: function dateText() {
            return this.form.date ? this.form.date : "未选择日期";
        },
        fileList: function fileList() {
            if (!this.form.files.length) {
                return false;
            }
            return this.form.files.filter(function (v) {
                return v.display;
            }).map(function (v) {
                return _extends({}, v, { className: typeToClass(v.name) });
            });
        },
        searchClass: function searchClass() {
            // 判断搜索框是否添加 focus 类名
            return this.keyword.trim() ? "g-search-box-small focus" : this.isSearchFocus ? "g-search-box-small focus" : "g-search-box-small";
        },
        sitInList: function sitInList() {
            var _this = this;

            if (!this.keyword.trim()) {
                return this.sitIns;
            }

            return this.sitIns.filter(function (v) {
                return v.realname.indexOf(_this.keyword) !== -1;
            });
        }
    },

    methods: {
        initEditor: function initEditor() {
            var E = window.wangEditor;
            this.editor = new E("#editor");
            this.editor.customConfig.menus = ["head", "bold", "italic", "underline", "image"];

            this.editor.customConfig.uploadImgServer = "http://fy.dev.com/api/upload/byFormData";
            this.editor.customConfig.uploadFileName = "file";
            this.editor.customConfig.uploadImgHooks = {
                customInsert: function customInsert(insertImg, result) {
                    insertImg("http://" + result.data.url);
                }
            };
            this.editor.create();
        },
        getInitData: function getInitData() {
            var _this2 = this;

            axiosIns.get("/Meeting/initMeetingData").then(function (res) {
                console.log("init data", res.data.data.branchs);
                if (res.data.code === 200) {
                    // 党组织不能修改，只需要显示
                    _this2.form.organization = res.data.data.branchs;
                    _this2.meetingTypes = res.data.data.meetingtype.map(function (v, i) {
                        return { id: i, text: v };
                    });
                    _this2.groupUsers = res.data.data.users.map(function (v) {
                        return { id: v.uid, name: v.realname, belong: null };
                    });

                    _this2.tasks = res.data.data.tasks.map(function (v) {
                        return { id: v.id, text: v.tasks_title };
                    });
                }
            });
        },
        getUsers: function getUsers() {
            var _this3 = this;

            if (this.sitIns.length) {
                return;
            }
            axiosIns.get("/Meeting/getAllUser").then(function (res) {
                console.log("users", res);
                if (res.data.code === 200) {
                    _this3.sitIns = res.data.data.map(function (v) {
                        return { id: v.uid, name: v.realname };
                    });
                }
            });
        },
        onShowChange: function onShowChange(key, boolean) {
            this.show[key] = boolean;
            // 列席人员列表太大，触发时再请求数据
            if (key === "sitIn" && boolean) {
                this.getUsers();
            }
        },
        onRadioChange: function onRadioChange(key, value) {
            this.form[key] = value;
        },
        onGroupRadioClick: function onGroupRadioClick(group, option) {
            // 单选可以取消所选
            var isChecked = this.form[group] && this.form[group].id === option.id;
            this.form[group] = isChecked ? {} : option;

            var oldChecked = this.groupUsers.find(function (v) {
                return v.belong === group;
            });
            var newChecked = this.groupUsers.find(function (v) {
                return v.id === option.id;
            });

            if (oldChecked) {
                oldChecked.belong = null;
                if (oldChecked.id !== newChecked.id) {
                    newChecked.belong = group;
                }
            } else {
                newChecked.belong = group;
            }
        },
        checkItemActive: function checkItemActive(key, value) {
            return this.form[key].length ? this.form[key].indexOf(value) !== -1 : false;
        },
        onCheckChange: function onCheckChange(key, value) {
            var valueIndex = this.form[key].indexOf(value);
            if (valueIndex !== -1) {
                this.form[key].splice(valueIndex, 1);
            } else {
                this.form[key].push(value);
            }

            console.log("sit in", this.form[key]);
        },
        onGroupCheckClick: function onGroupCheckClick(group, option) {
            var optionIndex = this.form[group].findIndex(function (v) {
                return v.id === option.id;
            });
            if (optionIndex !== -1) {
                this.form[group].splice(optionIndex, 1);
            } else {
                this.form[group].push(option);
            }

            this.groupUsers = this.groupUsers.map(function (v) {
                if (v.id === option.id) {
                    return optionIndex !== -1 ? _extends({}, v, { belong: null }) : _extends({}, v, { belong: group });
                } else {
                    return v;
                }
            });
        },
        list2Name: function list2Name(list) {
            return list.map(function (v) {
                return v.name;
            }).join(",");
        },
        initFileWatcher: function initFileWatcher() {
            var _this4 = this;

            this.$refs.eleFile;
            addEventListener("change", function (e) {
                var file = _this4.$refs.eleFile.files[0];
                if (file) {
                    var form = new FormData();
                    form.append("file", file);
                    axiosIns.post("/Meeting/ajaxUploadAnnex", form).then(function (res) {
                        if (res.data.code === 200) {
                            _this4.form.files.push(_extends({}, res.data.data, { display: true }));
                            _this4.$refs.eleFile.value = null;
                        }
                    });
                }
            });
        },
        onRemoveFile: function onRemoveFile(index) {
            this.form.files[index].display = false;
        },
        onSubmit: function onSubmit(type) {
            var _this5 = this;

            if (!this.form.meetingType) {
                alert("未选择会议类型");
                return;
            }
            if (!this.form.date) {
                alert("未选择召开日期");
                return;
            }
            if (!this.form.address) {
                alert("未填写召开地点");
                return;
            }
            if (!this.form.theme) {
                alert("未填写会议主题");
                return;
            }
            if (!this.form.presenter.id) {
                alert("未选择主持人");
                return;
            }
            if (!this.form.recorder.id) {
                alert("未选择记录人");
                return;
            }
            if (!this.form.attend.length) {
                alert("未选择出席人员");
                return;
            }
            if (!this.form.issue) {
                alert("未填写会议议题");
                return;
            }
            if (this.editor.txt.html() === "<p><br></p>") {
                alert("未填写会议记录");
                return;
            }

            var params = _extends({}, this.form, {
                meetingType: this.form.meetingType.id,
                organization: this.form.organization.id,
                presenter: this.form.presenter.id,
                recorder: this.form.recorder.id,
                attend: this.form.attend.map(function (v) {
                    return v.id;
                }).concat([this.form.presenter.id, this.form.recorder.id]),
                acticle: this.editor.txt.html(),
                task: this.form.task.id,
                saveType: type

                // 可选
            });if (this.form.sitIn.length) {
                params.sitIn = this.form.sitIn.map(function (v) {
                    return v.id;
                });
            } else {
                delete params.sitIn;
            }
            if (this.form.leave.length) {
                params.leave = this.form.leave.map(function (v) {
                    return v.id;
                });
            } else {
                delete params.leave;
            }
            if (this.form.absent.length) {
                params.absent = this.form.absent.map(function (v) {
                    return v.id;
                });
            } else {
                delete params.absent;
            }
            if (this.form.files.length) {
                params.files = this.form.files.filter(function (v) {
                    return v.display;
                }).map(function (v) {
                    return v.id;
                });
            } else {
                delete params.files;
            }
            if (this.form.files.filter(function (v) {
                return !v.display;
            }).map(function (v) {
                return v.id;
            }).length) {
                params.deleteFiles = this.form.files.filter(function (v) {
                    return !v.display;
                }).map(function (v) {
                    return v.id;
                });
            } else {
                delete params.deleteFiles;
            }

            var form = new FormData();
            for (var key in params) {
                form.append(key, params[key]);
            }

            console.log("form", params);

            // return

            axiosIns.post("/Meeting/ajaxSave", form).then(function (res) {
                console.log("res", res);
                if (res.data.code === 200) {
                    alert("提交成功");
                    _this5.goback();
                }
            });
        },


        // 监听 搜索框 聚焦状态
        onSearchStatus: function onSearchStatus(boolean) {
            this.isSearchFocus = boolean;
        },
        goback: function goback() {
            window.history.back();
        }
    },

    mounted: function mounted() {
        this.initEditor();
        this.initFileWatcher();
        this.getInitData();
    }
});