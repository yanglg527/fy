const axiosIns = axios.create({
    baseURL: window.baseURL,
    timeout: 1000
})

const vm = new Vue({
    el: "#app",

    data: () => ({
        detail: null,
        showCommentbox: false,
        commentText: "",
        hasMail: false
    }),

    computed: {
        collectionClass() {
            if (!this.detail) {
                return "bottom-item collection"
            }
            return this.detail["is_collection"] === "1" ? "bottom-item is-collection" : "bottom-item collection"
        },

        likeClass() {
            if (!this.detail) {
                return "bottom-item like"
            }
            return this.detail["is_like"] === "1" ? "bottom-item is-like" : "bottom-item like"
        }
    },

    methods: {
        getDetail() {
            const { id } = getQuery()
            axiosIns.get("Meeting/detail", { params: { id } }).then(res => {
                if (res.data.code === 200) {
                    this.detail = resultFormate(res.data.data)
                    if (res.data.data.mailbox && !!res.data.data.mailbox.length) {
                        this.hasMail = true
                    }
                }
            })
        },

        onCommentToggle(boolean) {
            this.showCommentbox = boolean
        },

        onCommentSubmit() {
            if (!this.commentText.trim()) {
                alert("内容不能为空")
                return
            }
            this.showCommentbox = false

            const form = new FormData()
            form.append("id", this.detail.id)
            form.append("comment", this.commentText)
            axiosIns
                .post("/Meeting/addComment", form, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                })
                .then(res => {
                    if (res.code === 200) {
                        alert("评论已提交，等待审核")
                        this.goback()
                    }
                })
        },

        // 点击收藏或赞
        onLikeOrCollection(type) {
            const key = `is_${type}`
            const url = type === "like" ? "/Meeting/likeAction" : "/Meeting/collectionAction"
            const newStatus = this.detail[key] === "1" ? "0" : "1"
            const form = new FormData()
            form.append("id", this.detail.id)
            form.append(key, newStatus)

            axiosIns.post(url, form, { headers: { "Content-Type": "multipart/form-data" } }).then(res => {
                if (res.data.code === 200) {
                    this.detail[key] = newStatus
                }
            })
        },

        gotoMailList() {
            const { id } = getQuery()
            window.location.href = "../MailList/index.html?id=" + id
        },

        goback() {
            window.history.back()
        }
    },

    mounted() {
        this.getDetail()
    }
})

function resultFormate(data) {
    return {
        ...data,
        attend: data.cx && data.cx.map(v => ({ ...v, img: v.img ? `url("${v.img}")` : "" })),
        sitIn: data.lx && data.lx.map(v => ({ ...v, img: v.img ? `url("${v.img}")` : "" })),
        leave: data.qj && data.qj.map(v => ({ ...v, img: v.img ? `url("${v.img}")` : "" })),
        absent: data.qx && data.qx.map(v => ({ ...v, img: v.img ? `url("${v.img}")` : "" })),
        comment:
            data.comment_list &&
            data.comment_list.map(v => ({ ...v, date: dateFormate(Number(v.create_at) * 1000, "yyy-mmm-ddd hh:mm:ss"), img: `url("${v.headimgurl}")` })),
        files: data.files && data.files.map(v => ({ ...v, className: typeToClass(v.files_path), downloadPath:  v.files_path_yulan }))
    }
}
