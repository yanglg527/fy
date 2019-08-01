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
            absent: false
        },
        formId: "",
        form: {
            meetingType: {}, // 会议类型
            organization: "", // 党组织
            date: "2018-12-10", // 召开日期
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
            files: [] // 附件
        },

        meetingTypes: [],
        presenters: [],
        recorders: [],
        attends: [],
        sitIns: [],
        leaves: [],
        absents: [],

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
                    this.presenters = res.data.data.users
                    this.recorders = res.data.data.users
                    this.attends = res.data.data.users
                    this.leaves = res.data.data.users
                    this.leaves = res.data.data.users
                    this.absents = res.data.data.users

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
                    this.form.presenter = this.presenters.find(v => v.uid == res.data.data.presenter)
                    this.form.recorder = this.recorders.find(v => v.uid == res.data.data.recorder)

                    // 多选项匹配
                    this.form.attend = ids2List(res.data.data.attend, this.attends)
                    this.form.leave = ids2List(res.data.data.leave, this.leaves)
                    this.form.absent = ids2List(res.data.data.absent, this.absents)

                    // 列席人员单独处理
                    this.sitIns = res.data.data.init_sitIn
                    this.form.sitIn = ids2List(res.data.data.sitIn, res.data.data.init_sitIn)

                    // 附件
                    this.form.files = res.data.data.files.map(v => ({ id: v.files_id, url: v.files_path, name: v.former_name, display: true }))
                }
            })
        },

        onShowChange(key, boolean) {
            this.show[key] = boolean
        },

        onRadioChange(key, value) {
            this.form[key] = value
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

        list2Name(list) {
            return list.map(v => v.realname).join(",")
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
            const params = {
                ...this.form,
                meetingType: this.form.meetingType.id,
                organization: this.form.organization.id,
                presenter: this.form.presenter.uid,
                recorder: this.form.recorder.uid,
                attend: this.form.attend.map(v => v.uid),
                sitIn: this.form.sitIn.map(v => v.uid),
                leave: this.form.leave.map(v => v.uid),
                absent: this.form.absent.map(v => v.uid),
                files: this.form.files.filter(v => v.display).map(v => v.id),
                deleteFiles: this.form.files.filter(v => !v.display).map(v => v.id),
                acticle: this.editor.txt.html(),
                saveType: type
            }
            const form = new FormData()
            for (const key in params) {
                form.append(key, params[key])
            }
            form.append("id", this.formId)

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
            let tempItem = options.find(v => v.uid == id)
            tempItem && arr.push(tempItem)
        }
    }
    return arr
}
