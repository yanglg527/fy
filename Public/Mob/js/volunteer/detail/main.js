const axiosIns = axios.create({
    baseURL: window.baseURL,
    timeout: 1000,
    headers: { "Content-Type": "multipart/form-data" }
})

const vm = new Vue({
    el: "#app",

    data: {
        detail: null
    },

    computed: {
        userList() {
            if (!this.detail || !this.detail.users.length) {
                return []
            }

            return this.detail.users.map(v => ({ ...v, img: `url("${v.img}")` }))
        }
    },

    methods: {
        getDetail(id) {
            axiosIns.get("Volunteer/detail", { params: { id } }).then(res => {
                console.log("detail", res)
                if (res.data.code === 200) {
                    this.detail = res.data.data
                }
            })
        },

        goback() {
            window.history.back()
        }
    },

    mounted() {
        const { id } = getQuery()
        this.getDetail(id)
    }
})
