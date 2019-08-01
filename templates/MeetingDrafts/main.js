const axiosIns = axios.create({
    baseURL:window.baseURL,
    timeout: 1000
})

const vm = new Vue({
    el: "#app",

    data: () => ({
        list: []
    }),

    methods: {
        getList() {
            axiosIns.get("/Meeting/drafts").then(res => {
                if (res.data.code === 200) {
                    this.list = res.data.data
                }
            })
        },

        goback() {
            window.history.back()
        },

        navToRecord(id) {
            window.location.href = "../RecordBookUpdate/index.html?id=" + id
        }
    },

    mounted() {
        this.getList()
    }
})
