/**
 * @desc
 * 列表项有两个部分信息，基本信息和与用户相关的状态
 * 关键是与用户相关的部分，需要通过匹配当前用户的已报名服务列表与服务列表
 * 计算出每一项服务对应的状态
 */

const axiosIns = axios.create({
    baseURL: window.baseURL,
    timeout: 1000,
    headers: { "Content-Type": "multipart/form-data" }
})

// 服务单项按钮状态
const itemStatus = [
    { text: "已结束", className: "btn grey" },
    { text: "进行中", className: "btn yellow" },
    { text: "待开始", className: "btn purple" },
    { text: "报名", className: "btn red" },
    { text: "已报名", className: "btn orange" }
]

const vm = new Vue({
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
        tabsWithCount() {
            return [
                { key: "all", text: `全部(${this.all})` },
                { key: "ing", text: `进行中(${this.ingTotal})` },
                { key: "in", text: `已参与(${this.inTotal})` },
                { key: "end", text: `已结束(${this.endTotal})` }
            ]
        },

        showList() {
            // 根据用户已报名列表和服务列表计算每个服务单项的状态
            const currList = this[`${this.tabKey}List`]
            return currList.map(v => {
                const isEnrolled = this.userEnrollList.findIndex(id => id === v.id) !== -1
                if (isEnrolled) {
                    return { ...v, btnStatus: itemStatus[4] }
                } else if (v.status === "3") {
                    return { ...v, btnStatus: itemStatus[3] }
                } else {
                    return { ...v, btnStatus: itemStatus[v.status - 1] }
                }
            })
        }
    },

    methods: {
        getUserEnrollInfo() {
            axiosIns.get("Volunteer/volunteerBaseInfo").then(res => {
                console.log("info", res)
                if (res.data.code === 200) {
                    this.all = res.data.data.all
                    this.ingTotal = res.data.data.ingTotal
                    this.inTotal = res.data.data.inTotal
                    this.endTotal = res.data.data.endTotal
                    this.userEnrollList = res.data.data.lists || []
                }
                this.getList()
            })
        },

        getList() {
            if (this.isLoading) {
                return
            }

            this.isLoading = true

            const url = "Volunteer/index"
            axiosIns
                .get(url, { params: { type: this.tabKey, page: 1 } })
                .then(res => {
                    if (res.data.code === 200) {
                        console.log("list", res)
                        this[`${this.tabKey}List`] = res.data.data
                    }
                })
                .finally(() => {
                    this.isLoading = false
                })
        },

        getMore() {
            if (this.isLoading || this[`${this.tabKey}NoMore`]) {
                return
            }

            this.isLoading = true

            const url = "Volunteer/index"
            axiosIns
                .get(url, { params: { type: this.tabKey, page: this[this.tabKey + "Page"] + 1 } })
                .then(res => {
                    if (res.data.code === 200) {
                        this[`${this.tabKey}List`] = this[`${this.tabKey}List`].concat(res.data.data)
                        this[`${this.tabKey}Page`]++
                    } else if (res.data.code === 404) {
                        this[`${this.tabKey}NoMore`] = true
                    }
                })
                .finally(() => {
                    this.isLoading = false
                })
        },

        onTab(key) {
            this.tabKey = key

            if (!this[`${this.tabKey}List`].length) {
                this.getList()
            }
        },

        onEnroll(typeText, id) {
            if (typeText === "报名") {
                console.log("报名", id)
                const form = obj2Form({ id })
                axiosIns.post("Volunteer/registratioEntrance", form).then(res => {
                    if (res.data.code === 200) {
                        this.userEnrollList.push(id)
                    }
                })
            }
        },

        onScroll(e) {
            var offsetHeight = e.target.offsetHeight
            var contentHeight = e.target.scrollHeight
            var maxScrollTop = contentHeight - offsetHeight
            var scrollTop = e.target.scrollTop

            if (maxScrollTop - scrollTop < 100) {
                var self = this
                self.scrollTimer && clearTimeout(self.scrollTimer)
                self.scrollTimer = setTimeout(function() {
                    self.getMore()
                }, 500)
            }
        },

        onNavToDetail(id) {
            window.location.href = `../VolunteerDetail/index.html?id=${id}`
        },

        goback() {
            window.history.back()
        }
    },

    mounted() {
        this.getUserEnrollInfo()
    }
})
