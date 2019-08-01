const axiosIns = axios.create({
    baseURL: window.baseURL,
    timeout: 1000
})

const vm = new Vue({
    el: "#app",

    data: () => ({
        list: []
    }),

    methods: {
        getList() {
            const { id } = getQuery()
            axiosIns.get("Meeting/detail", { params: { id } }).then(res => {
                if (res.data.code === 200 && res.data.data.mailbox) {
                    console.log("list", res)
                    this.list = res.data.data.mailbox.map(v => ({
                        id: v.uid,
                        name: v.realname,
                        date: v.date,
                        styleAvatar: `backgroundImage: url("${v.headimgurl}")`,
                        content: v.content
                    }))
                }
            })
        },

        goback() {
            window.history.back()
        }
    },

    mounted() {
        this.getList()
    }
})
