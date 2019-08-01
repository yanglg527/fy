const axiosIns = axios.create({
    baseURL: window.baseURL,
    timeout: 1000,
    headers: { "Content-Type": "multipart/form-data" }
})

// 按钮状态
const btnStatus = [
    { text: "已结束", className: "right grey" },
    { text: "我要报名", className: "right red" },
    { text: "待开始", className: "right purple" },
    { text: "我要报名", className: "right red" },
    { text: "已报名", className: "right orange" }
]

const vm = new Vue({
    el: "#app",

    data: {
        userEnrollList: [],
        detailId: null,
        detail: null
    },

    computed: {
        btnObj() {
            // 底部按钮状态
            if (!this.detail) {
                return btnStatus[0]
            }

            const isEnrolled = this.userEnrollList.findIndex(v => v === this.detailId) !== -1
            if (isEnrolled) {
                return btnStatus[4]
            } else if (this.detail.status === "3") {
                return btnStatus[3]
            } else {
                return btnStatus[this.detail.status - 1]
            }
        },

        userList() {
            if (!this.detail || !this.detail.users.length) {
                return []
            }

            return this.detail.users.map(v => ({ ...v, img: `url("${v.img}")` }))
        }
    },

    methods: {
        getUserEnrollInfo() {
            axiosIns.get("Volunteer/volunteerBaseInfo").then(res => {
                if (res.data.code === 200) {
                    this.userEnrollList = res.data.data.lists || []
                }
                this.getDetail()
            })
        },

        getDetail() {
            const id = this.detailId

            axiosIns.get("Volunteer/detail", { params: { id } }).then(res => {
                if (res.data.code === 200) {
                    this.detail = res.data.data
                }
            })
        },

        onEnroll(typeText) {
            const id = this.detailId

            if (typeText === "我要报名") {
                console.log("我要报名", id)
                const form = obj2Form({ id })
                axiosIns.post("Volunteer/registratioEntrance", form).then(res => {
                    if (res.data.code === 200) {
                        this.userEnrollList.push(id)
                    }
                })
            }
        },

        goback() {
            window.history.back()
        }
    },

    mounted() {
        this.detailId = getQuery().id
        this.getUserEnrollInfo()
    }
})
