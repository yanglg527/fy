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
        formId: "",
        form: {
            meetingType: {}, // 会议类型
            organization: "", // 党组织
            date: "", // 召开日期
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

        meetingTypes: [],
        groupUsers: [],
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

            this.editor.customConfig.uploadImgServer = window.baseURL + "/upload/byFormData";
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

            // 初始化选项
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

                    _this2.getInitForm();
                }
            });
        },
        getInitForm: function getInitForm() {
            var _this3 = this;

            // 获取当前记录的原始表单
            axiosIns.get("/Meeting/initEditData", { params: { id: this.formId } }).then(function (res) {
                console.log("form", res.data);
                if (res.data.code === 200) {
                    _this3.form.address = res.data.data.address;
                    _this3.form.theme = res.data.data.theme;
                    _this3.form.issue = res.data.data.issue;
                    _this3.form.groupName = res.data.data.groupName;
                    _this3.editor.txt.html(res.data.data.acticle);

                    // 单选项匹配
                    _this3.form.meetingType = _this3.meetingTypes.find(function (v) {
                        return v.id == res.data.data.meetingType;
                    });

                    var presenter = _this3.groupUsers.find(function (v) {
                        return v.id === res.data.data.presenter;
                    });
                    if (presenter) {
                        _this3.form.presenter = presenter;
                        presenter.belong = "presenter";
                    }

                    var recorder = _this3.groupUsers.find(function (v) {
                        return v.id === res.data.data.recorder;
                    });
                    if (recorder) {
                        _this3.form.recorder = recorder;
                        recorder.belong = "recorder";
                    }

                    // 出席
                    _this3.groupUsers = _this3.groupUsers.map(function (user) {
                        if (user.belong) {
                            return user;
                        }
                        var isIn = res.data.data.attend && res.data.data.attend.findIndex(function (id) {
                            return id === user.id;
                        }) !== -1;
                        if (isIn) {
                            var item = _extends({}, user, { belong: "attend" });
                            _this3.form.attend.push(item);
                            return item;
                        } else {
                            return user;
                        }
                    });
                    // 请假
                    _this3.groupUsers = _this3.groupUsers.map(function (user) {
                        if (user.belong) {
                            return user;
                        }
                        var isIn = res.data.data.leave && res.data.data.leave.findIndex(function (id) {
                            return id === user.id;
                        }) !== -1;
                        if (isIn) {
                            var item = _extends({}, user, { belong: "leave" });
                            _this3.form.leave.push(item);
                            return item;
                        } else {
                            return user;
                        }
                    });
                    // 缺席
                    _this3.groupUsers = _this3.groupUsers.map(function (user) {
                        if (user.belong) {
                            return user;
                        }
                        var isIn = res.data.data.absent && res.data.data.absent.findIndex(function (id) {
                            return id === user.id;
                        }) !== -1;
                        if (isIn) {
                            var item = _extends({}, user, { belong: "absent" });
                            _this3.form.absent.push(item);
                            return item;
                        } else {
                            return user;
                        }
                    });

                    // 列席人员单独处理
                    _this3.sitIns = res.data.data.init_sitIn && res.data.data.init_sitIn.map(function (v) {
                        return { id: v.uid, name: v.realname };
                    });
                    _this3.form.sitIn = ids2List(res.data.data.sitIn, _this3.sitIns);

                    // 附件
                    _this3.form.files = res.data.data.files && res.data.data.files.map(function (v) {
                        return { id: v.files_id, url: v.files_path, name: v.former_name, display: true };
                    });
                }
            });
        },
        onShowChange: function onShowChange(key, boolean) {
            this.show[key] = boolean;
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
            form.append("id", this.formId);

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
        // 获取表单id
        this.formId = getQuery().id;
        this.initEditor();
        this.initFileWatcher();
        this.getInitData();
    }
});

// 人员id列表转化为人员对象列表
function ids2List(ids, options) {
    var arr = [];
    if (ids && ids.length) {
        var _iteratorNormalCompletion = true;
        var _didIteratorError = false;
        var _iteratorError = undefined;

        try {
            var _loop = function _loop() {
                var id = _step.value;

                var tempItem = options.find(function (v) {
                    return v.id === id;
                });
                tempItem && arr.push(tempItem);
            };

            for (var _iterator = ids.values()[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                _loop();
            }
        } catch (err) {
            _didIteratorError = true;
            _iteratorError = err;
        } finally {
            try {
                if (!_iteratorNormalCompletion && _iterator.return) {
                    _iterator.return();
                }
            } finally {
                if (_didIteratorError) {
                    throw _iteratorError;
                }
            }
        }
    }
    return arr;
}