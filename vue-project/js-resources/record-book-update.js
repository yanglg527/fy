const axiosIns = axios.create({
    baseURL: window.baseURL,
    timeout: 1000,
    headers: { "Content-Type": "multipart/form-data" }
})

const vm = new Vue({
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
        dateText() {
            return this.form.date ? this.form.date : "未选择日期"
        },

        fileList() {
            if (!this.form.files.length) {
                return false
            }
            return this.form.files.filter(v => v.display).map(v => ({ ...v, className: typeToClass(v.name) }))
        },

        searchClass() {
            // 判断搜索框是否添加 focus 类名
            return this.keyword.trim() ? "g-search-box-small focus" : this.isSearchFocus ? "g-search-box-small focus" : "g-search-box-small"
        },

        sitInList() {
            if (!this.keyword.trim()) {
                return this.sitIns
            }

            return this.sitIns.filter(v => v.realname.indexOf(this.keyword) !== -1)
        }
    },

    methods: {
        initEditor() {
            const E = window.wangEditor
            this.editor = new E("#editor")
            this.editor.customConfig.menus = ["head", "bold", "italic", "underline", "image"]

            this.editor.customConfig.uploadImgServer = window.baseURL + "/upload/byFormData"
            this.editor.customConfig.uploadFileName = "file"
            this.editor.customConfig.uploadImgHooks = {
                customInsert: function(insertImg, result) {
                    insertImg("http://" + result.data.url)
                }
            }
            this.editor.create()
        },

        getInitData() {
            // 初始化选项
            axiosIns.get("/Meeting/initMeetingData").then(res => {
                console.log("init data", res.data.data.branchs)
                if (res.data.code === 200) {
                    // 党组织不能修改，只需要显示
                    this.form.organization = res.data.data.branchs
                    this.meetingTypes = res.data.data.meetingtype.map((v, i) => ({ id: i, text: v }))
                    this.groupUsers = res.data.data.users.map(v => ({ id: v.uid, name: v.realname, belong: null }))
                    this.tasks = res.data.data.tasks.map(v => ({ id: v.id, text: v.tasks_title }))

                    this.getInitForm()
                }
            })
        },

        getInitForm() {
            // 获取当前记录的原始表单
            axiosIns.get("/Meeting/initEditData", { params: { id: this.formId } }).then(res => {
                console.log("form", res.data)
                if (res.data.code === 200) {
                    this.form.address = res.data.data.address
                    this.form.theme = res.data.data.theme
                    this.form.issue = res.data.data.issue
                    this.form.groupName = res.data.data.groupName
                    this.editor.txt.html(res.data.data.acticle)

                    // 单选项匹配
                    this.form.meetingType = this.meetingTypes.find(v => v.id == res.data.data.meetingType)

                    const presenter = this.groupUsers.find(v => v.id === res.data.data.presenter)
                    if (presenter) {
                        this.form.presenter = presenter
                        presenter.belong = "presenter"
                    }

                    const recorder = this.groupUsers.find(v => v.id === res.data.data.recorder)
                    if (recorder) {
                        this.form.recorder = recorder
                        recorder.belong = "recorder"
                    }

                    // 出席
                    this.groupUsers = this.groupUsers.map(user => {
                        if (user.belong) {
                            return user
                        }
                        const isIn = res.data.data.attend && res.data.data.attend.findIndex(id => id === user.id) !== -1
                        if (isIn) {
                            const item = { ...user, belong: "attend" }
                            this.form.attend.push(item)
                            return item
                        } else {
                            return user
                        }
                    })
                    // 请假
                    this.groupUsers = this.groupUsers.map(user => {
                        if (user.belong) {
                            return user
                        }
                        const isIn = res.data.data.leave && res.data.data.leave.findIndex(id => id === user.id) !== -1
                        if (isIn) {
                            const item = { ...user, belong: "leave" }
                            this.form.leave.push(item)
                            return item
                        } else {
                            return user
                        }
                    })
                    // 缺席
                    this.groupUsers = this.groupUsers.map(user => {
                        if (user.belong) {
                            return user
                        }
                        const isIn = res.data.data.absent && res.data.data.absent.findIndex(id => id === user.id) !== -1
                        if (isIn) {
                            const item = { ...user, belong: "absent" }
                            this.form.absent.push(item)
                            return item
                        } else {
                            return user
                        }
                    })

                    // 列席人员单独处理
                    this.sitIns = res.data.data.init_sitIn && res.data.data.init_sitIn.map(v => ({ id: v.uid, name: v.realname }))
                    this.form.sitIn = ids2List(res.data.data.sitIn, this.sitIns)

                    // 附件
                    this.form.files =
                        res.data.data.files && res.data.data.files.map(v => ({ id: v.files_id, url: v.files_path, name: v.former_name, display: true }))
                }
            })
        },

        onShowChange(key, boolean) {
            this.show[key] = boolean
        },

        onRadioChange(key, value) {
            this.form[key] = value
        },

        onGroupRadioClick(group, option) {
            // 单选可以取消所选
            const isChecked = this.form[group] && this.form[group].id === option.id
            this.form[group] = isChecked ? {} : option

            const oldChecked = this.groupUsers.find(v => v.belong === group)
            const newChecked = this.groupUsers.find(v => v.id === option.id)

            if (oldChecked) {
                oldChecked.belong = null
                if (oldChecked.id !== newChecked.id) {
                    newChecked.belong = group
                }
            } else {
                newChecked.belong = group
            }
        },

        checkItemActive(key, value) {
            return this.form[key].length ? this.form[key].indexOf(value) !== -1 : false
        },

        onCheckChange(key, value) {
            const valueIndex = this.form[key].indexOf(value)
            if (valueIndex !== -1) {
                this.form[key].splice(valueIndex, 1)
            } else {
                this.form[key].push(value)
            }
        },

        onGroupCheckClick(group, option) {
            const optionIndex = this.form[group].findIndex(v => v.id === option.id)
            if (optionIndex !== -1) {
                this.form[group].splice(optionIndex, 1)
            } else {
                this.form[group].push(option)
            }

            this.groupUsers = this.groupUsers.map(v => {
                if (v.id === option.id) {
                    return optionIndex !== -1 ? { ...v, belong: null } : { ...v, belong: group }
                } else {
                    return v
                }
            })
        },

        list2Name(list) {
            return list.map(v => v.name).join(",")
        },

        initFileWatcher() {
            this.$refs.eleFile
            addEventListener("change", e => {
                const file = this.$refs.eleFile.files[0]
                if (file) {
                    const form = new FormData()
                    form.append("file", file)
                    axiosIns.post("/Meeting/ajaxUploadAnnex", form).then(res => {
                        if (res.data.code === 200) {
                            this.form.files.push({ ...res.data.data, display: true })
                            this.$refs.eleFile.value = null
                        }
                    })
                }
            })
        },

        onRemoveFile(index) {
            this.form.files[index].display = false
        },

        onSubmit(type) {
            if (!this.form.meetingType) {
                alert("未选择会议类型")
                return
            }
            if (!this.form.date) {
                alert("未选择召开日期")
                return
            }
            if (!this.form.address) {
                alert("未填写召开地点")
                return
            }
            if (!this.form.theme) {
                alert("未填写会议主题")
                return
            }
            if (!this.form.presenter.id) {
                alert("未选择主持人")
                return
            }
            if (!this.form.recorder.id) {
                alert("未选择记录人")
                return
            }
            if (!this.form.attend.length) {
                alert("未选择出席人员")
                return
            }
            if (!this.form.issue) {
                alert("未填写会议议题")
                return
            }
            if (this.editor.txt.html() === "<p><br></p>") {
                alert("未填写会议记录")
                return
            }

            const params = {
                ...this.form,
                meetingType: this.form.meetingType.id,
                organization: this.form.organization.id,
                presenter: this.form.presenter.id,
                recorder: this.form.recorder.id,
                attend: this.form.attend.map(v => v.id).concat([this.form.presenter.id, this.form.recorder.id]),
                acticle: this.editor.txt.html(),
                task: this.form.task.id,
                saveType: type
            }

            // 可选
            if (this.form.sitIn.length) {
                params.sitIn = this.form.sitIn.map(v => v.id)
            } else {
                delete params.sitIn
            }
            if (this.form.leave.length) {
                params.leave = this.form.leave.map(v => v.id)
            } else {
                delete params.leave
            }
            if (this.form.absent.length) {
                params.absent = this.form.absent.map(v => v.id)
            } else {
                delete params.absent
            }
            if (this.form.files.length) {
                params.files = this.form.files.filter(v => v.display).map(v => v.id)
            } else {
                delete params.files
            }
            if (this.form.files.filter(v => !v.display).map(v => v.id).length) {
                params.deleteFiles = this.form.files.filter(v => !v.display).map(v => v.id)
            } else {
                delete params.deleteFiles
            }

            const form = new FormData()
            for (const key in params) {
                form.append(key, params[key])
            }
            form.append("id", this.formId)

            console.log("form", params)

            // return

            axiosIns.post("/Meeting/ajaxSave", form).then(res => {
                console.log("res", res)
                if (res.data.code === 200) {
                    alert("提交成功")
                    this.goback()
                }
            })
        },

        // 监听 搜索框 聚焦状态
        onSearchStatus(boolean) {
            this.isSearchFocus = boolean
        },

        goback() {
            window.history.back()
        }
    },

    mounted() {
        // 获取表单id
        this.formId = getQuery().id
        this.initEditor()
        this.initFileWatcher()
        this.getInitData()
    }
})

// 人员id列表转化为人员对象列表
function ids2List(ids, options) {
    const arr = []
    if (ids && ids.length) {
        for (let id of ids.values()) {
            let tempItem = options.find(v => v.id === id)
            tempItem && arr.push(tempItem)
        }
    }
    return arr
}
